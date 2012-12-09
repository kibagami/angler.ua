<?php

namespace Angler\UserBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="angler_roles")
 * @ORM\Entity()
 */
class Role implements RoleInterface {

	/**
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id()
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\Column(name="name", type="string", length=30)
	 */
	private $name;

	/**
	 * @ORM\Column(name="role", type="string", length=20, unique=true)
	 */
	private $role;

	/**
	 * @ORM\ManyToMany(targetEntity="User", mappedBy="groups")
	 */
	private $users;

	public function __construct() {
		$this->users = new ArrayCollection();
	}

// ... getters and setters for each property

	/**
	 * @see RoleInterface
	 */
	public function getRole() {
		return $this->role;
	}

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set role
	 *
	 * @param string $role
	 */
	public function setRole($role) {
		$this->role = $role;
	}

	/**
	 * Add users
	 *
	 * @param \Angler\UserBundle\Entity\User $user
	 */
	public function addUser(\Angler\UserBundle\Entity\User $user) {
		$this->users->add($user);
	}

	/**
	 * Get users
	 *
	 * @return \Angler\UserBundle\Entity\User[]
	 */
	public function getUsers() {
		return $this->users;
	}

	public static function loadValidatorMetadata(\Symfony\Component\Validator\Mapping\ClassMetadata $metadata) {
		echo('fff');
		$metadata->addPropertyConstraint('name', new Assert\MaxLength(array(
			'limit' => 12,
			'message' => 'This value is too long',
		)));
	}
}
