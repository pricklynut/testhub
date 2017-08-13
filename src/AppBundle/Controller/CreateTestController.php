<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Question;
use AppBundle\Entity\Test;
use AppBundle\Entity\Variant;
use AppBundle\Form\TestFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        $question = new Question();
        $question->setTest($test);
        $variant = new Variant();
        $variant->setQuestion($question);

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
