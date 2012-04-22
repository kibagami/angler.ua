<?php

namespace Angler\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('AnglerBackendBundle:Default:index.html.twig', array('name' => $name));
    }
}
