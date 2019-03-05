<?php

namespace ZombieBundle\Managers\Securite;

use Symfony\Component\HttpFoundation\Request;
use Seriel\AppliToolboxBundle\Managers\SerielManager;

class ConnexionsManager extends SerielManager
{
	public function getObjectClass() {
		return 'ZombieBundle\Entity\Securite\Connexion';
	}
	
	protected function createQueryBuilder() {
		$qb = parent::createQueryBuilder();
		// TODO : let's make it optional.
		//$qb->addSelect('compte, fournisseur');
		//$qb->addSelect('fournisseur');
		//$qb->leftJoin('connexion.compte', 'compte');
		//$qb->leftJoin('connexion.fournisseur', 'fournisseur');
	
		return $qb;
	}
	
	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParam = false;
		
		$alias = $this->getAlias();
	
		if (isset($params['date_connexion']) && $params['date_connexion']) {
			if ($this->addWhereDate($qb, $alias.'.dateConnexion', $params['date_connexion'])) {
				$hasParam = true;
			}
		}
		
		if (isset($params['individu']) && $params['individu']) {
			if ($this->addWhereId($qb, $alias.'.individu', $params['individu'])) {
				$hasParam = true;
			}
		}
	
		return $hasParam;
	}
	
	/**
	 * @return Connexion
	 */
	public function getConnexion($id) {
		return $this->get($id);
	}
	
	public function query($params, $options = array(), &$execvars = null) {
		if ((!isset($options['one'])) && (!isset($options['orderBy']))) {
			$options['orderBy'] = array('dateConnexion' => 'desc');
		}
		return parent::query($params, $options);
	}
  
	/**
	 * @return Connexion[]
	 */
	public function getAllConnexion($options = array()) {
		return $this->getAll($options);
	}
	
	public function search($params, $options = array()) {
		return $this->query($params, $options);
	}
	
	public function getSearchParamsFromRequest($request) {
		$datas = array();
		 
		if ($request instanceof Request) {
			$datas = $request->request->all();
		} else {
			$datas = $request;
		}
		
		$date_connexion = isset($datas['date_connexion']) ? $datas['date_connexion'] : null;
		$individu = isset($datas['individu']) ? $datas['individu'] : null;
		 
		$params = array();
		
		if ($date_connexion) $params['date_connexion'] = $date_connexion;
		if ($individu) $params['individu'] = $individu;
		 
		return $params;
	}
}

?>
