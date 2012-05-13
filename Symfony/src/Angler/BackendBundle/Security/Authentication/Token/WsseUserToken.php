<?php
namespace Angler\BackendBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class WsseUserToken extends AbstractToken {

	public $created;
	public $digest;
	public $nonce;

	public function getCredentials() {
		return '';
	}
}
