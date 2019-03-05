<?php

namespace ZombieBundle\Security\Voters;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use ZombieBundle\Managers\Securite\SecurityManager;
use ZombieBundle\Security\Utils\GestionnaireDeDroits;

class MainAccessVoter implements VoterInterface {
	
	protected $container = null;
	
	public function __construct($container) {
		$this->container = $container;
	}
	
	public function supportsAttribute($attribute) {
		return true;
	}
	
	public function supportsClass($class) {
		if ($class == null) return true;
		if ($class == 'Symfony\Component\HttpFoundation\Request') return true;
		
		return false;
	}
	
	public function vote(TokenInterface $token, $object, array $attributes) {
		if ($object != null && (!$object instanceof Request)) return VoterInterface::ACCESS_ABSTAIN;
		
		// OK let deal with this.
		if (1 !== count($attributes)) {
			throw new \InvalidArgumentException('Only one attribute is allowed for VIEW or EDIT');
		}
		
		// set the attribute to check against
		$attribute = $attributes[0];
		

		$securityMgr = $this->container->get('security_manager');
		if (false) $securityMgr = new SecurityManager();
		
		$gestionnaire_de_droits = $securityMgr->getCurrentCredentials();
		if (!$gestionnaire_de_droits) return VoterInterface::ACCESS_DENIED;
		
		if (false) $gestionnaire_de_droits = new GestionnaireDeDroits();
		
		if ($gestionnaire_de_droits->hasDroit($attribute)) {
			return VoterInterface::ACCESS_GRANTED;
		}
		
		return VoterInterface::ACCESS_DENIED;
		
	}
}