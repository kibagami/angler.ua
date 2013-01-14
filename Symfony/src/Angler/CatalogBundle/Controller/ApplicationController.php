<?php

namespace Angler\CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ApplicationController extends Controller
{
    public function indexAction()
    {
        return $this->render('AnglerCatalogBundle:Application:index.html.php');
    }
}
