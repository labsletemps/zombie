<?php

namespace Seriel\CrossIndicatorBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use Seriel\CrossIndicatorBundle\Entity\CrossIndicatorArticle;
use Doctrine\ORM\Query\Expr\Join;

class CrossIndicatorArticleManager extends SerielManager
{
	public function getObjectClass() {
		return 'Seriel\CrossIndicatorBundle\Entity\CrossIndicatorArticle';
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
		if (isset($params['datearticle']) && $params['datearticle']) {
			$qb->innerJoin('ZombieBundle\Entity\News\Article', 'art', Join::WITH, $alias.'.article = art.id');
			if ($this->addWhereDate($qb, 'art.date_parution', $params['datearticle'])) {
				$hasParams = true;
			}
		}
		if (isset($params['datearticleday']) && $params['datearticleday']) {
			$qb->innerJoin('ZombieBundle\Entity\News\Article', 'art', Join::WITH, $alias.'.article = art.id');
			$qb->andWhere('WEEKDAY(art.date_parution) = WEEKDAY(:day)')->setParameter('day', $params['datearticleday']);
			$hasParams = true;
	
		}

		return $hasParams;
	}
	
	/**
	 * @return CrossIndicatorArticle
	 */
	public function getCrossIndicatorArticle($id) {
		return $this->get($id);
	}
	
	/**
	 * @return CrossIndicatorArticle
	 */
	public function getCrossIndicatorArticleForArticleId($article_id) {
		if (!$article_id) return null;
		if (is_array($article_id)) return null;
		
		return $this->query(array('article' => $article_id), array('one' => true));
	}
	
	/**
	 * @return CrossIndicatorArticle[]
	 */
	public function getAllCrossIndicatorArticle($options = array()) {
		return $this->getAll($options);
	}
	
	/**
	 * @return CrossIndicatorArticle[]
	 */
	public function getAllCrossIndicatorArticleByDateArticle($date_debut,$date_fin) {
		if ((!$date_debut) && (!$date_fin)) return null;
		if (!$date_debut) $date_debut = '';
		if (!$date_fin) $date_fin = '';
		$date = $date_debut.'::'.$date_fin;
		return $this->query(array('datearticle' => $date), array());
	}
	
	/**
	 * @return CrossIndicatorArticle[]
	 */
	public function getAllCrossIndicatorArticleByDateArticleDay($day) {
		if (!$day) return array();
		return $this->query(array('datearticleday' => $day), array());
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
	public function updateAvgAll() {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		$em = $this->doctrine->getManager();
		
		$sql = "SELECT count(id) as qte, avg(indicator1) as indicator1_avg, avg(indicator2) as indicator2_avg, avg(indicator3) as indicator3_avg, avg(indicator4) as indicator4_avg, avg(indicator5) as indicator5_avg, avg(indicator6) as indicator6_avg, avg(indicator7) as indicator7_avg, avg(indicator8) as indicator8_avg, avg(indicator9) as indicator9_avg, avg(indicator10) as indicator10_avg, avg(indicator11) as indicator11_avg, avg(indicator12) as indicator12_avg, avg(indicator13) as indicator13_avg, avg(indicator14) as indicator14_avg, avg(indicator15) as indicator15_avg FROM cross_indicator_article";
		
		$stmt = $em->getConnection()->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll();
		
		if ($rows) {
			$row = $rows[0];
			
			$qte = $row['qte'];
			
			$inficator1 = $row['indicator1_avg'];
			$inficator2 = $row['indicator2_avg'];
			$inficator3 = $row['indicator3_avg'];
			$inficator4 = $row['indicator4_avg'];
			$inficator5 = $row['indicator5_avg'];
			$inficator6 = $row['indicator6_avg'];
			$inficator7 = $row['indicator7_avg'];
			$inficator8 = $row['indicator8_avg'];
			$inficator9 = $row['indicator9_avg'];
			$inficator10 = $row['indicator10_avg'];
			$inficator11 = $row['indicator11_avg'];
			$inficator12 = $row['indicator12_avg'];
			$inficator13 = $row['indicator13_avg'];
			$inficator14 = $row['indicator14_avg'];
			$inficator15 = $row['indicator15_avg'];
			
			// On stock les valeur
			$paramsMgr->setValue('crossindicator.indicator1_avg', $inficator1);
			$paramsMgr->setValue('crossindicator.indicator2_avg', $inficator2);
			$paramsMgr->setValue('crossindicator.indicator3_avg', $inficator3);
			$paramsMgr->setValue('crossindicator.indicator4_avg', $inficator4);
			$paramsMgr->setValue('crossindicator.indicator5_avg', $inficator5);
			$paramsMgr->setValue('crossindicator.indicator6_avg', $inficator6);
			$paramsMgr->setValue('crossindicator.indicator7_avg', $inficator7);
			$paramsMgr->setValue('crossindicator.indicator8_avg', $inficator8);
			$paramsMgr->setValue('crossindicator.indicator9_avg', $inficator9);
			$paramsMgr->setValue('crossindicator.indicator10_avg', $inficator10);
			$paramsMgr->setValue('crossindicator.indicator11_avg', $inficator11);
			$paramsMgr->setValue('crossindicator.indicator12_avg', $inficator12);
			$paramsMgr->setValue('crossindicator.indicator13_avg', $inficator13);
			$paramsMgr->setValue('crossindicator.indicator14_avg', $inficator14);
			$paramsMgr->setValue('crossindicator.indicator15_avg', $inficator15);			
			
		}
		
		$paramsMgr->flush();
	}
	
	public function getAvgIndicatorView($id) {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return $paramsMgr->getValue('crossindicator.'.$id.'_avg');
	}
}

?>
