<?php

namespace Angler\UserBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Table(name="acme_groups")
* @ORM\Entity()
*/
class Group implements RoleInterface
{
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

public function __construct()
{
$this->users = new ArrayCollection();
}

// ... getters and setters for each property

/**
* @see RoleInterface
*/
public function getRole()
{
return $this->role;
}
}
