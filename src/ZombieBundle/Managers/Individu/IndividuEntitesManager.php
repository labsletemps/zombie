<?php

namespace ZombieBundle\Managers\Individu;

use ZombieBundle\Entity\Individu\IndividuEntite;

class IndividuEntitesManager extends AEManager
{
	public function getObjectClass() {
		return 'ZombieBundle\Entity\Individu\IndividuEntite';
	}
	
	protected function getAlias() {
		return "indiv_ent";
	}
	
	protected function createQueryBuilder() {
		$alias = $this->getAlias();
	
		$qb = parent::createQueryBuilder();
		$qb->join('indiv_ent.individu', 'individu');
	
		return $qb;
	}
	
	protected function buildQuery($qb, $params, $options = null) {
		$hasParams = false;
		
		$qb->andWhere('individu.deleted is null or individu.deleted != 1');
		
		if (isset($params['entiteId']) && $params['entiteId']) {
			$entiteId = addslashes($params['entiteId']);
			$qb->andwhere("indiv_ent.entite = '$entiteId'");
				
			$hasParams = true;
		}
	
		return $hasParams;
	}

	/**
	 * @return IndividuEntite
	 */
	public function getIndividuEntite($id) {
		return $this->get($id);
	}
	
	/**
	 * @return IndividuEntite[]
	 */
	public function getAllIndividuEntitesForEntite($entite_id) {
		if (!$entite_id) return array();
		
		$params = array('entiteId' => $entite_id);
		return $this->query($params);
	}
	
	public function getAllRegisseurForEntite($entite_id) {
		if (!$entite_id) return array();
		
		$entitesMgr = $this->container->get('entites_manager');
		if (false) $entitesMgr = new EntitesManager();
		
		$regisseurs = array();
		$individusLinks = $this->getAllIndividuEntitesForEntite($entite_id);
		if ($individusLinks) {
			foreach ($individusLinks as $link) {
				if (false) $link = new IndividuEntite();
				$individu = $link->getIndividu();
				
				if ($individu && $individu->isRegisseur()) $regisseurs[] = $individu;
			}
		}
		
		$regisseursParent = array();
		
		$entite = $entitesMgr->getEntite($entite_id);
		if ($entite && $entite->getEntiteParent() != null) {
			$parent = $entite->getEntiteParent();
			if ($parent->getId() != $entite_id) { // This should always be true.
				//get Regisseur parents
				$regisseursParent = $this->getAllRegisseurForEntite($parent->getId());
			}
		}
		
		$regisseursById = array();
		if ($regisseurs) {
			foreach ($regisseurs as $regisseur) {
				$regisseursById[$regisseur->getId()] = $regisseur;
			}
		}
		
		if ($regisseursParent) {
			foreach ($regisseursParent as $regisseur) {
				$regisseursById[$regisseur->getId()] = $regisseur;
			}
		}
		
		return array_values($regisseursById);
	}
	
	
	/**
	 * @return IndividuEntite[]
	 */
	public function getAllIndividuEntites($options = null) {
		return $this->getAll($options);
	}
	
	
	public function getFieldsName() {
		$individusMgr = $this->container->get('individus_manager');
		$indiv_fields = $individusMgr->getFieldsName();
		
		$fields = array();
		$fields['fonction'] = array('label' => 'Fonction');
		foreach ($indiv_fields as $key => $arr) {
			$fields['individu.'.$key] = $arr;
		}
		
		return $fields;
	}
}

?>
