<?php
namespace Angler\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class RoleType extends AbstractType {

	/**
	 * Returns the name of this type.
	 *
	 * @return string The name of this type
	 */
	function getName() {
		return "role";
	}

	public function buildForm(FormBuilder $builder, array $options) {
		$builder
			->add('name', 'text')
			->add('role', 'choice', array(
				'choices' => array(
					"Administrator",
					'Common User',
					'Anonymous User',
				),
		));
}

	public function getDefaultOptions(array $options) {
		return array(
			'data_class' => 'Angler\UserBundle\Entity\Role',
		);
	}

}
