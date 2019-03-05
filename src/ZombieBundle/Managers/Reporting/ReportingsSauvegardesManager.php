<?php

namespace ZombieBundle\Managers\Reporting;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use ZombieBundle\Entity\Reporting\ReportingSauvegarde;

class ReportingsSauvegardesManager extends SerielManager
{
	protected function addSecurity($qb) {
		// No security here.
		return;
	}
	
	public function getObjectClass() {
		return 'ZombieBundle\Entity\Reporting\ReportingSauvegarde';
	}
	
	public function getAlias() {
		return 'rs';
	}
	
	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;
	
		if (isset($params['type']) && $params['type']) {
			$type = $params['type'];
			$qb->andWhere('rs.type = :type')->setParameter('type', $type);
			$hasParams = true;
		}
		
		if (isset($params['nom']) && $params['nom']) {
			$nom = $params['nom'];
			$qb->andWhere('rs.nom = :nom')->setParameter('nom', $nom);
			$hasParams = true;
		}
		
		if (isset($params['individu']) && $params['individu']) {
			$individu = $params['individu'];
			$qb->andWhere('rs.individu = :individu')->setParameter('individu', $individu);
			$hasParams = true;
		}
		
		if (isset($params['active']) && $params['active']) {
			$active= $params['active'];
			$qb->andWhere('rs.deleted <> :active')->setParameter('active', $active);
			$hasParams = true;
		}
	
		return $hasParams;
	}
	
	/**
	 * @return ReportingSauvegarde
	 */
	public function getReportingSauvegarde($id) {
		return $this->get($id);
	}
	
	/**
	 * @return ReportingSauvegarde[]
	 */
	public function getAllReportingsSauvegardesForTypeAndIndividu($type, $individu_id) {
		if ((!$type) || (!$individu_id)) return array();
		return $this->query(array('type' => $type, 'individu' => $individu_id, 'active' => true));
	}
	
	/**
	 * @return ReportingSauvegarde[]
	 */
	public function getReportingSauvegardeForTypeNomAndIndividu($type, $nom, $individu_id) {
		if ((!$type) || (!$nom) || (!$individu_id)) null;
		return $this->query(array('type' => $type, 'nom' => $nom, 'individu' => $individu_id), array('one' => true));
	}
	
	/**
	 * @return ReportingSauvegarde[]
	 */
	public function getAllReportingsSauvegardes($options = array()) {
		return $this->getAll($options);
	}
	
}

?>
