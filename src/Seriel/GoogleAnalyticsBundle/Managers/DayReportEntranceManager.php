<?php

namespace Seriel\GoogleAnalyticsBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use Seriel\GoogleAnalyticsBundle\Entity\DayReportEntrance;
use Doctrine\ORM\Query\Expr\Join;
class DayReportEntranceManager extends SerielManager
{
	public function getObjectClass() {
		return 'Seriel\GoogleAnalyticsBundle\Entity\DayReportEntrance';
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
			$qb->innerJoin('Seriel\GoogleAnalyticsBundle\Entity\GoogleAnalyticsDayReport', 'drep', Join::WITH, $alias.'.dayreport = drep.id');
			$qb->andWhere('drep.path in (:paths)')->setParameter('paths', $paths);
			$hasParams = true;
		}
		
		return $hasParams;
	}
	
	/**
	 * @return DayReportEntrance
	 */
	public function getDayReportEntrance($id) {
		return $this->get($id);
	}

	/**
	 * @return DayReportEntrance[]
	 */
	public function getDayReportEntranceByPaths($paths,$options = array()) {
		if (!is_array($paths)) return null;
		return $this->query(array('paths' => $paths), $options);
	}
	
	/**
	 * @return int
	 */
	public function deleteDayReportEntranceByDate($date_debut,$date_fin) {
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
	 * @return DayReportEntrance[]
	 */
	public function getAllDayReportEntranceByDate($date_debut,$date_fin){
		if ((!$date_debut) && (!$date_fin)) return null;
		if (!$date_debut) $date_debut = '';
		if (!$date_fin) $date_fin = '';
		$date = $date_debut.'::'.$date_fin;
		var_dump($date);
		return $this->query(array('date' => $date), array());
	}
	
	/**
	 * @return DayReportEntrance[]
	 */
	public function getAllDayReportEntrance($options = array()) {
		return $this->getAll($options);
	}
}

?>
