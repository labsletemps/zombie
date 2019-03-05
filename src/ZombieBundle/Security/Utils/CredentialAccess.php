<?php 

namespace ZombieBundle\Security\Utils;

use Seriel\AppliToolboxBundle\Managers\ManagersManager;
use ZombieBundle\Entity\Securite\IndividuProfil;
use ZombieBundle\Entity\Securite\ZombieProfilCredential;
use ZombieBundle\Entity\Securite\ZombieCredential;
use ZombieBundle\Managers\Securite\CredentialsManager;
use ZombieBundle\Entity\Entite\ZombieEntite;

class CredentialAccess {
	protected $profil_id;
	
	protected $structure_id;
	
	protected $credential_id;
	protected $credential_code;
	protected $credential_uid;
	
	protected $has_level = false;
	protected $level_profil = 0;
	protected $level_individu = 0;
	
	protected $has_choice = false;
	protected $choice = null;
	
	protected $entite_id;
	
	protected $_cache_credential = null;
	protected $_cache_entite = null;

	
	public function __construct(IndividuProfil $indivProf, ZombieProfilCredential $profCred) {
		$profil = $indivProf->getProfil();
		$credential = $profCred->getCredential();
		
		$this->profil_id = $profil->getId();
		
		$this->credential_id = $credential->getId();
		$this->credential_code = $credential->getCode();
		$this->credential_uid = $credential->getUID();
		

		
		if (!$credential->hasLevel()) {
			$this->has_level = false;
		} else {
			$this->has_level = true;
			
			// get level profil
			$this->level_profil = $profCred->getAccessLevel();

		}
		
		if (!$credential->hasChoices()) {
			$this->has_choice = false;
		} else {
			$this->has_choice = true;
			$this->choice = $profCred->getChoice();
		}
	}
	
	public function getProfilId() {
		return $this->profil_id;
	}
	public function getStructureId() {
		return $this->structure_id;
	}
	public function getCredentialId() {
		return $this->credential_id;
	}
	public function getCredentialCode() {
		return $this->credential_code;
	}
	public function getCredentialUid() {
		return $this->credential_uid;
	}
	public function getLevelProfil() {
		return $this->level_profil;
	}
	public function getLevelIndividu() {
		return $this->level_individu;
	}
	public function getEntiteId() {
		return $this->entite_id;
	}

	
	public function __toString() {
		return "CREDENTIAL_ACCESS >>> profil_id[".$this->profil_id."] credential[".$this->credential_uid."] level_profil[".$this->level_profil."] level_individu[".$this->level_individu."] entite[".$this->entite_id."] ";
	}
	
	protected function buildCache() {
		if ($this->credential_id) {
			$credsMgr = ManagersManager::getManager()->getContainer()->get('credentials_manager');
			if (false) $credsMgr = new CredentialsManager();
			
			$this->_cache_credential = $credsMgr->getCredential($this->credential_id);
		}
		if ($this->entite_id) {
			$entitesMgr = ManagersManager::getManager()->getContainer()->get('entites_manager');
			if (false) $entitesMgr = new EntitesManager();
			
			$this->_cache_e = $entitesMgr->getCompte($this->entite_id);
		}
	}
	
	protected function clearCache() {
		$this->_cache_credential = null;
		$this->_cache_entite = null;
	}
	
	public function hasDroit($obj = null, ZombieEntite $entite = null, $choice = null) {

		// Test choic
		if ($this->has_choice) {
			if ((!$this->choice) || (!$choice)) return false;
			if ($choice instanceof CredentialMultiChoice) {
				if ($choice->getId() != $this->choice->getId()) return false;
			} else if (is_string($choice)) {
				if (trim(strtoupper($choice)) != trim(strtoupper($this->choice->getCode()))) return false;
			} else {
				return false;
			}
		}
		
		if ($this->has_level == false) return true;

	
		if ((!$obj) && (!$entite)) {
			return true;
		}

		
		if ($this->level_profil == ZombieCredential::ACCESS_LEVEL_SELF) {
			// TODO : on doit tester le créateur de l'objet.
			return false;
		}

		
		if ($this->level_profil == ZombieCredential::ACCESS_LEVEL_COMPANY) {
			return true;
		}
		
		if ($this->level_profil == ZombieCredential::ACCESS_LEVEL_ENTITE) {

			
		}
		
		return false;
	}
}