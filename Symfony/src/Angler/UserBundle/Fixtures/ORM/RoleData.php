<?php
namespace Angler\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use \Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Angler\UserBundle\Entity\Role;

class RoleData  extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface {
	private $container;

	private $roles = array(
		"Administrator" => "ROLE_ADMIN",
		"Common User" => "ROLE_USER",
		"Anonymous User" => "ROLE_ANONYMOUS",
	);

	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}
	/**
	 * Load data fixtures with the passed EntityManager
	 *
	 * @param Doctrine\Common\Persistence\ObjectManager $manager
	 */
	function load(ObjectManager $manager) {
		foreach($this->roles as $name => $role) {
			$target = new Role();
			$target->setName($name);
			$target->setRole($role);

			if($role == "ROLE_ADMIN") {
				/** @var $user \Angler\UserBundle\Entity\User */
				$user = $this->getReference('admin-user');
				$user->addRole($target);
				$target->addUser($user);
			}

			$manager->persist($target);
		}

		$manager->flush();
	}

	public function getOrder() {
		return 2;
	}
}
