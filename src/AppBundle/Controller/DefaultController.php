<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $tests = $em->getRepository('AppBundle\Entity\Test')->getRecentTests();

        return $this->render('default/index.html.twig', [
            'tests' => $tests,
        ]);
    }
}
