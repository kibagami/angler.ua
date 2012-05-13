<?php

namespace Angler\StoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('AnglerStoreBundle:Default:index.html.twig', array('name' => $name));
    }
}
