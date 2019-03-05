<?php

namespace Seriel\ChartbeatBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use Seriel\ChartbeatBundle\Entity\ChartbeatArticleDayReport;
use Doctrine\ORM\Query\Expr\From;
use Doctrine\ORM\Query\ResultSetMapping;

class ChartbeatArticleDayReportManager extends SerielManager
{
	public function getObjectClass() {
		return 'Seriel\ChartbeatBundle\Entity\ChartbeatArticleDayReport';
	}
	
	protected function addSecurityFilters($qb, $individu) {
		// NO SECURITY HERE.
		return;
	}
	
	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;
		
		$alias = $this->getAlias();
	
		if (isset($params['path']) && $params['path']) {
			$path = $params['path'];
			if (!is_array($path)) $path = array($path);
			
			$path_in = array();
			$path_params = array();
			
			$counter = 1;
			foreach ($path as $p) {
				$path_in[] = ':path'.$counter;
				$path_params[':path'.$counter] = $p;
				
				$counter++;
			}
			
			$qb->andWhere($alias.'.path in ('.implode(', ', $path_in).')')->setParameters($path_params);
			$hasParams = true;
		}
		
		if (isset($params['day']) && $params['day']) {
			if ($this->addWhereDate($qb, $alias.'.day', $params['day']));
		}
		

		if (isset($params['opt1']) && $params['opt1']) {
			$opt1 = $params['opt1'];
			$qb->andWhere($alias.'.option_1 = :opt1')->setParameter('opt1', $opt1);
		}
		if (isset($params['opt2']) && $params['opt2']) {
			$opt2 = $params['opt2'];
			$qb->andWhere($alias.'.option_2 = :opt2')->setParameter('opt2', $opt2);
		}
		if (isset($params['opt3']) && $params['opt3']) {
			$opt3 = $params['opt3'];
			$qb->andWhere($alias.'.option_3 = :opt3')->setParameter('opt3', $opt3);
		}
		if (isset($params['opt4']) && $params['opt4']) {
			$opt4 = $params['opt4'];
			$qb->andWhere($alias.'.option_4 = :opt4')->setParameter('opt4', $opt4);
		}
		if (isset($params['opt5']) && $params['opt5']) {
			$opt5 = $params['opt5'];
			$qb->andWhere($alias.'.option_5 = :opt5')->setParameter('opt5', $opt5);
		}
		if (isset($params['opt6']) && $params['opt6']) {
			$opt6 = $params['opt6'];
			$qb->andWhere($alias.'.option_6 = :opt6')->setParameter('opt6', $opt6);
		}
		if (isset($params['opt7']) && $params['opt7']) {
			$opt7 = $params['opt7'];
			$qb->andWhere($alias.'.option_7 = :opt7')->setParameter('opt7', $opt7);
		}
		if (isset($params['opt8']) && $params['opt8']) {
			$opt8 = $params['opt8'];
			$qb->andWhere($alias.'.option_8 = :opt8')->setParameter('opt8', $opt8);
		}
		if (isset($params['opt9']) && $params['opt9']) {
			$opt9 = $params['opt9'];
			$qb->andWhere($alias.'.option_9 = :opt9')->setParameter('opt9', $opt9);
		}
		
		return $hasParams;
	}
	
	/**
	 * @return ChartbeatArticleDayReport
	 */
	public function getChartbeatArticleDayReport($id) {
		return $this->get($id);
	}
	
	/**
	 * @return ChartbeatArticleDayReport
	 */
	public function getChartbeatArticleDayReportForPathAndDay($path, $day) {
		if ((!$path) || (!$day)) return null;
		return $this->query(array('path' => $path, 'day' => $day), array('one' => true));
	}
	
	/**
	 * @return ChartbeatArticleDayReport[]
	 */
	public function getAllChartbeatArticleDayReportForDay($day, $options = array()) {
		if (!$day) return array();
		return $this->query(array('day' => $day), $options);
	}
	
	/**
	 * @return ChartbeatArticleDayReport[]
	 */
	public function getAllChartbeatArticleDayReportForPath($path, $options = array()) {
		if (!$path) return array();
		return $this->query(array('path' => $path), $options);
	}
	
	/**
	 * @return ChartbeatArticleDayReport[]
	 */
	public function getAllChartbeatArticleDayReport($options = array()) {
		return $this->getAll($options);
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
	 * @return ChartbeatArticleDayReport[]
	 */
	public function getDataForCalculGiniEvergreen($paths,$day_start, $day_end, $options = array()) {
		if (!is_array($paths)) return array();
		if (!$day_start) return array();
		if (!$day_end) return array();

		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('day', 'day');
		$rsm->addScalarResult('nb_jour_ratio', 'nb_jour_ratio');
		$rsm->addScalarResult('page_view_ratio', 'page_view_ratio');
		
		$sql =' SELECT rep.day ';
		$sql .= ', ( (TO_DAYS(rep.day) - TO_DAYS(?)) /  (TO_DAYS(?) - TO_DAYS(?)) )as nb_jour_ratio ';
		$sql .= ', (select SUM(rep1.page_views) FROM chartbeat_article_day_report as rep1 where  rep1.path in(?) and rep1.day <= rep.day and rep1.day >= ?) ';
		$sql .= '/ (select SUM(rep2.page_views) FROM chartbeat_article_day_report as rep2 where  rep2.path in(?) and rep2.day <= ? and rep2.day >= ?) as page_view_ratio ';
		$sql .= ' FROM chartbeat_article_day_report as rep ';
		$sql .= ' WHERE rep.path in(?) ';
		$sql .= ' AND rep.day >= ? ';
		$sql .= ' AND rep.day <= ? ';
		$sql .= ' union select ? as day, 1 as nb_jour_ratio, 1 as page_view_ratio ';
		$sql .= ' order by day';
		
		
		$query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
		
		
		// -----------------PARAMETER---------------------
		$i = 1;
		$query->setParameter($i, $day_start);
		$i++;
		$query->setParameter($i, $day_end);
		$i++;
		$query->setParameter($i, $day_start);
		$i++;
		$query->setParameter($i, $paths);
		$i++;
		$query->setParameter($i, $day_start);
		$i++;
		$query->setParameter($i, $paths);
		$i++;
		$query->setParameter($i, $day_end);
		$i++;
		$query->setParameter($i, $day_start);
		$i++;
		$query->setParameter($i, $paths);
		$i++;
		$query->setParameter($i, $day_start);
		$i++;
		$query->setParameter($i, $day_end);
		$i++;
		$query->setParameter($i, $day_end);
		$i++;
		return $query->getResult();	

	}
}

?>
