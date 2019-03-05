<?php

namespace ZombieBundle\Managers\Securite;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use ZombieBundle\Entity\Securite\ZombieCredential;

class CredentialsManager extends SerielManager
{
	protected function addSecurityFilters($qb, $individu) {
		// No security here. Required for security !
	}
	protected function addSecurity($qb) {
		// No security here. Required for security !
		return;
	}
	protected function isGrantedView($obj) {
		return true;
	}
	
	public function getObjectClass() {
		return 'ZombieBundle\Entity\Securite\ZombieCredential';
	}
	
	public function getAlias() {
		return 'credential';
	}
	
	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;
		
		if (isset($params['ids']) && $params['ids']) {
			$ids = $params['ids'];
		
			$qb->andwhere("credential.id in (:ids)")->setParameter('ids', $ids);
		
			$hasParams = true;
		}
		
		if (isset($params['code']) && $params['code']) {
			$code = $params['code'];
				
			$qb->andwhere("credential.code = :code")->setParameter('code', $code);
		
			$hasParams = true;
		}
	
		if (isset($params['object']) && $params['object']) {
			$object = $params['object'];
			
			$qb->andwhere("credential.object = :object")->setParameter('object', $object);
				
			$hasParams = true;
		}
		
		if (isset($params['is_root']) && $params['is_root']) {
			$qb->andWhere("credential.parent_credential is null");
			$hasParams = true;
		}
		
		if (isset($params['parent_id']) && $params['parent_id']) {
			$parent_id = $params['parent_id'];
			$qb->andWhere('credential.parent_credential = :parent_id')->setParameter('parent_id', $parent_id);
			
			$hasParams = true;
		}
	
		return $hasParams;
	}
	
	/**
	 * @return SsdCredential
	 */
	public function getCredential($id) {
		return $this->get($id);
	}
	
	/**
	 * @return SsdCredential[]
	 */
	public function getAllCredentialsForObject($obj) {
		$params = array('object' => $obj);
		
		return $this->query($params);
	}
	
	/**
	 * @return SsdCredential[]
	 */
	public function getAllCredentialsForListIds($ids) {
		if (!$ids) return array();
		
		return $this->query(array('ids' => $ids));
	}
	
	/**
	 * @return SsdCredential[]
	 */
	public function getAllCredentials() {
		return $this->getAll();
	}
	
	public function getAllCredentialsByEntity($root_only = true) {
		$entities = $this->getAllEntitiesRequiringCredentials();
	
		$credentials = $this->getAllCredentials();
		$credsByEntity = array();
		foreach ($credentials as $cred) {
			if (false) $cred = new ZombieCredential();
			
			if ($root_only && $cred->getParentCredential()) continue;
			if (!$cred->getObject()) continue;
			$entity = $cred->getObject();
			$code = $cred->getCode();
				
			if (!isset($credsByEntity[$entity])) $credsByEntity[$entity] = array();
			$credsByEntity[$entity][$code] = $cred;
		}
		foreach ($entities as $entity) {
			if (!isset($credsByEntity[$entity])) $credsByEntity[$entity] = array();
				
			if (!isset($credsByEntity[$entity]['view'])) {
				// Create object crédential.
				$cred = new ZombieCredential();
				$cred->setObject($entity);
				$cred->setCode('view');
				$cred->setName('Lecture');
				$cred->setShortName('Voir');
				$cred->setHasLevel(true);
	
				$this->save($cred, true);
	
				$credsByEntity[$entity]['view'] = $cred;
			}
				
			if (!isset($credsByEntity[$entity]['edit'])) {
				// Create object crédential.
				$cred = new ZombieCredential();
				$cred->setObject($entity);
				$cred->setCode('edit');
				$cred->setName('Ecriture');
				$cred->setShortName('Ecrire');
				$cred->setHasLevel(true);
					
				$this->save($cred, true);
					
				$credsByEntity[$entity]['edit'] = $cred;
			}
		}
	
		return $credsByEntity;
	}
	
	public function getAllEntitiesRequiringCredentials() {
	
		return array(

		);
	
	}
	
	public function getAllCredentialsByGroup($root_only = true) {
		$credentials = $this->getAllCredentials();
		$credsByGroup = array();
	
		foreach ($credentials as $cred) {
			if (false) $cred = new ZombieCredential();
				
			if ($root_only && $cred->getParentCredential()) continue;
			if ($cred->getObject() || (!$cred->getGroup())) continue;
			
			$group = $cred->getGroup();
			$code = $cred->getCode();
			
			if (!isset($credsByGroup[$group])) $credsByGroup[$group] = array();
			$credsByGroup[$group][$code] = $cred;
		}
	
		return $credsByGroup;
	}
	
}

?>
