<?php

namespace ZombieBundle\Security\Utils;

use ZombieBundle\Managers\Securite\SecurityManager;
use ZombieBundle\Entity\Individu\Utilisateur;
use ZombieBundle\Entity\Securite\IndividuProfil;
use ZombieBundle\Entity\Securite\ZombieProfilCredential;
use ZombieBundle\Entity\Individu\Individu;

class GestionnaireDeDroits {
	
	protected $individu_id;
	
	protected $est_invite = false;
	
	protected $arrayCredsByCredentialUid;
	
	public function __construct($individu) {
		if (!$individu) return;
		
		$this->individu_id = $individu->getId();
		
		if ($individu instanceof Utilisateur) {
			$this->est_invite = false;
			$this->arrayCredsByCredentialUid[SecurityManager::ACCESS_USER] = true;

		} else {
			$this->est_invite = true;
			$this->arrayCredsByCredentialUid[SecurityManager::ACCESS_INVITE] = true;
		}
		
		$this->arrayCredsByCredentialUid = array();
		
		$individusProfils = $individu->getProfils();
		
		if ($individusProfils) {
			foreach ($individusProfils as $indivProf) {
				if (false) $indivProf = new IndividuProfil();
				$profil = $indivProf->getProfil();
				if ($profil) {
					// initialyse credentials.
					foreach ($profil->getProfilsCredentials() as $profCred) {
						if (false) $profCred = new ZombieProfilCredential();
						
						$this->addDroits($indivProf, $profCred);
					}
				}
			}				
		}
		
		$this->setAllAnyRightsOn();
	}
	
	protected function setAllAnyRightsOn() {
		if ($this->arrayCredsByCredentialUid) {
			foreach (array_keys($this->arrayCredsByCredentialUid) as $uid) {
				$this->arrayCredsByCredentialUid['ANY_RIGHT_ON['.$uid.']'] = true;
			}			
		}
	}
	
	public function getIndividuId() {
		return $this->individu_id;
	}
	
	public function isInvite() {
		return $this->est_invite;
	}
	
	public function addDroits(IndividuProfil $indivProf, ZombieProfilCredential $profCred) {
		
		$credential = $profCred->getCredential();
		
		if (!isset($this->arrayCredsByCredentialUid[$credential->getUID()])) {
			$arrCredAccess = new ArrayCredentialAccess($credential);
			$this->arrayCredsByCredentialUid[$credential->getUID()] = $arrCredAccess;
		}
		
		$credAccess = new CredentialAccess($indivProf, $profCred);
		
		$arrCredAccess = $this->arrayCredsByCredentialUid[$credential->getUID()];
		if (false) $arrCredAccess = new ArrayCredentialAccess();
		
		$arrCredAccess->addCredentialAccess($credAccess);
		
	}
	
	public function hasDroit($droit, $obj = null, $entite = null, $choice = null) {
		if (!$droit) return false;
		
		$splitted = explode('|', $droit);
		if (count($splitted) == 2) {
			$droit = trim($splitted[0]);
			$choice = trim($splitted[1]);
		}
		
		if (!isset($this->arrayCredsByCredentialUid[$droit])) return false;
		
		if ($this->arrayCredsByCredentialUid[$droit] === true) return true;
		
		$arrayCredAccess = $this->arrayCredsByCredentialUid[$droit];
		if (false) $arrayCredAccess = new ArrayCredentialAccess();
		
		return $arrayCredAccess->hasDroit($obj, $entite, $choice);

	}
	
	public function hasAnyLevelOnDroit($droit) {
		if (!$droit) return false;
		
		if (isset($this->arrayCredsByCredentialUid[$droit])) return true;
		
		return false;
	}
	
	public function getAllLevelsForDroit($droit) {
		
		if (!$droit) return array();
		if (isset($this->arrayCredsByCredentialUid[$droit]) && $this->arrayCredsByCredentialUid[$droit] instanceof ArrayCredentialAccess) {
			return $this->arrayCredsByCredentialUid[$droit]->getAllLevels();
		}
		
		return array();
	}
	
	public function __toString() {
		$str = "";
		foreach ($this->arrayCredsByCredentialUid as $uid => $aca) {
			$str .= "DROITS $uid >>> ".$aca."\n";
		}
		
		return $str;
	}
	
	public function getDebugStrs() {
		$res = array();
		
		if ($this->arrayCredsByCredentialUid) {
			foreach ($this->arrayCredsByCredentialUid as $uid => $aca) {
				if ($aca instanceof ArrayCredentialAccess) {
					$strs = $aca->getDebugStrs();
					foreach ($strs as $str) {
						$res[] = "DROITS $uid >>> ".$str;
					}
				} else {
					$res[] = "DROITS $uid >>> TRUE";
				}
			}	
		}
		
		return $res;
	}
}