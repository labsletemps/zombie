<?php

namespace Seriel\GoogleAnalyticsBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use Seriel\GoogleAnalyticsBundle\Entity\GoogleAnalyticsArticleMetrics;

class GoogleAnalyticsArticleMetricsManager extends SerielManager
{
	public function getObjectClass() {
		return 'Seriel\GoogleAnalyticsBundle\Entity\GoogleAnalyticsArticleMetrics';
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
		if (isset($params['date_calcul']) && $params['date_calcul']) {
			$qb->andwhere($alias.'.date_calcul < :date_calcul')->setParameter('date_calcul',$params['date_calcul']);
			$hasParams = true;
		}
		return $hasParams;
	}
	
	/**
	 * @return GoogleAnalyticsArticleMetrics
	 */
	public function getGoogleAnalyticsArticleMetrics($id) {
		return $this->get($id);
	}
	
	/**
	 * @return GoogleAnalyticsArticleMetrics
	 */
	public function getGoogleAnalyticsArticleMetricsForArticleId($article_id) {
		if (!$article_id) return null;
		if (is_array($article_id)) return null;
		
		return $this->query(array('article' => $article_id), array('one' => true));
	}
	
	/**
	 * @return GoogleAnalyticsArticleMetrics[]
	 */
	public function getAllGoogleAnalyticsArticleMetrics($options = array()) {
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
	/**
	 * @return int
	 */
	public function deleteGoogleAnalyticsOldArticleMetrics(\DateTime $date_limite) {
		if (!$date_limite) return null;
		
		$qb = $this->getQueryBuilder(array('date_calcul' => $date_limite), array());
		$objClass = $this->class;
		$alias = $this->getAlias();
		$qb->delete($objClass, $alias);
		return $qb->getQuery()->execute();
		
	}
	
	// Update average of google analytics metrics in Table parameters
	public function updateAverageGeneral() {
		
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		$em = $this->doctrine->getManager();
		$sql = "SELECT count(id) as qte, avg(pageview_measure) as avg_pageview_measure, avg(pageview_subscriber_measure) as avg_pageview_subscriber_measure,avg(pageview_visitor_measure) as avg_pageview_visitor_measure,";
		$sql .= " avg(uniquepageview_measure) as avg_uniquepageview_measure, avg(uniquepageview_subscriber_measure) as avg_uniquepageview_subscriber_measure, avg(uniquepageview_visitor_measure) as avg_uniquepageview_visitor_measure,";
		$sql .= " avg(readtime_measure) as avg_readtime_measure, avg(readtime_subscriber_measure) as avg_readtime_subscriber_measure, avg(readtime_visitor_measure) as avg_readtime_visitor_measure,";
		$sql .= " avg(entrance_measure) as avg_entrance_measure, avg(entrance_subscriber_measure) as avg_entrance_subscriber_measure, avg(entrance_visitor_measure) as avg_entrance_visitor_measure,";
		$sql .= " avg(subscription_measure) as avg_subscription_measure,";
		$sql .= " avg(completionread_indicator) as avg_completionread_indicator,  avg(subscription_indicator) as avg_subscription_indicator,";
		$sql .= " avg(entrance_indicator) as avg_entrance_indicator,";
		$sql .= " avg(bounce_indicator) as avg_bounce_indicator,";
		$sql .= " avg(pageview_indicator) as avg_pageview_indicator,";
		$sql .= " avg(attention_indicator) as avg_attention_indicator,";
		$sql .= " avg(abonne_like_indicator) as avg_abonne_like_indicator,";
		$sql .= " avg(visiteur_like_indicator) as avg_visitor_like_indicator,";
		$sql .= " avg(completionread_subscriber_indicator) as avg_completionread_subscriber_indicator,";
		$sql .= " avg(completionread_visitor_indicator) as avg_completionread_visitor_indicator";
		$sql .= " FROM google_analytics_article_metrics";
			
		$stmt = $em->getConnection()->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll();
		
		if ($rows) {
			$row = $rows[0];
			
			$qte = $row['qte'];
			
			$avg_pageview_measure = round($row['avg_pageview_measure']);
			$avg_pageview_subscriber_measure = round($row['avg_pageview_subscriber_measure']);
			$avg_pageview_visitor_measure = round($row['avg_pageview_visitor_measure']);
			$avg_uniquepageview_measure= round($row['avg_uniquepageview_measure']);
			$avg_uniquepageview_subscriber_measure= round($row['avg_uniquepageview_subscriber_measure']);
			$avg_uniquepageview_visitor_measure= round($row['avg_uniquepageview_visitor_measure']);
			$avg_readtime_measure= round($row['avg_readtime_measure']);
			$avg_readtime_subscriber_measure= round($row['avg_readtime_subscriber_measure']);
			$avg_readtime_visitor_measure= round($row['avg_readtime_visitor_measure']);
			$avg_entrance_measure=  round($row['avg_entrance_measure']);
			$avg_entrance_subscriber_measure = round($row['avg_entrance_subscriber_measure']);
			$avg_entrance_visitor_measure = round($row['avg_entrance_visitor_measure']);
			$avg_subscription_measure= round($row['avg_subscription_measure']);
			$avg_completionread_indicator= (float)($row['avg_completionread_indicator']);
			$avg_subscription_indicator = (float)($row['avg_subscription_indicator']);		
			$avg_entrance_indicator= (float)($row['avg_entrance_indicator']);
			$avg_pageview_indicator= (float)($row['avg_pageview_indicator']);
			$avg_completionread_subscriber_indicator= (float)($row['avg_completionread_subscriber_indicator']);
			$avg_completionread_visitor_indicator= (float)($row['avg_completionread_visitor_indicator']);
			$avg_attention_indicator= (float)($row['avg_attention_indicator']);
			$avg_abonne_like_indicator= (float)($row['avg_abonne_like_indicator']);
			$avg_visitor_like_indicator = (float)($row['avg_visitor_like_indicator']);
			$avg_bounce_indicator = (float)($row['avg_bounce_indicator']);
			
			// On stock les valeur
			$paramsMgr->setValue('googleanalytics.qte', $qte);
			$paramsMgr->setValue('googleanalytics.avg_pageview_measure', $avg_pageview_measure);
			$paramsMgr->setValue('googleanalytics.avg_pageview_subscriber_measure', $avg_pageview_subscriber_measure);
			$paramsMgr->setValue('googleanalytics.avg_pageview_visitor_measure', $avg_pageview_visitor_measure);
			$paramsMgr->setValue('googleanalytics.avg_uniquepageview_measure', $avg_uniquepageview_measure);
			$paramsMgr->setValue('googleanalytics.avg_uniquepageview_subscriber_measure', $avg_uniquepageview_subscriber_measure);
			$paramsMgr->setValue('googleanalytics.avg_uniquepageview_visitor_measure', $avg_uniquepageview_visitor_measure);		
			$paramsMgr->setValue('googleanalytics.avg_readtime_measure', $avg_readtime_measure);
			$paramsMgr->setValue('googleanalytics.avg_readtime_subscriber_measure', $avg_readtime_subscriber_measure);
			$paramsMgr->setValue('googleanalytics.avg_readtime_visitor_measure', $avg_readtime_visitor_measure);
			$paramsMgr->setValue('googleanalytics.avg_entrance_measure', $avg_entrance_measure);
			$paramsMgr->setValue('googleanalytics.avg_entrance_subscriber_measure', $avg_entrance_subscriber_measure);
			$paramsMgr->setValue('googleanalytics.avg_entrance_visitor_measure', $avg_entrance_visitor_measure);
			$paramsMgr->setValue('googleanalytics.avg_subscription_measure', $avg_subscription_measure);
			$paramsMgr->setValue('googleanalytics.avg_completionread_indicator', $avg_completionread_indicator);
			$paramsMgr->setValue('googleanalytics.avg_subscription_indicator', $avg_subscription_indicator);
			
			$paramsMgr->setValue('googleanalytics.avg_entrance_indicator', $avg_entrance_indicator);
			$paramsMgr->setValue('googleanalytics.avg_pageview_indicator', $avg_pageview_indicator);
			$paramsMgr->setValue('googleanalytics.avg_completionread_subscriber_indicator', $avg_completionread_subscriber_indicator);
			$paramsMgr->setValue('googleanalytics.avg_completionread_visitor_indicator', $avg_completionread_visitor_indicator);
			$paramsMgr->setValue('googleanalytics.avg_attention_indicator', $avg_attention_indicator);
			$paramsMgr->setValue('googleanalytics.avg_abonne_like_indicator', $avg_abonne_like_indicator);
			$paramsMgr->setValue('googleanalytics.avg_visitor_like_indicator', $avg_visitor_like_indicator);
			$paramsMgr->setValue('googleanalytics.avg_bounce_indicator', $avg_bounce_indicator);
			
			
			
		}
		
		$paramsMgr->flush();
	}
	
	public function getAvgUniquePageView() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return $paramsMgr->getValue('googleanalytics.avg_uniquepageview_measure');
	}
	
	public function getAvgUniquePageSubscriberView() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return $paramsMgr->getValue('googleanalytics.avg_uniquepageview_subscriber_measure');
	}
	
	public function getAvgUniquePageVisitorView() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return $paramsMgr->getValue('googleanalytics.avg_uniquepageview_visitor_measure');
	}
		
	public function getAvgCompletionRead() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return $paramsMgr->getValue('googleanalytics.avg_completionread_indicator');
	}

	public function getAvgSubscription() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return $paramsMgr->getValue('googleanalytics.avg_subscription_indicator');
	}
	
	public function getAvgEntranceIndicator() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return $paramsMgr->getValue('googleanalytics.avg_entrance_indicator');
	}
	public function getAvgPageViewIndicator() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return $paramsMgr->getValue('googleanalytics.avg_pageview_indicator');
	}
	public function getAvgCompletionReadSubscriber() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return $paramsMgr->getValue('googleanalytics.avg_completionread_subscriber_indicator');
	}
	public function getAvgCompletionReadVisitor() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return $paramsMgr->getValue('googleanalytics.avg_completionread_visitor_indicator');
	}
	
	public function getAvgAttentionIndicator() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return $paramsMgr->getValue('googleanalytics.avg_attention_indicator');
	}
	
	public function getAvgAbonneIndicator() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return $paramsMgr->getValue('googleanalytics.avg_abonne_like_indicator');
	}
	
	public function getAvgVisitorIndicator() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return $paramsMgr->getValue('googleanalytics.avg_visitor_like_indicator');
	}
	
	public function getAvgBounceIndicator() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return $paramsMgr->getValue('googleanalytics.avg_bounce_indicator');
	}
	
}

?>
