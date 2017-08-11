<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Answer;
use AppBundle\Entity\Attempt;
use AppBundle\Entity\Question;
use AppBundle\Entity\Test;
use AppBundle\Entity\User;
use AppBundle\Form\NumberAnswerFormType;
use AppBundle\Form\VariantAnswerFormType;
use AppBundle\Form\StringAnswerFormType;
use AppBundle\Helper\HashGenerator;
use AppBundle\Helper\Pager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class TestsController extends Controller
{
    /**
     * @param Request $request
     * @Route("/tests", name="tests_list")
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $search = $request->query->get('search');

        $em = $this->getDoctrine()->getManager();
        $testRepo = $em->getRepository('AppBundle\Entity\Test');
        $pager = new Pager($page, $testRepo->getTotalCount($search));
        $tests = $testRepo->findByPage($page, $search, $pager->getPerPage());

        return $this->render('tests/list.html.twig', [
            'tests' => $tests,
            'pager' => $pager,
            'search' => $search,
        ]);
    }

    /**
     * @param $testId
     * @Route("/test/{testId}/preface", name="test_preface", requirements={"testId": "\d+"})
     */
    public function prefaceAction($testId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $testRepo = $em->getRepository('AppBundle\Entity\Test');
        $guestKey = $request->cookies->get('guest_key');
        $test = $em->find('AppBundle\Entity\Test', $testId);
        $questionsCount = $testRepo->getQuestionsCount($test->getId());
        $totalPoints = $testRepo->getTotalPoints($test->getId());

        $this->checkNotFound($test);

        $activeAttempt = null;

        if (!empty($guestKey)) {
            $user = $em->getRepository('AppBundle\Entity\User')
                ->findOneBy(['guestKey' => $guestKey]);
            $activeAttempt = $em->getRepository('AppBundle\Entity\Attempt')
                ->findActiveAttempt($user);
        }

        if (!empty($activeAttempt)) {
            $nextQuestionNumber = $em->getRepository('AppBundle:Attempt')
                ->getNextQuestionNumber($activeAttempt, 0);
        } else {
            $nextQuestionNumber = null;
        }

        return $this->render('tests/preface.html.twig', [
            'test' => $test,
            'activeAttempt' => $activeAttempt,
            'questionsCount' => $questionsCount,
            'totalPoints' => $totalPoints,
            'nextQuestionNumber' => $nextQuestionNumber,
        ]);
    }

    /**
     * @param $testId
     * @Route("/test/{testId}/start", name="start_test", requirements={"testId": "\d+"})
     */
    public function startAction($testId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $guestKey = $request->cookies->get('guest_key');
        $test = $em->find('AppBundle\Entity\Test', $testId);
        $this->checkNotFound($test);

        $response = new RedirectResponse(
            $this->generateUrl('test_question', ['testId' => $testId, 'serialNumber' => 1])
        );

        if (empty($guestKey)) {
            $user = $this->createAndPersistUser();
            $response->headers->setCookie(
                new Cookie('guest_key', $user->getGuestKey(), time() + 3600*24*365)
            );
        } else {
            $user = $em->getRepository('AppBundle\Entity\User')->findOneBy(['guestKey' => $guestKey]);
            $em->getRepository('AppBundle\Entity\Attempt')->finishActiveAttempts($user);
        }

        $this->createAndPersistAttempt($user, $test);
        $em->flush();

        return $response;
    }

    /**
     * @param $testId
     * @param $serialNumber
     * @Route(
     *     "/test/{testId}/question/{serialNumber}",
     *     name="test_question",
     *     requirements={"testId": "\d+", "serialNumber": "\d+"}
     * )
     */
    public function questionAction($testId, $serialNumber, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $guestKey = $request->cookies->get('guest_key');
        $user = $em->getRepository('AppBundle\Entity\User')
            ->findOneBy(['guestKey' => $guestKey]);

        if ( !$this->canUserPassTest(intval($testId), $user) ) {
            return $this->redirectToRoute('test_preface', ['testId' => $testId]);
        }

        $attemptRepo = $em->getRepository('AppBundle\Entity\Attempt');
        $attempt = $attemptRepo->findActiveAttempt($user);
        if ($attempt->timeIsUp()) {
            return $this->redirectToRoute('test_finish', ['testId' => $testId]);
        }

        $currentQuestion = $attemptRepo
            ->getCurrentQuestion(intval($testId), intval($serialNumber));
        $nextQuestionNumber = $attemptRepo
            ->getNextQuestionNumber($attempt, $serialNumber);

        $testRepo = $em->getRepository('AppBundle\Entity\Test');
        $questionsCount = $testRepo->getQuestionsCount(intval($testId));

        $answer = new Answer();

        $form = $this->createFormByQuestion($currentQuestion, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $attemptRepo->deletePreviousAnswer($attempt, $currentQuestion);
            $this->populateAndPersistAnswers($currentQuestion, $attempt, $form->getData());
            $em->flush();

            return $this->goToNextQuestionOrFinish($testId, $nextQuestionNumber);
        }

        return $this->render('tests/question.html.twig', [
            'currentQuestion' => $currentQuestion,
            'nextQuestionNumber' => $nextQuestionNumber,
            'attempt' => $attempt,
            'questionsCount' => $questionsCount,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $testId
     * @param Request $request
     * @Route(
     *     "/test/{testId}/finish",
     *     name="test_finish",
     *     requirements={"testId": "\d+"}
     * )
     */
    public function finishAction($testId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $guestKey = $request->cookies->get('guest_key');
        $user = $em->getRepository('AppBundle:User')
            ->findOneBy(['guestKey' => $guestKey]);

        $attemptRepo = $em->getRepository('AppBundle:Attempt');
        $attempt = $attemptRepo->findActiveAttempt($user);
        $nextQuestionNumber = $attemptRepo->getNextQuestionNumber($attempt);

        if (!$attemptRepo->hasUnansweredQuestions($attempt)) {
            $attempt->finish();
            $attempt->setFinished(new \DateTime());
            $em->flush();
            return $this->redirectToRoute('test_result', ['testId' => $testId]);
        }

        if ($attempt->timeIsUp()) {
            $attempt->failed();
            $attempt->setFinished(new \DateTime());
            $em->flush();
        }

        return $this->render('tests/finish.html.twig', [
            'attempt' => $attempt,
            'nextQuestionNumber' => $nextQuestionNumber,
        ]);
    }

    /**
     * @param $testId
     * @Route(
     *     "/test/{testId}/result",
     *     name="test_result",
     *     requirements={"testId": "\d+"}
     * )
     */
    public function resultAction($testId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $guestKey = $request->cookies->get('guest_key');
        $user = $em->getRepository('AppBundle:User')
            ->findOneBy(['guestKey' => $guestKey]);
        $test = $em->getRepository('AppBundle:Test')->find($testId);
        $attemptRepo = $em->getRepository('AppBundle:Attempt');
        $attempt = $attemptRepo->findAttemptByUserAndTest($user, $test);
        $attempt->setStatus(Attempt::STATUS_FINISHED);
        $em->flush();

        $points = 0;
        $rightAnswersCount = 0;

        $questions = $test->getQuestions();

        foreach ($questions as $question) {
            $correctVariants = $question->getCorrectVariants();
            $answers = $attemptRepo->getAnswersOnQuestion($attempt, $question);
            $correctVariantsArray = array_map(function ($v) {
                return $v->getAnswer();
            }, $correctVariants);
            $answersArray = array_map(function ($a) {
                return $a->getAnswer();
            }, $answers);
            if (count($correctVariantsArray) !== count($answersArray)) {
                continue;
            }
            if (empty(array_diff($correctVariantsArray, $answersArray))) {
                $points += $question->getPrice();
                $rightAnswersCount++;
            }
        }

        return $this->render('tests/result.html.twig', [
            'attempt' => $attempt,
            'points' => $points,
            'rightAnswersCount' => $rightAnswersCount,
        ]);
    }

    private function goToNextQuestionOrFinish(int $testId, int $nextQuestionNumber = null)
    {
        if ( empty($nextQuestionNumber) ) {
            return $this->redirectToRoute('test_finish', [
                'testId' => $testId,
            ]);
        } else {
            return $this->redirectToRoute('test_question', [
                'testId' => $testId,
                'serialNumber' => $nextQuestionNumber,
            ]);
        }
    }

    private function populateAndPersistAnswers($question, $attempt, $answersData)
    {
        switch ($question->getType()) {
            case Question::TYPE_NUMBER_TYPEIN:
            case Question::TYPE_STRING_TYPEIN:
                $answer = $answersData;
                $answer->setAttempt($attempt);
                $answer->setQuestion($question);
                $answer->setReceived(new \DateTime());
                $this->getDoctrine()->getManager()->persist($answer);
                break;
            case Question::TYPE_SINGLE_VARIANT:
                $model = new Answer();
                $model->setQuestion($question);
                $model->setAttempt($attempt);
                $model->setReceived(new \DateTime());
                $model->setAnswer($answersData['answer']);
                $this->getDoctrine()->getManager()->persist($model);
                break;
            case Question::TYPE_MULTIPLE_VARIANTS:
                foreach ($answersData['answer'] as $answerText) {
                    $answer = new Answer();
                    $answer->setQuestion($question);
                    $answer->setAttempt($attempt);
                    $answer->setReceived(new \DateTime());
                    $answer->setAnswer($answerText);
                    $this->getDoctrine()->getManager()->persist($answer);
                }
                break;
        }
    }

    /**
     * @param Question $question
     * @param Answer $answer
     * @return \Symfony\Component\Form\Form
     */
    private function createFormByQuestion(Question $question, Answer $answer)
    {
        $data = [
            'choices' => $question->getVariantsList(),
        ];
        $data['multiple'] = true;
        $data['expanded'] = true;

        switch ($question->getType()) {
            case Question::TYPE_NUMBER_TYPEIN:
                return $this->createForm(NumberAnswerFormType::class, $answer);
            case Question::TYPE_STRING_TYPEIN:
                return $this->createForm(StringAnswerFormType::class, $answer);
            case Question::TYPE_SINGLE_VARIANT:
                $data['multiple'] = false;
                return $this->createForm(VariantAnswerFormType::class, $data);
            case Question::TYPE_MULTIPLE_VARIANTS:
                return $this->createForm(VariantAnswerFormType::class, $data);
        }
    }

    /**
     * User can pass test in the following cases:
     * - if she has a guest_key cookie
     * - and if she has an active attempt
     * - and if the active attempt's test_id equals to the current test
     *
     * @param Request $request
     * @param $testId
     * @return bool
     */
    private function canUserPassTest(int $testId, User $user = null)
    {
        $em = $this->getDoctrine()->getManager();

        if (empty($user)) {
            return false;
        }

        $activeAttempt = $em->getRepository('AppBundle\Entity\Attempt')
            ->findActiveAttempt($user);

        if (
            empty($activeAttempt)
            or ($activeAttempt->getTest()->getId() !== $testId))
        {
            return false;
        }

        return true;
    }

    /**
     * @return User
     */
    private function createAndPersistUser()
    {
        $user = new User();
        $user->setGuestKey(HashGenerator::generateHash());
        $user->setRegistered(new \DateTime());
        $this->getDoctrine()->getManager()->persist($user);

        return $user;
    }

    /**
     * @param User $user
     * @param Test $test
     * @return Attempt
     */
    private function createAndPersistAttempt(User $user, Test $test)
    {
        $attempt = new Attempt();
        $attempt->setStatus(Attempt::STATUS_UNDERWAY);
        $attempt->setStarted(new \DateTime());
        $attempt->setUser($user);
        $attempt->setTest($test);
        $this->getDoctrine()->getManager()->persist($attempt);

        return $attempt;
    }

    /**
     * @param Test $test
     */
    private function checkNotFound(Test $test)
    {
        if (empty($test)) {
            throw $this->createNotFoundException('Тест не найден');
        }
    }

}
