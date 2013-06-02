<?php
namespace Angler\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \Symfony\Component\Security\Core\User\AdvancedUserInterface;
use \Symfony\Component\Security\Core\User\UserInterface;
use \Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Angler\UserBundle\Entity\User
 *
 * @ORM\Table(name="angler_users")
 * @ORM\Entity(repositoryClass="Angler\UserBundle\Repository\UserRepository")
 */
class User implements AdvancedUserInterface {

	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=25, unique=true)
	 */
	private $username;

	/**
	 * @ORM\Column(type="string", length=32)
	 */
	private $salt;

	/**
	 * @ORM\Column(type="string", length=40)
	 */
	private $password;

	/**
	 * @ORM\Column(type="string", length=60, unique=true)
	 */
	private $email;

	/**
	 * @ORM\Column(name="is_active", type="boolean")
	 */
	private $isActive;

	/**
	 * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
	 *
	 */
	private $roles;

	public function __construct() {
		$this->isActive = true;
		$this->salt     = md5(uniqid(null, true));
		$this->roles   = new ArrayCollection();
	}

	/**
	 * @inheritDoc
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @inheritDoc
	 * @return string
	 */
	public function getSalt() {
		return $this->salt;
	}

	/**
	 * @inheritDoc
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @inheritDoc
	 * @return array
	 */
	public function getRoles() {
		$roles = array();
		/** @var $role Role */
		foreach ($this->roles as $role) {
			$roles[] = $role->getRole();
		}

		return $roles;
//		return $this->roles->toArray();
	}

	public function getRoleObjects() {
		return $this->roles;
	}

	/**
	 * @return void
	 */
	public function eraseCredentials() {
	}

	/**
	 * @inheritDoc
	 * @param \Symfony\Component\Security\Core\User\AdvancedUserInterface|\Symfony\Component\Security\Core\User\UserInterface $user
	 * @return bool
	 */
	public function equals(UserInterface $user) {
		return $this->username === $user->getUsername();
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
	 * Set username
	 *
	 * @param string $username
	 */
	public function setUsername($username) {
		$this->username = $username;
	}

	/**
	 * Set salt
	 *
	 * @param string $salt
	 */
	public function setSalt($salt) {
		$this->salt = $salt;
	}

	/**
	 * Set password
	 *
	 * @param string $password
	 */
	public function setPassword($password) {
		$password =

		$this->password = $password;
	}

	/**
	 * Set email
	 *
	 * @param string $email
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * Get email
	 *
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * Set isActive
	 *
	 * @param boolean $isActive
	 */
	public function setIsActive($isActive) {
		$this->isActive = $isActive;
	}

	/**
	 * Get isActive
	 *
	 * @return boolean
	 */
	public function getIsActive() {
		return $this->isActive;
	}

	/**
	 * Checks whether the user's account has expired.
	 *
	 * Internally, if this method returns false, the authentication system
	 * will throw an AccountExpiredException and prevent login.
	 *
	 * @return Boolean true if the user's account is non expired, false otherwise
	 *
	 * @see AccountExpiredException
	 */
	function isAccountNonExpired() {
		return true;
	}

	/**
	 * Checks whether the user is locked.
	 *
	 * Internally, if this method returns false, the authentication system
	 * will throw a LockedException and prevent login.
	 *
	 * @return Boolean true if the user is not locked, false otherwise
	 *
	 * @see LockedException
	 */
	function isAccountNonLocked() {
		return true;
	}

	/**
	 * Checks whether the user's credentials (password) has expired.
	 *
	 * Internally, if this method returns false, the authentication system
	 * will throw a CredentialsExpiredException and prevent login.
	 *
	 * @return Boolean true if the user's credentials are non expired, false otherwise
	 *
	 * @see CredentialsExpiredException
	 */
	function isCredentialsNonExpired() {
		return true;
	}

	/**
	 * Checks whether the user is enabled.
	 *
	 * Internally, if this method returns false, the authentication system
	 * will throw a DisabledException and prevent login.
	 *
	 * @return Boolean true if the user is enabled, false otherwise
	 *
	 * @see DisabledException
	 */
	function isEnabled() {
		return $this->isActive;
	}

	public function setRoleObjects($roles) {
		$this->roles = $roles;
	}

	/**
	 * @param $role Role
	 */
	public function addRoleObjects($role) {
		$role->addUser($this);
		$this->roles->add($role);
	}

	/**
	 * Add role
	 *
	 * @param \Angler\UserBundle\Entity\Role $role
	 */
    public function addRole(\Angler\UserBundle\Entity\Role $role)
    {
        $this->roles->add($role);
    }

	public static function loadValidatorMetadata(\Symfony\Component\Validator\Mapping\ClassMetadata $metadata) {
		$metadata->addPropertyConstraint('username', new Assert\MaxLength(array(
			'limit' => 12,
			'message' => 'This value is too long',
		)));
	}
}
