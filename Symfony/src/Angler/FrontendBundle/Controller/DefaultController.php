<?php

namespace Angler\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('AnglerFrontendBundle:Default:index.html.twig', array('name' => $name));
    }
}
