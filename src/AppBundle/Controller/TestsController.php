<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Attempt;
use AppBundle\Entity\Test;
use AppBundle\Entity\User;
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

        return $this->render('tests/preface.html.twig', [
            'test' => $test,
            'activeAttempt' => $activeAttempt,
            'questionsCount' => $questionsCount,
            'totalPoints' => $totalPoints,
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
    public function questionAction($testId, $serialNumber)
    {
        var_dump($testId, $serialNumber);die;
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
