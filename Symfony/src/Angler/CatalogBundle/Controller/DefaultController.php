<?php

namespace Angler\CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AnglerCatalogBundle:Default:index.html.php');
    }
}
