<?php

namespace AutoRIABundle\Controller;

use AutoRIABundle\Form\Type\CreateSearchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $searchForm = $this->createForm(CreateSearchType::class);

        return $this->render('AutoRIABundle:Search:index.html.twig', array(
            'searchForm' => $searchForm->createView(),
        ));
    }
}
