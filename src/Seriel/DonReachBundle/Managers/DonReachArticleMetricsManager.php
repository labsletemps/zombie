<?php

namespace Seriel\DonReachBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use ZombieBundle\Managers\Params\ParametersManager;
use Seriel\DonReachBundle\Entity\DonReachArticleMetrics;

class DonReachArticleMetricsManager extends SerielManager
{
	public function getObjectClass() {
		return 'Seriel\DonReachBundle\Entity\DonReachArticleMetrics';
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
	 * @return DonReachArticleMetrics
	 */
	public function getDonReachArticleMetrics($id) {
		return $this->get($id);
	}
	
	/**
	 * @return DonReachArticleMetrics
	 */
	public function getDonReachArticleMetricsForArticleId($article_id) {
		if (!$article_id) return null;
		if (is_array($article_id)) return null;
		
		return $this->query(array('article' => $article_id), array('one' => true));
	}
	
	/**
	 * @return DonReachArticleMetrics[]
	 */
	public function getAllDonReachArticleMetrics($options = array()) {
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
		
		$sql = "SELECT count(id) as qte, avg(buffer) as buffer, avg(facebook) as facebook, avg(fancy) as fancy, avg(google) as google, avg(hackernews) as hackernews, avg(hatena) as hatena, avg(linkedin) as linkedin, avg(mailru) as mailru, avg(odnoklassniki) as odnoklassniki, avg(pinterest) as pinterest, avg(pocket) as pocket, avg(reddit) as reddit, avg(scoopit) as scoopit, avg(stumbleupon) as stumbleupon, avg(tumblr) as tumblr, avg(twitter) as twitter, avg(vk) as vk, avg(weibo) as weibo, avg(xing) as xing, avg(yummly) as yummly FROM donreach_article_metrics";
		
		$stmt = $em->getConnection()->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll();
	
		if ($rows) {
			$row = $rows[0];
				
			$qte = $row['qte'];
				
			$buffer = round($row['buffer']);
			$facebook = round($row['facebook']);
			$fancy = round($row['fancy']);
			$google = round($row['google']);
			$hackernews = round($row['hackernews']);
			$hatena = round($row['hatena']);
			$linkedin = round($row['linkedin']);
			$mailru = round($row['mailru']);
			$odnoklassniki = round($row['odnoklassniki']);
			$pinterest = round($row['pinterest']);
			$pocket = round($row['pocket']);
			$reddit = round($row['reddit']);
			$scoopit = round($row['scoopit']);
			$stumbleupon = round($row['stumbleupon']);
			$tumblr = round($row['tumblr']);
			$twitter = round($row['twitter']);
			$vk = round($row['vk']);
			$weibo = round($row['weibo']);
			$xing = round($row['xing']);
			$yummly = round($row['yummly']);
				
			// save value
				
			$paramsMgr->setValue('donreach.qte', $qte);
				
			$paramsMgr->setValue('donreach.buffer', $buffer);
			$paramsMgr->setValue('donreach.facebook', $facebook);
			$paramsMgr->setValue('donreach.fancy', $fancy);
			$paramsMgr->setValue('donreach.google', $google);
			$paramsMgr->setValue('donreach.hackernews', $hackernews);
			$paramsMgr->setValue('donreach.hatena', $hatena);
			$paramsMgr->setValue('donreach.linkedin', $linkedin);
			$paramsMgr->setValue('donreach.mailru', $mailru);
			$paramsMgr->setValue('donreach.odnoklassniki', $odnoklassniki);
			$paramsMgr->setValue('donreach.pinterest', $pinterest);
			$paramsMgr->setValue('donreach.pocket', $pocket);
			$paramsMgr->setValue('donreach.reddit', $reddit);
			$paramsMgr->setValue('donreach.scoopit', $scoopit);
			$paramsMgr->setValue('donreach.stumbleupon', $stumbleupon);
			$paramsMgr->setValue('donreach.tumblr', $tumblr);
			$paramsMgr->setValue('donreach.twitter', $twitter);
			$paramsMgr->setValue('donreach.vk', $vk);
			$paramsMgr->setValue('donreach.weibo', $weibo);
			$paramsMgr->setValue('donreach.xing', $xing);
			$paramsMgr->setValue('donreach.yummly', $yummly);
		}
	
		$paramsMgr->flush();
	}
	
	public function getGeneralMoy($type) {
		$paramsMgr = $this->container->get('parameters_manager');
		if (false) $paramsMgr = new ParametersManager();
		
		return intval($paramsMgr->getValue('donreach.'.$type));
	}
	
}

?>
