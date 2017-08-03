<?php

namespace AppBundle\Controller;

use AppBundle\Helper\Pager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
}
