<?php

namespace Angler\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

	public function indexAction() {
		$data = array();

		return $this->render('AnglerBackendBundle:Default:index.html.php', $data);
	}

	public function loginAction() {
		return $this->render('AnglerBackendBundle:Default:login.html.php');
	}
}
