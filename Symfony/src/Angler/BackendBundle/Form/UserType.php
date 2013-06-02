<?php
namespace Angler\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType {

	/**
	 * Returns the name of this type.
	 *
	 * @return string The name of this type
	 */
	function getName() {
		return "user";
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('username', 'text')
			->add('password', 'repeated')
			->add('email', 'text')
			->add('roleObjects', 'collection', array(
				'type' => new RoleType(),
				'allow_add' => true,
				'allow_delete' => true,
				'prototype' => true,
			));
	}

	public function getDefaultOptions(array $options) {
		return array(
			'data_class' => 'Angler\UserBundle\Entity\User',
		);
	}

}
