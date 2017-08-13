<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Question;
use AppBundle\Entity\Test;
use AppBundle\Entity\Variant;
use AppBundle\Form\TestFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

class CreateTestController extends Controller
{
    /**
     * @Route("/test/new", name="test_new")
     */
    public function actionNew(Request $request)
    {
        $guestKey = $request->cookies->get('guest_key');
        $user = $this->getDoctrine()->getManager()->getRepository("AppBundle:User")->findOneBy(['guestKey' => $guestKey]);

        $test = new Test();
        $test->assignAuthor($user);
        $test->setCreated(new \DateTime());
        $test->setTimeLimit(0);

        $question = new Question();
        $question->setTest($test);
        $question->setSerialNumber(1);

        $variant = new Variant();
        $variant->setQuestion($question);

        return $this->createOrEdit($request, $test);
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

        $guestKey = $request->cookies->get('guest_key');
        $user = $em->getRepository('AppBundle:User')->findOneBy(['guestKey' => $guestKey]);

        if ($user->getId() !== $test->getAuthor()->getId()) {
            throw new AccessDeniedException("У вас нет прав на выполнение данного действия");
        }

        return $this->createOrEdit($request, $test);
    }

    /**
     * @param Request $request
     * @param string|null $testId
     */
    private function createOrEdit(Request $request, Test $test)
    {
        $form = $this->createForm(TestFormType::class, $test);

        $form->handleRequest($request);

        if ( $form->isSubmitted() and $form->isValid() ) {
            $test = $form->getData();
            if ($test->getShowAnswers()) {
                $test->setShowAnswers("yes");
            } else {
                $test->setShowAnswers("no");
            }

            foreach ($test->getQuestions() as $question) {
                $question->setTest($test);
                foreach ($question->getVariants() as $variant) {
                    $variant->setQuestion($question);
                    if ($variant->getIsCorrect()) {
                        $variant->setIsCorrect("yes");
                    } else {
                        $variant->setIsCorrect("no");
                    }
                }
            }

            $this->getDoctrine()->getManager()->persist($test);
            $this->getDoctrine()->getManager()->flush();
            // todo: redirect to /test/{id}/edit
        }

        return $this->render('create-test/new.html.twig', [
            'test' => $test,
            'form' => $form->createView(),
        ]);
    }

}
