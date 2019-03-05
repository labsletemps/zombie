<?php

namespace Seriel\DonReachBundle\Managers;

use ZombieBundle\Entity\News\Article;
use ZombieBundle\API\Managers\ManagerMetrics;
use ZombieBundle\API\Managers\ManagerStateData;
use ZombieBundle\API\Entity\StateImport;

class DonReachManager implements ManagerMetrics, ManagerStateData{
	
	protected $baseurl = "https://count.donreach.com/?providers=all&url=";
	
	protected $container = null;
	protected $logger = null;
	protected $donreach_params = null;
	
	public function __construct($container, $logger, $params) {
		$this->container = $container;
		$this->logger = $logger;
		
		$this->donreach_params= $params;
	}
	
	protected function query($article_url, $parameters = array()) {
		if (!$article_url) {
			return false;
		}
		
		$url = $this->baseurl.urlencode($article_url);
		
		echo "URL : ".$url."\n";
		
		$options = array(
				CURLOPT_RETURNTRANSFER => true,     // return web page
				CURLOPT_HEADER         => false,    // don't return headers
				CURLOPT_FOLLOWLOCATION => true,     // follow redirects
				CURLOPT_ENCODING       => "",       // handle all encodings
				CURLOPT_USERAGENT      => "letemps-ch-zombie", // who am i
				CURLOPT_AUTOREFERER    => true,     // set referer on redirect
				CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
				CURLOPT_TIMEOUT        => 120,      // timeout on response
				CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
		);
		
		$ch = curl_init($url);
		
		curl_setopt_array($ch, $options);

		$datas = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		return array('httpcode' => $httpcode, 'datas' => $datas);
	}
	
	public function getDatasForArticleUrl($article_url) {
		if (!$article_url) return false;
		
		$res = $this->query($article_url);
		
		$httpcode = intval($res['httpcode']);
		$datas = $res['datas'];
		
		if ($httpcode == 200) {
			// GOOD.
			$decoded = json_decode($datas);
			
			return $decoded;
		}
		
		return false;
	}
	
	public function getDatasForArticle(Article $article) {
		if (!$article) return false;
		
		$urls = $article->getUrls();
		
		if (!$urls) return false;
		
		$res = array();
		foreach ($urls as $url) {
			$datas = $this->getDatasForArticleUrl($url);
			
			if ($datas) {
				foreach ($datas->shares as $key => $val) {
					if (!isset($res[$key])) $res[$key] = 0;
					$res[$key] += intval($val);
				}
			}
		}
		
		$res['day'] = new \DateTime();
		
		return $res;
		
	}
	
	// return all metrics Donreach of for article
	public function getMetricsObjectForArticle($article) {
		$article_id = null;
		if ($article instanceof Article) {
			$article_id = $article->getId();
		} else {
			$article_id = $article;
			$article = null;
		}
		
		$artMetricsMgr = $this->container->get('seriel_donreach.article_metrics_manager');
		if (false) $artMetricsMgr = new DonReachArticleMetricsManager();
		
		$artMetrics = $artMetricsMgr->getDonReachArticleMetricsForArticleId($article_id);
		
		return $artMetrics;
	}
	
	// return view of subpage history of article
	public function articleHistory($article) {
		if (!$article) return '';
		if (false) $article = new Article();
		
		$templating = $this->container->get('templating');
		
		$dramMgr = $this->container->get('seriel_donreach.article_metrics_manager');
    	if (false) $dramMgr = new DonReachArticleMetricsManager();
    	
    	$dram = $dramMgr->getDonReachArticleMetricsForArticleId($article->getId());

		
    	$values = array();
    	if ($dram) {
    		$values = $dram->getValues();
    		if ($values) {
    			foreach ($values as $key => $datas) {
    				$average = $dramMgr->getGeneralMoy($key);
    				$values[$key]['average'] = $average;
    			}
    		}
    	}
    	
    	
		
		return $templating->render('SerielDonReachBundle:Article:article_historique.html.twig', array('article' => $article, 'dram' => $dram, 'values' => $values));
	}
	// return check list of import and calculate data for DonReach
	public function getStateImports() {
		$stateInports = array();
		
		$dramMgr = $this->container->get('seriel_donreach.article_metrics_manager');
		if (false) $dramMgr = new DonReachArticleMetricsManager();
		
		$date= $dramMgr->getLastDateCalcul();
		if (isset($date)) {
			$stateInports[] = New StateImport('DonReach - Dernier calcul', $date);
		}
		return $stateInports;
	}
	
	// return label of indicator generique in parameter
	public function getLabelIndicator($idIndicator) {
		
		if (isset($this->donreach_params[$idIndicator])) {
			return $this->donreach_params[$idIndicator];
		}
		return $idIndicator;
	}
}