<?php

namespace ZombieBundle\Security\Utils;

use ZombieBundle\Entity\Securite\ZombieCredential;

class ArrayCredentialAccess {
	
	protected $credential_id;
	protected $credential_code;
	protected $credential_uid;
	
	protected $has_level = false;
	
	protected $credential_accesses = array();
	
	public function __construct(ZombieCredential $credential) {
		$this->credential_id = $credential->getId();
		$this->credential_code = $credential->getCode();
		$this->credential_uid = $credential->getUID();
		
		$this->has_level = $credential->hasLevel();
	}
	
	public function addCredentialAccess(CredentialAccess $credAccess) {
		$this->credential_accesses[] = $credAccess;
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
	
	public function getCredentialAccesses() {
		return $this->credential_accesses;
	}
	
	public function hasDroit($obj = null, $entite = null, $choice = null) {
		if ($this->has_level == false) return true;
		
		if ((!$obj) && (!$entite)) {
			return true;
		}
		
		foreach ($this->credential_accesses as $credAccess) {
			if (false) $credAccess = new CredentialAccess();
			if ($credAccess->hasDroit($obj, $entite, $choice)) return true;
		}
		
		return false;
	}
	
	public function getAllLevels() {
		if ($this->has_level == false) return array();
		
		$res = array();
		
		
		foreach ($this->credential_accesses as $credAccess) {
			$level_profil = $credAccess->getLevelProfil();
			if ($level_profil == ZombieCredential::ACCESS_LEVEL_SELF) {
				$res['SELF'] = true;
			} else if ($level_profil == ZombieCredential::ACCESS_LEVEL_ENTITE) {
				$res['ENTITE'] = true;
			}else if ($level_profil == ZombieCredential::ACCESS_LEVEL_COMPANY) {
				$res['COMPANY'] = true;
			}
		}
		
		return $res;
	}
	
	public function getDebugStrs() {
		$str = "ARRAY_CREDENTIAL_ACCESS >>> credential[".$this->credential_uid."] has_level[".$this->has_level."]";
		
		$res = array();
		foreach ($this->credential_accesses as $ca) {
			$res[] = $str." ".$ca;
		}
		
		return $res;
	}
	
	public function __toString() {
		$str = "ARRAY_CREDENTIAL_ACCESS >>> credential[".$this->credential_uid."] has_level[".$this->has_level."]";
		
		$res = "";
		foreach ($this->credential_accesses as $ca) {
			$res .= $str." ".$ca."\n";
		}
		
		return $res;
	}
}