<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /* when calling route "/" this page will not be called
     * because of an entry in routing_dev.yml.
     * but if you change in the annotation below: 
     * name to name="_homepage" then
     * the page defined in routing_dev.yml
     * will lead to this controller and this page will be called
     */
      
    /**
     * @Route("/", name="_homepage_old")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }
}
