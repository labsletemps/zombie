<?php

namespace Seriel\GoogleAnalyticsBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use Seriel\GoogleAnalyticsBundle\Entity\GoogleAnalyticsDayReport;

class GoogleAnalyticsDayReportManager extends SerielManager
{
	public function getObjectClass() {
		return 'Seriel\GoogleAnalyticsBundle\Entity\GoogleAnalyticsDayReport';
	}
	
	protected function addSecurityFilters($qb, $individu) {
		// NO SECURITY HERE.
		return;
	}
	
	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;
		
		$alias = $this->getAlias();
	
		if (isset($params['date']) && $params['date']) {
			if ($this->addWhereDate($qb, $alias.'.day', $params['date'])) {
				$hasParams = true;
			}
		}
		if (isset($params['paths']) && $params['paths']) {
			$paths = $params['paths'];
			$qb->andWhere($alias.'.path in (:paths)')->setParameter('paths', $paths);
			$hasParams = true;
		}
		if (isset($params['uniquepageview']) && $params['uniquepageview']) {
			$qb->andWhere($alias.'.uniquepageview >= :uniquepageview')->setParameter('uniquepageview', $params['uniquepageview']);
			$hasParams = true;
		}
		
		
		return $hasParams;
	}
	
	/**
	 * @return GoogleAnalyticsDayReport
	 */
	public function getGoogleAnalyticsDayReport($id) {
		return $this->get($id);
	}
	
	/**
	 * @return GoogleAnalyticsDayReport[]
	 */
	public function getGoogleAnalyticsDayReportByPathsDate($paths,$date_debut,$date_fin) {
		if ((!$date_debut) && (!$date_fin)) return null;
		if (!$date_debut) $date_debut = '';
		if (!$date_fin) $date_fin = '';
		if (!is_array($paths)) return null;
		$date = $date_debut.'::'.$date_fin;
		return $this->query(array('paths' => $paths,'date' => $date), array());
	}
	
	/**
	 * @return GoogleAnalyticsDayReport[]
	 */
	public function getGoogleAnalyticsDayReportByDate($date_debut,$date_fin) {
		if ((!$date_debut) && (!$date_fin)) return null;
		if (!$date_debut) $date_debut = '';
		if (!$date_fin) $date_fin = '';
		$date = $date_debut.'::'.$date_fin;
		return $this->query(array('date' => $date), array());
	}

	/**
	 * @return GoogleAnalyticsDayReport[]
	 */
	public function getGoogleAnalyticsDayReportByDateView($date_debut,$date_fin, $viewMin = 1) {
		if ((!$date_debut) && (!$date_fin)) return null;
		if (!$date_debut) $date_debut = '';
		if (!$date_fin) $date_fin = '';
		$date = $date_debut.'::'.$date_fin;
		return $this->query(array('date' => $date, 'uniquepageview' => $viewMin), array());
	}
		

	/**
	 * @return GoogleAnalyticsDayReport[]
	 */
	public function getGoogleAnalyticsDayReportByPaths($paths,$options = array()) {
		if (!is_array($paths)) return null;
		return $this->query(array('paths' => $paths), $options);
	}
	
	public function getLastCreatedAt() {
		$Last = $this->query(array(), array('orderBy' => array('created_at' => 'desc'), 'limit' => 1));
		if (count($Last) == 1){
			return $Last[0]->getCreatedAt();
		}
		else {
			return null;
		}
		
	}
	
	/**
	 * @return int
	 */
	public function deleteGoogleAnalyticsDayReportByDate($date_debut,$date_fin) {
		if ((!$date_debut) && (!$date_fin)) return null;
		if (!$date_debut) $date_debut = '';
		if (!$date_fin) $date_fin = '';
		$date = $date_debut.'::'.$date_fin;
		
		$qb = $this->getQueryBuilder(array('date' => $date), array());
		$objClass = $this->class;
		$alias = $this->getAlias();
		$qb->delete($objClass, $alias);
		return $qb->getQuery()->execute();
			
	}
	
	/**
	 * @return GoogleAnalyticsDayReport[]
	 */
	public function getAllGoogleAnalyticsDayReport($options = array()) {
		return $this->getAll($options);
	}

	/**
	 * @return array()
	 */
	public function getSumMeasureGoogleAnalyticsDayReportByPaths($paths) {
		if (!is_array($paths)) return null;
		
		$qb = $this->getQueryBuilder(array('paths' => $paths), array());
		
		$qb->select('SUM('.$this->getAlias().'.pageview) as pageview');
		$qb->addselect('SUM('.$this->getAlias().'.pageview_subscriber) as pageview_subscriber');
		$qb->addselect('SUM('.$this->getAlias().'.pageview_visitor) as pageview_visitor');
		$qb->addselect('SUM('.$this->getAlias().'.uniquepageview) as uniquepageview');
		$qb->addselect('SUM('.$this->getAlias().'.uniquepageview_subscriber) as uniquepageview_subscriber');
		$qb->addselect('SUM('.$this->getAlias().'.uniquepageview_visitor) as uniquepageview_visitor');
		$qb->addselect('SUM('.$this->getAlias().'.readtime) as readtime');
		$qb->addselect('SUM('.$this->getAlias().'.readtime_subscriber) as readtime_subscriber');
		$qb->addselect('SUM('.$this->getAlias().'.readtime_visitor) as readtime_visitor');
		$qb->addselect('SUM('.$this->getAlias().'.subscription) as subscription');
		$qb->addselect('SUM('.$this->getAlias().'.entrance) as entrance');
		$qb->addselect('SUM('.$this->getAlias().'.entrance_subscriber) as entrance_subscriber');
		$qb->addselect('SUM('.$this->getAlias().'.entrance_visitor) as entrance_visitor');
		$qb->addselect('SUM('.$this->getAlias().'.exitpage) as exitpage');
		
		return $qb->getQuery()->getArrayResult();
	}

}

?>
