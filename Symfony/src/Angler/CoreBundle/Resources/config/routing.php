<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();
$collection->add('AnglerCoreBundle_homepage', new Route('/hello/{name}', array(
    '_controller' => 'AnglerCoreBundle:Default:index',
)));

return $collection;
