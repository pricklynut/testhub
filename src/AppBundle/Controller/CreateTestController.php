<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Question;
use AppBundle\Entity\Tag;
use AppBundle\Entity\Test;
use AppBundle\Entity\Variant;
use AppBundle\Form\TestFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

        $test = new Test();
        if (!empty($user)) {
            $test->assignAuthor($user);
        }
        $test->setCreated(new \DateTime());
        $test->setTimeLimit(0);
        $test->setStatus(Test::STATUS_DRAFT);

        $tag = new Tag();
        $tag->attachToTest($test);

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
        $user = $this->get('user_service')->findByGuestKey($guestKey);

        $this->get('user_service')->checkIsUserAuthor($user, $test);

        return $this->createOrEdit($request, $test, 'edit');
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

            $guestKey = $request->cookies->get('guest_key');
            if (!$this->get('user_service')->hasGuestKey($guestKey)) {
                $user = $this->get('user_service')->createAndPersistUser();
                $test->assignAuthor($user);
            }

            $this->getDoctrine()->getManager()->persist($test);
            $this->getDoctrine()->getManager()->flush();

            $redirect = new RedirectResponse(
                $this->generateUrl('test_edit', ['testId' => $test->getId()])
            );

            if (!$this->get('user_service')->hasGuestKey($guestKey) and $action === 'create') {
                $redirect->headers->setCookie(
                    new Cookie('guest_key', $user->getGuestKey(), time() + 3600*24*365)
                );
            }

            $this->addFlash(
                'message',
                ($action === 'create') ? 'Тест успешно создан' : 'Изменения сохранены'
            );

            return $redirect;
        }

        $view = ($action === 'create')
            ? 'create-test/new.html.twig'
            : 'create-test/edit.html.twig';

        return $this->render($view, [
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
