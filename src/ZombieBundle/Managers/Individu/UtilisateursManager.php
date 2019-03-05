<?php

namespace ZombieBundle\Managers\Individu;

use ZombieBundle\Entity\Individu\Utilisateur;
use ZombieBundle\Managers\ZombieManager;
use ZombieBundle\Security\Utils\GestionnaireDeDroits;
use ZombieBundle\Entity\Individu\Individu;

class UtilisateursManager extends ZombieManager
{
	private $_cache_user_id = array();
	
	protected function isGrantedFilter( $individu, $code) {
		if ($individu instanceof Individu) {
			
			// get entite.			
			$entite = $individu->getEntitePrincipale();
			if (!$entite) {
				return false;
			}
			
			$gestionnaire_de_droits = $this->container->get('security_manager')->getCurrentCredentials();
			if (!$gestionnaire_de_droits) {
				return false;
			}
			if (false) $gestionnaire_de_droits = new GestionnaireDeDroits($individu);

			switch ($code) {
				case 'view':
					$levels= $gestionnaire_de_droits->getAllLevelsForDroit('ZombieBundle\Entity\Individu\Utilisateur >> view');
					if ((!$levels) || count($levels) == 0) {
						return false;
					}
					else {
						return true;
					}
				case 'edit':
					$levels = $gestionnaire_de_droits->getAllLevelsForDroit('ZombieBundle\Entity\Individu\Utilisateur >> edit');
					if ((!$levels) || count($levels) == 0) {
						return false;
					}
					else {
						return true;
					}
				default:
					return true;
			}

			
		}
	
		// if user is no individu = > block.
		return false;

	}
	
	public function getObjectClass() {
		return 'ZombieBundle\Entity\Individu\Utilisateur';
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
	 * @return Utilisateur
	 */
	public function getUtilisateur($id) {
		// No use Security checks.
		$em = $this->getDoctrineEM();
		return $em->getRepository($this->class)->find($id);
	}
	
	/**
	 * @return Utilisateur
	 */
	public function getUtilisateurForUserId($id) {
		if (isset($this->_cache_user_id[$id])) return $this->_cache_user_id[$id];
		$res = $this->query(array('user_id' => $id), array('one' => true));
		$this->_cache_user_id[$id] = $res;
		return $res;
	}
	
	/**
	 * @return Utilisateur
	 */
	public function getUtilisateurForNeocaseUid($_ncuid) {
		return $this->query(array('ncuid' => $_ncuid), array('one' => true));
	}
	
	public function getUtilisateurForNomEtPrenom($nom, $prenom) {
		$indivs = $this->query(array('nom' => $nom, 'prenom' => $prenom));
		if ($indivs) {
			if (count($indivs) > 1) {
				$this->logger->warning("duplicate Utilisateur : $nom $prenom");
			}
			foreach ($indivs as $ind) return $ind;
		}
		
		return null;
	}
	
	/**
	 * @return Utilisateur[]
	 */
	public function getAllUtilisateurs($options = null) {
		return $this->getAll($options);
	}
	
	public function getUtilisateurForSearch($search) {
		return $this->query(array('search' => $search));
	}
        
}

?>
