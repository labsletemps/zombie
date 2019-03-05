<?php

namespace Seriel\DandelionBundle\Securite\Voters;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Seriel\UserBundle\Entity\User;
use Seriel\AppliToolboxBundle\Security\Voters\SerielEntityVoter;
use ZombieBundle\Managers\Securite\CredentialsManager;

class DandelionArticleSemanticsVoter extends SerielEntityVoter {
	
	public function getClass() {
		return 'Seriel\DandelionBundle\Entity\DandelionArticleSemantics';
	}
	public function getCredentials() {
		$credsMgr = $this->container->get('credentials_manager');
		if (false) $credsMgr = new CredentialsManager();
		
		return $credsMgr->getAllCredentialsForObject($this->getClass());
	}
	
	public function runVote(User $user, $obj, $attribute) {
		// TODO
		return VoterInterface::ACCESS_GRANTED;
	}
}