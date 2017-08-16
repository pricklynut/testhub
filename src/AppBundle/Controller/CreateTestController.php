<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Question;
use AppBundle\Entity\Test;
use AppBundle\Entity\Variant;
use AppBundle\Form\TestFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateTestController extends Controller
{
    /**
     * @Route("/test/new", name="test_new")
     */
    public function actionNew(Request $request)
    {
        $guestKey = $request->cookies->get('guest_key');
        $user = $this->get('user_service')->findByGuestKey($guestKey);

        /* if (empty($user)) {
            $user = $this->get('user_service')->createAndPersistUser();
            $redirect->headers->setCookie(
                new Cookie('guest_key', $user->getGuestKey(), time() + 3600*24*365)
            );
        } */

        $test = new Test();
        if (!empty($user)) {
            $test->assignAuthor($user);
        }
        $test->setCreated(new \DateTime());
        $test->setTimeLimit(0);

        $question = new Question();
        $question->setTest($test);
        $question->setSerialNumber(1);

        $variant = new Variant();
        $variant->setQuestion($question);

        return $this->createOrEdit($request, $test, 'create');
    }

    /**
     * @param Request $request
     * @param $testId
     *
     * @Route(
     *     "/test/{testId}/edit",
     *     name="test_edit",
     *     requirements={"testId": "\d+"}
     * )
     */
    public function actionEdit(Request $request, $testId)
    {
        $em = $this->getDoctrine()->getManager();
        $test = $em->find('AppBundle:Test', $testId);
        $this->checkNotFound($test);

        $guestKey = $request->cookies->get('guest_key');
        $user = $em->getRepository('AppBundle:User')->findOneBy(['guestKey' => $guestKey]);

        if ($user != $test->getAuthor()) {
            throw new AccessDeniedException("У вас нет прав на выполнение данного действия");
        }

        return $this->createOrEdit($request, $test, 'edit');
    }

    /**
     * @param $testId
     *
     * @Route(
     *     "test/{testId}/publish",
     *     name="test_publish",
     *     requirements={"testId": "\d+"}
     * )
     */
    public function actionPublish($testId)
    {
        // publish
    }

    /**
     * @param Request $request
     * @param string|null $testId
     * @param $action
     */
    private function createOrEdit(Request $request, Test $test, $action)
    {
        $form = $this->createForm(TestFormType::class, $test);

        $form->handleRequest($request);

        if ( $form->isSubmitted() and $form->isValid() ) {

            $test = $form->getData();
            $test->setShowAnswersString();
            $test->fixBrokenRelations();

            if (!$this->get('user_service')->hasGuestKey($request)) {
                $user = $this->get('user_service')->createAndPersistUser();
                $test->assignAuthor($user);
            }

            $this->getDoctrine()->getManager()->persist($test);
            $this->getDoctrine()->getManager()->flush();

            $redirect = new RedirectResponse(
                $this->generateUrl('test_edit', ['testId' => $test->getId()])
            );

            if (!$this->get('user_service')->hasGuestKey($request)) {
                $redirect->headers->setCookie(
                    new Cookie('guest_key', $user->getGuestKey(), time() + 3600*24*365)
                );
            }

            return $redirect;
        }

        return $this->render('create-test/new.html.twig', [
            'test' => $test,
            'form' => $form->createView(),
        ]);
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
