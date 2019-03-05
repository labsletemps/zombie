<?php

namespace ZombieBundle\Managers\Individu;

use ZombieBundle\Entity\Individu;
use Seriel\AppliToolboxBundle\Managers\SerielManager;

class IndividusManager extends SerielManager
{
	private $_cache_user_id = array();
		
	public function getObjectClass() {
		return 'ZombieBundle\Entity\Individu\Individu';
	}
	
	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;
		
		$alias = $this->getAlias();
		
		$qb->andWhere($alias.'.deleted is null or '.$alias.'.deleted != 1');
		
		if (isset($params['user_id']) && $params['user_id']) {
			$user_id = $params['user_id'];
			
			$qb->andWhere($alias.'.user = :user_id')->setParameter('user_id', $user_id);
			$hasParams = true;
		}
		
		if (isset($params['nom']) && $params['nom']) {
			$nom = $params['nom'];
			$qb->andWhere($alias.'.nom = :nom')->setParameter('nom', $nom);
			$hasParams = true;
		}
		
		if (isset($params['prenom']) && $params['prenom']) {
			$prenom = $params['prenom'];
			$qb->andWhere($alias.'.prenom = :prenom')->setParameter('prenom', $prenom);
			$hasParams = true;
		}
		
		if (isset($params['nom_complet']) && $params['nom_complet']) {
			$nom_complet = addslashes($params['nom_complet']);
			if ($this->addWhereFullTextLike($qb, $alias.'.nom_complet', $nom_complet)) {
				$hasParams = true;
			}
		}
		
		if (isset($params['search']) && $params['search']) {
			$search = $params['search'];
			$this->addWhereFullTextLike($qb, $alias.'.nom_complet', $search);
			$hasParams = true;
		}
	
		return $hasParams;
	}

	/**
	 * @return Individu
	 */
	public function getIndividu($id) {

		// No use Security checks.
		$em = $this->getDoctrineEM();
		return $em->getRepository($this->class)->find($id);
	}
	
	/**
	 * @return Individu
	 */
	public function getIndividuForUserId($id) {
		if (isset($this->_cache_user_id[$id])) return $this->_cache_user_id[$id];
		$res = $this->query(array('user_id' => $id), array('one' => true));
		$this->_cache_user_id[$id] = $res;
		return $res;
	}
	
	/**
	 * @return Individu
	 */
	public function getIndividuForNeocaseUid($_ncuid) {
		return $this->query(array('ncuid' => $_ncuid), array('one' => true));
	}
	
	public function getIndividuForNomEtPrenom($nom, $prenom) {
		$indivs = $this->query(array('nom' => $nom, 'prenom' => $prenom));
		if ($indivs) {
			if (count($indivs) > 1) {
				$this->logger->warning("duplicate individu : $nom $prenom");
			}
			foreach ($indivs as $ind) return $ind;
		}
		
		return null;
	}
	
	/**
	 * @return Individu[]
	 */
	public function getAllIndividus($options = null) {
		return $this->getAll($options);
	}
	
	public function getIndividuForSearch($search) {
		return $this->query(array('search' => $search));
	}
        
}

?>
