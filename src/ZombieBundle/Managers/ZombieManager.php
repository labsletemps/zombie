<?php

namespace ZombieBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use ZombieBundle\Managers\Securite\CredentialsManager;
use ZombieBundle\Managers\Securite\SecurityManager;
use Seriel\AppliToolboxBundle\Utils\Recherche\SearchObject;

abstract class ZombieManager extends SerielManager {
	
	protected function isGrantedFilter($individu,$code) {
		$this->logger->info('MANAGER::addSecurityFilter >> no security for : '.get_class($this));
		return true;
	}
	
	protected function isGranted($code = 'view') {
		
		if ($this instanceof CredentialsManager) return true; // For stop infinity loop.
		$securityMgr = $this->container->get('security_manager');
		if (false) $securityMgr = new SecurityManager();

		$individu = $securityMgr->getCurrentIndividu();
		
		if ($individu) {
			return $this->isGrantedFilter($individu,$code);
		}

		
		return false;
	}
	
	protected function forceQueryFail($qb) {
		// TODO : make it better.
		$qb->andWhere('1 = 2');
	}
	
	public function query($params, $options = null, &$execvars = null) {
		$params_orig = $params;
		$options_orig = $options;
		
		if ($options && isset($options['supp_params']) && $options['supp_params']) {
			foreach ($options['supp_params'] as $k => $v) {
				$params[$k] = $v;
			}
		}
		
		$objClass = $this->class;
		$alias = $this->getAlias();
		
		$qb = $this->getQueryBuilder($params, $options, $execvars);
		if (!$this->isGranted('view')) $this->forceQueryFail($qb);
		
		if ($options && isset($options['count'])) {
			$qb->select('count('.$alias.')');
			return $qb->getQuery()->getSingleScalarResult();
		} else if ($options && isset($options['ids_only'])) {
			$qb->select($alias.'.id');
			$datas = $qb->getQuery()->getResult();
			
			$res_ids = array();
			if ($datas) {
				foreach ($datas as $dt) {
					$res_ids[] = $dt['id'];
				}
			}
			
			return $res_ids;
		}
		
		$result = null;
		
		if ($options && isset($options['one'])) {
			$query = $qb->getQuery();
			try {
				$result = $query->getOneOrNullResult();
			} catch (NonUniqueResultException $ex) {
				$res = $query->getResult();
				if ($res) {
					$this->logger->warning(get_class($this).' : query for one result but got '.count($res).' > '.print_r($params_orig, true));
					foreach ($res as $r) {
						$result = $r;
						break;
					}
				}
			}
		} else {
			$must_get_ids_first = false;
			if ($options && isset($options['offset'])) {
				$offset = intval($options['offset']);
				if ($offset >= 0) {
					$must_get_ids_first = true;
					$qb->setFirstResult($offset);
				}
			}
			
			if ($options && isset($options['limit'])) {
				$limit = intval($options['limit']);
				if ($limit > 0) {
					$must_get_ids_first = true;
					$qb->setMaxResults($limit);
				}
			}
			
			if ($must_get_ids_first == true) $qb->distinct();
			
			$query = $qb->getQuery();
			$result = $query->getResult();
			
		}
		
		$result_type = ($options && isset($options['result_type']) && $options['result_type']) ? $options['result_type'] : 'default';
		
		if ($result_type == 'search_object') {
			// create object SearchObject
			$searchObject = new SearchObject($objClass, $params_orig, $options_orig, $result);
			
			if ($options && (isset($options['offset']) || isset($options['limit']))) {
				if (!isset($options['cancel_total'])) {
					$count_options = $options_orig;
					unset($count_options['total_rows']);
					if (isset($count_options['offset'])) unset($count_options['offset']);
					if (isset($count_options['limit'])) unset($count_options['limit']);
					if (isset($count_options['force_empty'])) unset($count_options['force_empty']);
					
					$count_options['count'] = true;
					
					$total = $this->query($params_orig, $count_options);
					
					$searchObject->setTotalResults($total);
				}
			}
			
			return $searchObject;
		}
		return $result;
	}
	
	public function save($obj, $flush = false) {
		if (!$obj) return false;
		if ($this->isGranted('edit')) {
			$this->updateCaches($obj);
			$this->updateListePropertiesCache($obj);
			
			if (method_exists($obj, 'getSaveCounter') && method_exists($obj, 'setSaveCounter')) {
				$orig_counter = $obj->getSaveCounter();
				$counter = 1;
				if ($orig_counter && $orig_counter > 0) $counter = $orig_counter + 1;
				
				$obj->setSaveCounter($counter);
			}
			
			$em = $this->getDoctrineEM();
			$em->persist($obj);
			
			if ($flush) $em->flush();
			return true;
		}else {
			return false;
		}
		
	}
	
	public function remove($obj, $flush = false) {
		if (!$obj) return false;
		if ($this->isGranted('edit')) {
			$em = $this->getDoctrineEM();
			$em->remove($obj);
			
			if ($flush) $em->flush();
			return true;
		}else {
			return false;
		}
		
	}
	
}