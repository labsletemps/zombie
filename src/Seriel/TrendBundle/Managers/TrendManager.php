<?php

namespace Seriel\TrendBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;

class TrendManager extends SerielManager{

	public function getObjectClass() {
		return 'Seriel\TrendBundle\Entity\Trend';
	}
	
	protected function addSecurityFilters($qb, $individu) {
		// NO SECURITY HERE.
		return;
	}
	
	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;
		
		$alias = $this->getAlias();
		if (isset($params['date']) && $params['date']) {
			if ($this->addWhereDate($qb, $alias.'.date', $params['date'])) {
				$hasParams = true;
			}
		}
		if (isset($params['day']) && $params['day']) {
			$qb->andWhere('DAY('.$alias.'.date) = :day')->setParameter('day', $params['day']);
			$hasParams = true;
		}
		if (isset($params['month']) && $params['month']) {
			$qb->andWhere('MONTH('.$alias.'.date) = :month')->setParameter('month', $params['month']);
			$hasParams = true;
		}		
		if (isset($params['module']) && $params['module']) {
			$qb->andWhere($alias.'.module = :module')->setParameter('module', $params['module']);
			$hasParams = true;
		}
		
		return $hasParams;
	}
	
	/**
	 * @return Trend
	 */
	public function getTrend($id) {
		return $this->get($id);
	}
	
	
	/**
	 * @return Trend[]
	 */
	public function getAllTrend($options = array()) {
		return $this->getAll($options);
	}
	
	/**
	 * @return Trend[]
	 */
	public function getTrends($qte, $startDate, $endDate) {
		if (!$startDate) $startDate= '';
		if (!$endDate) $endDate= '';
		$date = $startDate.'::'.$endDate;
		if ($qte > 0) {
			return $this->query(array('date' => $date), array('limit' => $qte, 'orderBy' => array('position' => 'desc')));
		}
		else {
			return $this->query(array('date' => $date), array('orderBy' => array('position' => 'desc')));
		}
		
	}
	
	/**
	 * @return Trend[]
	 */
	public function getTrendsByDayInYear($day, $month) {
		if (!$day) return array();
		if (!$month) return array();

		return $this->query(array('day' => $day, 'month' => $month), array('orderBy' => array('position' => 'desc')));
	}
	
	/**
	 * @return int
	 */
	public function removeAllTrendByDateModule($date, $module) {
		if (!$date) return null;
		if (is_array($date)) return null;
		if (!$module) return null;
		if (is_array($module)) return null;
		
		$qb = $this->getQueryBuilder(array('date' => $date->format('Y-m-d'),'module' => $module), array());
		$objClass = $this->class;
		$alias = $this->getAlias();
		$qb->delete($objClass, $alias);

		return $qb->getQuery()->execute();
	}
	
	public function getLastDate() {
		$Last = $this->query(array(), array('orderBy' => array('date' => 'desc'), 'limit' => 1));
		if (count($Last) == 1){
			return $Last[0]->getDate();
		}
		else {
			return null;
		}
		
	}
}
