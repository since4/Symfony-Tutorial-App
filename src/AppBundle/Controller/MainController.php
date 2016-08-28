<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class MainController extends Controller
{
    /* two routes lead to this controller
     * the annotated below and another defined
     * in app/config/routing_dev.yml 
     */
    
    /**
     * @Route("/main/" , name="app_main_homepage_")
     */
    public function homepageAction()
    {
        return $this->render('main/homepage.html.twig');
    }
}
