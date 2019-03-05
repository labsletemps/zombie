<?php

namespace ZombieBundle\Managers\Securite;

use ZombieBundle\Managers\ZombieManager;
use ZombieBundle\Entity\Individu\Individu;

class ZombieProfilManager extends ZombieManager
{
	
	protected function isGrantedFilter( $individu, $code) {

		if ($individu instanceof Individu) {

			// get entite.			
			$entite = $individu->getEntitePrincipale();
			if (!$entite) {
				return false;
			}
			
			$gestionnaire_de_droits = $this->container->get('security_manager')->getCurrentCredentials();
			if (!$gestionnaire_de_droits) {
				return false;
			}
			if (false) $gestionnaire_de_droits = new GestionnaireDeDroits($individu);
			
			switch ($code) {
				case 'view':
					$levels= $gestionnaire_de_droits->getAllLevelsForDroit('ZombieBundle\Entity\Securite\ZombieProfil >> view');
					if ((!$levels) || count($levels) == 0) {
						return false;
					}
					else {
						return true;
					}
				case 'edit':
					$levels = $gestionnaire_de_droits->getAllLevelsForDroit('ZombieBundle\Entity\Securite\ZombieProfil >> edit');
					if ((!$levels) || count($levels) == 0) {
						return false;
					}
					else {
						return true;
					}
				default:
					return true;
			}
			
			
		}
		
		// if user is not individu => block.
		return false;
		
	}
	public function getObjectClass() {
		return 'ZombieBundle\Entity\Securite\ZombieProfil';
	}
	
	protected function getAlias() {
		return 'profil';
	}
	
	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;	
		return $hasParams;
	}
	
	/**
	 * @return ZombieProfil
	 */
	public function getZombieProfil($id) {
		return $this->get($id);
	}
	
	/**
	 * @return ZombieProfil[]
	 */
	public function getAllZombieProfils($options = array()) {
		return $this->getAll($options);
	}
	
	public function save($obj, $flush = false) {
		if (!$obj) return false;
		
		if ($obj->getId()) {
			// it is update
			// create hash with credentials.
			$newHash = array();
			foreach ($obj->getProfilsCredentials() as $profCred) {
				if (false) $profCred = new ZombieProfilCredential();
				$newHash[$profCred->getCredential()->getId()] = $profCred;
			}
			
			$old_prod_cerds = $this->getDoctrineEM()->getRepository('ZombieBundle\Entity\Securite\ZombieProfilCredential')->findBy(array('profil' => $obj->getId()));
			// create hash with old credentials.
			$oldHash = array();
			if ($old_prod_cerds) {
				foreach ($old_prod_cerds as $profCred) {
					if (false) $profCred = new ZombieProfilCredential();
					$oldHash[$profCred->getCredential()->getId()] = $profCred;
				}
			}
			
			// TODO. do it better.
			foreach ($oldHash as $cred_id => $profCred) $this->remove($profCred, $flush);
		}
		
		parent::save($obj, $flush);
	}
}

?>
