<?php

namespace ZombieBundle\Managers\Entite;

use ZombieBundle\Entity\Entite\Societe;
use Seriel\AppliToolboxBundle\Managers\SerielManager;

class SocieteManager extends SerielManager
{
	protected function addSecurityFilters($qb, $individu) {
		// NOTHING TO DO THERE !
		return;
	}
	
	public function getObjectClass() {
		return 'ZombieBundle\Entity\Entite\Societe';
	}
	
	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;
	
		// this is never used
	
		return $hasParams;
	}
	
	/**
	 * @return Societe
	 */
	public function getSociete() {
		$societes = $this->getAll();
				
		foreach ($societes as $societe) {
			return $societe;
		}
		
		return null;
	}
}

?>
