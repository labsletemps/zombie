<?php

namespace ZombieBundle\Managers\Utils;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use ZombieBundle\Entity\Utils\SearchHelper;

class SearchHelperManager extends SerielManager
{
	public function getObjectClass() {
		return 'ZombieBundle\Entity\Utils\SearchHelper';
	}

	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParam = false;
		
		$alias = $this->getAlias();
		
		if (isset($params['uid']) && $params['uid']) {
			$qb->andwhere($alias.'.uid = :uid')->setParameter('uid', $params['uid']);
			$hasParam = true;
		}
		if (isset($params['created_at']) && $params['created_at']) {
			$qb->andwhere($alias.'.created_at < :created_at')->setParameter('created_at',$params['created_at']);
			$hasParams = true;
		}
		
		return $hasParam;
	}
	
	protected function createUID() {
		return time().'_'.rand(10000, 99999);
	}
	
	public function mapDatas($datas) {
		$uid = $this->createUID();
		
		$elems = array();
		foreach ($datas as $id => $dt) {
			$elem = new SearchHelper();
			$elem->setUid($uid);
			$elem->setObjId($id);
			
			if (is_array($dt)) {
				$counterNum = 1;
				$counterStr = 1;
				
				foreach ($dt as $val) {
					if (is_numeric($val)) {
						eval('$elem->setNum'.$counterNum.'($val);');
						$counterNum++;
					} else {
						eval('$elem->setStr'.$counterStr.'($val);');
						$counterStr++;
					}
				}
			} else {
				if (is_numeric($dt)) {
					$elem->setNum1($dt);
				} else {
					$elem->setStr1($dt);
				}
			}
			$this->save($elem);
		}
		
		$this->flush();
		return $uid;
	}
	
	/**
	 * @return SearchHelper
	 */
	public function getSearchHelper($id) {
		return $this->get($id);
	}
	
	/**
	 * @return SearchHelper[]
	 */
	public function getAllSearchHelpersForUID($uid, $options = array()) {
		return $this->query(array('uid' => $uid), $options);
	}
	
	/**
	 * @return SearchHelper[]
	 */
	public function getAllSearchHelpers($options = array()) {
		return $this->getAll($options);
	}
	
	/**
	 * @return int
	 */
	public function deleteOldSearchHelpers(\DateTime $date_limite) {
		if (!$date_limite) return null;
		
		$qb = $this->getQueryBuilder(array('created_at' => $date_limite), array());
		$objClass = $this->class;
		$alias = $this->getAlias();
		$qb->delete($objClass, $alias);
		return $qb->getQuery()->execute();
		
	}
}

?>
