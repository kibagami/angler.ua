<?php

namespace Angler\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

	public function indexAction() {
		$data = array();
		/** @var $em \Doctrine\ORM\EntityManager */
		$em   = $this->getDoctrine()->getEntityManager();
		$repo = $em->getRepository('Angler\UserBundle\Entity\User');

		$user = $repo->find(6);

		if (!$user) {
			$user = new \Angler\UserBundle\Entity\User();
		}

		$form = $this->createForm(new \Angler\BackendBundle\Form\UserType(), $user);

		if ($this->getRequest()->getMethod() == "POST") {
			$form->bindRequest($this->getRequest());
			if ($form->isValid()) {
				$this->redirect($this->generateUrl("AnglerBackendBundle_homepage"));
			}
		}

		$data['form'] = $form->createView();

		return $this->render('AnglerBackendBundle:Default:index.html.php', $data);
	}
}
