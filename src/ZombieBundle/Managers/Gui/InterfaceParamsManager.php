<?php

namespace ZombieBundle\Managers\Gui;

use Seriel\AppliToolboxBundle\Managers\SerielManager;

class InterfaceParamsManager extends SerielManager
{
	protected function addSecurity($qb) {
		// No security here.
		return;
	}
	
	public function getObjectClass() {
		return 'ZombieBundle\Entity\Gui\InterfaceParam';
	}
	
	public function getAlias() {
		return 'ip';
	}
	
	public function query($params, $options = array(), &$execvars = null) {
		if ((!isset($options['one'])) && (!isset($options['orderBy']))) {
			$options['orderBy'] = array('ip.pos' => 'asc');
		}
		return parent::query($params, $options);
	}
	
	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;
	
		if (isset($params['type']) && $params['type']) {
			$type = $params['type'];
			$qb->andWhere('ip.type = :type')->setParameter('type', $type);
			$hasParams = true;
		}
		
		if (isset($params['profil']) && $params['profil']) {
			$profil_id = $params['profil'];
			$qb->andWhere('ip.profil = :profil_id')->setParameter('profil_id', $profil_id);
			$hasParams = true;
		}

		if (isset($params['individu']) && $params['individu']) {
			$individu_id = $params['individu'];
			$qb->andWhere('ip.individu = :individu_id')->setParameter('individu_id', $individu_id);
			$hasParams = true;
		}
		
		return $hasParams;
	}
	
	/**
	 * @return InterfaceParam
	 */
	public function getInterfaceParam($id) {
		return $this->get($id);
	}
	
	/**
	 * @return InterfaceParam[]
	 */
	public function getAllInterfaceParamsForTypeAndProfil($type, $profil_id) {
		if (!$type) return array();
		if (!$profil_id) return array();
		
		$params = array('type' => $type, 'profil' => $profil_id);
		
		return $this->query($params);
	}
	
	/**
	 * @return InterfaceParam[]
	 */
	public function getAllInterfaceParamsForTypeAndIndividu($type, $individu_id) {
		if (!$type) return array();
		if (!$individu_id) return array();
		
		$params = array('type' => $type, 'individu' => $individu_id);
		
		return $this->query($params);
	}
	
	/**
	 * @return InterfaceParam[]
	 */
	public function getAllInterfaceParams($options = array()) {
		$options['orderBy'] = array('ip.pos' => 'asc');
		return $this->getAll($options);
	}
	
}

?>
