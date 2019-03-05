<?php

namespace Seriel\ChartbeatBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use Seriel\ChartbeatBundle\Entity\ChartbeatArticleMetrics;
use ZombieBundle\Managers\Params\ParametersManager;

class ChartbeatArticleMetricsManager extends SerielManager
{
	public function getObjectClass() {
		return 'Seriel\ChartbeatBundle\Entity\ChartbeatArticleMetrics';
	}
	
	protected function addSecurityFilters($qb, $individu) {
		// NO SECURITY HERE.
		return;
	}
	
	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;
		
		$alias = $this->getAlias();
	
		if (isset($params['article']) && $params['article']) {
			if ($this->addWhereId($qb, $alias.'.article', $params['article'])) {
				$hasParams = true;
			}
		}
		
		return $hasParams;
	}
	
	/**
	 * @return ChartbeatArticleMetrics
	 */
	public function getChartbeatArticleMetrics($id) {
		return $this->get($id);
	}
	
	/**
	 * @return ChartbeatArticleMetrics
	 */
	public function getChartbeatArticleMetricsForArticleId($article_id) {
		if (!$article_id) return null;
		if (is_array($article_id)) return null;
		
		return $this->query(array('article' => $article_id), array('one' => true));
	}
	
	/**
	 * @return ChartbeatArticleMetrics[]
	 */
	public function getAllChartbeatArticleMetrics($options = array()) {
		return $this->getAll($options);
	}
	
	public function getLastDateCalcul() {
		$Last = $this->query(array(), array('orderBy' => array('date_calcul' => 'desc'), 'limit' => 1));
		if (count($Last) == 1){
			return $Last[0]->getDateCalcul();
		}
		else {
			return null;
		}
		
	}
	public function updateMoyennesGenerales() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
	
		$em = $this->doctrine->getManager();
		
		$sql = "SELECT count(id) as qte, avg(page_views_total) as page_views_total, avg(page_time_total) as page_time_total, avg(page_avg_scroll_since_parution) as page_avg_scroll_since_parution, avg(page_avg_scroll_since_evergreen) as page_avg_scroll_since_evergreen, avg(page_views_per_day_since_parution) as page_views_per_day_since_parution, avg(page_views_per_day_between_parution_and_evergreen) as page_views_per_day_between_parution_and_evergreen, avg(page_views_per_day_since_evergreen) as page_views_per_day_since_evergreen, avg(page_views_ratio_before_and_after_evergreen) as page_views_ratio_before_and_after_evergreen, avg(page_time_per_day_since_parution) as page_time_per_day_since_parution, avg(page_time_per_day_since_evergreen) as page_time_per_day_since_evergreen, avg(page_average_time_since_parution) as page_average_time_since_parution, avg(page_average_time_since_evergreen) as page_average_time_since_evergreen, avg(page_time_avg) as page_time_avg, avg(page_time_avg_on_words) as page_time_avg_on_words , avg(indicator_durabilite) as durabilite_avg FROM chartbeat_article_metrics";
		
		$stmt = $em->getConnection()->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll();
	
		if ($rows) {
			$row = $rows[0];
				
			$qte = $row['qte'];
				
			$page_views_total = round($row['page_views_total']);
			$page_time_total = round($row['page_time_total']);
			$page_avg_scroll_since_parution = round($row['page_avg_scroll_since_parution']);
			$page_avg_scroll_since_evergreen = round($row['page_avg_scroll_since_evergreen']);
			$page_views_per_day_since_parution = round($row['page_views_per_day_since_parution']);
			$page_views_per_day_between_parution_and_evergreen = round($row['page_views_per_day_between_parution_and_evergreen']);
			$page_views_per_day_since_evergreen = round($row['page_views_per_day_since_evergreen']);
			$page_views_ratio_before_and_after_evergreen = $row['page_views_ratio_before_and_after_evergreen'];
			$page_time_per_day_since_parution = round($row['page_time_per_day_since_parution']);
			$page_time_per_day_since_evergreen = round($row['page_time_per_day_since_evergreen']);
			
			$page_average_time_since_parution = round($row['page_average_time_since_parution']);
			$page_average_time_since_evergreen = round($row['page_average_time_since_evergreen']);
			
			$page_time_avg = round($row['page_average_time_since_parution']);
			$page_time_avg_on_words = $row['page_time_avg_on_words'];
			$durabilite_avg = $row['durabilite_avg'];
			// On stock les valeur
				
			$paramsMgr->setValue('chartbeat.qte', $qte);
				
			$paramsMgr->setValue('chartbeat.page_views_total', $page_views_total);
			$paramsMgr->setValue('chartbeat.page_time_total', $page_time_total);
			$paramsMgr->setValue('chartbeat.page_avg_scroll_since_parution', $page_avg_scroll_since_parution);
			$paramsMgr->setValue('chartbeat.page_avg_scroll_since_evergreen', $page_avg_scroll_since_evergreen);
			$paramsMgr->setValue('chartbeat.page_views_per_day_since_parution', $page_views_per_day_since_parution);
			$paramsMgr->setValue('chartbeat.page_views_per_day_between_parution_and_evergreen', $page_views_per_day_between_parution_and_evergreen);
			$paramsMgr->setValue('chartbeat.page_views_per_day_since_evergreen', $page_views_per_day_since_evergreen);
			$paramsMgr->setValue('chartbeat.page_views_ratio_before_and_after_evergreen', $page_views_ratio_before_and_after_evergreen);
			$paramsMgr->setValue('chartbeat.page_time_per_day_since_parution', $page_time_per_day_since_parution);
			$paramsMgr->setValue('chartbeat.page_time_per_day_since_evergreen', $page_time_per_day_since_evergreen);
			
			$paramsMgr->setValue('chartbeat.page_average_time_since_parution', $page_average_time_since_parution);
			$paramsMgr->setValue('chartbeat.page_average_time_since_evergreen', $page_average_time_since_evergreen);
			
			$paramsMgr->setValue('chartbeat.page_time_avg', $page_time_avg);
			$paramsMgr->setValue('chartbeat.page_time_avg_on_words', $page_time_avg_on_words);
			$paramsMgr->setValue('chartbeat.durabilite_avg', $durabilite_avg);
		}
	
		$paramsMgr->flush();
	}
	
	public function getGeneralMoyView() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return intval($paramsMgr->getValue('chartbeat.page_views_per_day_since_parution'));
	}
	public function getGeneralMoyTime() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return intval($paramsMgr->getValue('chartbeat.page_average_time_since_parution'));
	}
	
	public function getGeneralMoyViewEvergreen() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return intval($paramsMgr->getValue('chartbeat.page_views_per_day_since_evergreen'));
	}
	public function getGeneralMoyTimeEvergreen() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return intval($paramsMgr->getValue('chartbeat.page_average_time_since_evergreen'));
	}
	
	public function getGeneralRatioViewBeforeAndAfterEvergreen() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return $paramsMgr->getValue('chartbeat.page_views_ratio_before_and_after_evergreen');		
	}
	
	public function getGeneralPageTimeAvg() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return $paramsMgr->getValue('chartbeat.page_time_avg');
	}
	
	public function getGeneralPageTimeAvgOnWords() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return $paramsMgr->getValue('chartbeat.page_time_avg_on_words');
	}
	
	public function getGeneralMoyDurabilite() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return $paramsMgr->getValue('chartbeat.durabilite_avg');
	}
}

?>
