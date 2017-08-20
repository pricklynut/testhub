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
        $tagId = $request->query->get('tagId');

        $tests = $this->get('test_service')->findByTagId($tagId);
        if (empty($tests)) {
            $tests = $this->get('test_service')->findByPhrase($page, $search);
            $pager = $this->get('test_service')->createPagerForSearch($page, $search);
        } else {
            $pager = $this->get('test_service')->createPagerForTagSearch($page, $tagId);
        }

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
        $testService = $this->get('test_service');

        $test = $testService->findById(intval($testId));
        $this->checkNotFound($test);

        $questionsCount = $testService->getQuestionsCount($test->getId());
        $totalPoints = $testService->getTotalPoints($test->getId());

        $guestKey = $request->cookies->get('guest_key');
        $user = $this->get('user_service')->findByGuestKey($guestKey);
        $activeAttempt = $this->get('attempt_service')->findActiveAttempt($user);
        $nextQuestionNumber = $this->get('attempt_service')
            ->getNextQuestionNumber($activeAttempt, $curNumber = 0);

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
        $test = $this->get('test_service')->findById(intval($testId));
        $this->checkNotFound($test);

        $response = new RedirectResponse(
            $this->generateUrl(
                'test_question',
                ['testId' => $testId, 'serialNumber' => 1]
            )
        );

        $guestKey = $request->cookies->get('guest_key');
        $user = $this->get('user_service')->findByGuestKey($guestKey);

        if (empty($user)) {
            $user = $this->get('user_service')->createAndPersistUser();
            $response->headers->setCookie(
                new Cookie('guest_key', $user->getGuestKey(), time() + 3600*24*365)
            );
        } else {
            $this->get('attempt_service')->finishActiveAttempts($user);
        }

        $this->get('attempt_service')->createAndPersistAttempt($user, $test);
        $this->getDoctrine()->getManager()->flush();

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
        $test = $this->get('test_service')->findById(intval($testId));
        $this->checkNotFound($test);

        $guestKey = $request->cookies->get('guest_key');
        $user = $this->get('user_service')->findByGuestKey($guestKey);

        if ( !$this->get('user_service')->canUserPassTest($user, $test) ) {
            return $this->redirectToRoute('test_preface', ['testId' => $testId]);
        }

        if ($this->get('attempt_service')->timeIsUp($user, $test)) {
            return $this->redirectToRoute('test_finish', ['testId' => $testId]);
        }

        $attempt = $this->get('attempt_service')->findActiveAttemptByTest($user, $test);
        $currentQuestion = $this->get('attempt_service')
            ->getCurrentQuestion(intval($testId), intval($serialNumber));
        $nextQuestionNumber = $this->get('attempt_service')
            ->getNextQuestionNumber($attempt, $serialNumber);

        $questionsCount = $this->get('test_service')->getQuestionsCount(intval($testId));

        $answer = new Answer();

        $form = $this->createFormByQuestion($currentQuestion, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('attempt_service')->deletePreviousAnswer($attempt, $currentQuestion);
            $this->get('attempt_service')
                ->populateAndPersistAnswers($currentQuestion, $attempt, $form->getData());

            $this->getDoctrine()->getManager()->flush();

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
            $attempt->finish(new \DateTime());
            $em->flush();
            return $this->redirectToRoute('test_result', ['testId' => $testId]);
        }

        if ($attempt->timeIsUp()) {
            $attempt->failed(new \DateTime());
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
        $this->checkNotFound($test);

        $attemptRepo = $em->getRepository('AppBundle:Attempt');
        $attempt = $attemptRepo->findAttemptByUserAndTest($user, $test);
        $attempt->finish(new \DateTime());
        $em->flush();

        $result = $this->get('attempt_service')->getResult($test, $attempt);

        return $this->render('tests/result.html.twig', [
            'attempt' => $attempt,
            'result' => $result,
        ]);
    }

    /**
     * @param $testId
     *
     * @Route(
     *     "/test/{testId}/publish",
     *     name="test_publish",
     *     requirements={"testId": "\d+"}
     * )
     */
    public function publishAction($testId, Request $request)
    {
        $test = $this->get('test_service')->findById(intval($testId));

        $this->checkNotFound($test);

        $guestKey = $request->cookies->get('guest_key');
        $user = $this->get('user_service')->findByGuestKey($guestKey);
        $this->get('user_service')->checkIsUserAuthor($user, $test);

        $test->setStatus(Test::STATUS_PUBLISHED);
        $this->getDoctrine()->getManager()->flush();

        return $this->render('tests/publish.html.twig', ['test' => $test]);
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
     * @param Test $test
     */
    private function checkNotFound(Test $test)
    {
        if (empty($test)) {
            throw $this->createNotFoundException('Тест не найден');
        }
    }

}
