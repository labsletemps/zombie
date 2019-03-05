<?php

namespace ZombieBundle\Managers\Recherche;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use ZombieBundle\Entity\Recherche\RechercheSauvegarde;

class RecherchesSauvegardesManager extends SerielManager
{
	protected function addSecurity($qb) {
		// No security here.
		return;
	}
	
	public function getObjectClass() {
		return 'ZombieBundle\Entity\Recherche\RechercheSauvegarde';
	}
	
	public function getAlias() {
		return 'rs';
	}
	
	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;
		
		$qb->andWhere('(rs.deleted is null or rs.deleted != 1)');
	
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
	
		return $hasParams;
	}
	
	/**
	 * @return RechercheSauvegarde
	 */
	public function getRechercheSauvegarde($id) {
		return $this->get($id);
	}
	
	/**
	 * @return RechercheSauvegarde[]
	 */
	public function getAllRecherchesSauvegardesForTypeAndIndividu($type, $individu_id) {
		if ((!$type) || (!$individu_id)) return array();
		return $this->query(array('type' => $type, 'individu' => $individu_id));
	}
	
	/**
	 * @return RechercheSauvegarde[]
	 */
	public function getRechercheSauvegardeForTypeNomAndIndividu($type, $nom, $individu_id) {
		if ((!$type) || (!$nom) || (!$individu_id)) null;
		return $this->query(array('type' => $type, 'nom' => $nom, 'individu' => $individu_id), array('one' => true));
	}
	
	/**
	 * @return RechercheSauvegarde[]
	 */
	public function getAllRecherchesSauvegardes($options = array()) {
		return $this->getAll($options);
	}
	
}

?>
