<?php

namespace Angler\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction()
    {
        return $this->render('AnglerBackendBundle:Default:index.html.php');
    }
}
