<?php
namespace Angler\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Angler\UserBundle\Entity\User;

class UserData  implements FixtureInterface, ContainerAwareInterface {
	private $container;

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
		$adminUser = new User();
		$adminUser->setUsername('admin');
		$adminUser->setEmail('admin@angler.ua');

		/** @var $encoder \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder  */
		$encoder = $this->container->get('security.encoder_factory')->getEncoder($adminUser);
		$adminUser->setPassword($encoder->encodePassword('gycve2d5dg7bax', $adminUser->getSalt()));

		$manager->persist($adminUser);
		$manager->flush();
	}
}
