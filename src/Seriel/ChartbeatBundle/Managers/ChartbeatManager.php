<?php

namespace Seriel\ChartbeatBundle\Managers;

use ZombieBundle\Entity\News\Article;
use Seriel\ChartbeatBundle\Entity\ChartbeatArticleDayReport;
use ZombieBundle\API\Managers\ManagerMetrics;
use ZombieBundle\API\Managers\ManagerStateData;
use ZombieBundle\API\Entity\StateImport;

class ChartbeatManager implements ManagerMetrics, ManagerStateData{

	const LIVE_TOPPAGES = 'toppages/v3/';
	const LIVE_QUICKSTATS = 'quickstats/v4/';

	const REPORT_SUBMIT = 'submit/page/';
	const REPORT_FETCH = 'fetch/';

	protected $baseurl = "https://api.chartbeat.com/";

	protected $live = 'live/';
	protected $histo = 'histo/';
	protected $report = 'query/v2/';

	protected $container = null;
	protected $logger = null;
	protected $chartbeat_params = null;

	public function __construct($container, $logger, $params) {
		$this->container = $container;
		$this->logger = $logger;

		$this->chartbeat_params = $params;
	}

	public function getNbDaysBeforeEvergreen() {
		// TODO : parametrable
		return 3;
	}

	protected function query($url, $parameters = array()) {
		if (!$url) {
			return false;
		}

		$parameters['apikey'] = $this->chartbeat_params['apikey'];
		$parameters['host'] = $this->chartbeat_params['host'];

		$tmp_params = array();
		foreach ($parameters as $key => $value) {
			$tmp_params[] = "$key=$value";
		}
		$params_str = implode('&', $tmp_params);

		//$url .= '?apikey='.$this->chartbeat_params['apikey'].'&host='.$this->chartbeat_params['host'];
		$url .= '?'.$params_str;

		echo "URL : ".$this->baseurl.$url."\n";

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

		$ch = curl_init($this->baseurl.$url);

		curl_setopt_array($ch, $options);

		$datas = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		return array('httpcode' => $httpcode, 'datas' => $datas);
	}

	public function test() {
		$res = $this->query($this->live.self::LIVE_TOPPAGES);
		$httpcode = intval($res['httpcode']);
		$datas = $res['datas'];

		if ($httpcode == 200) {
			// GOOD.
			$decoded = json_decode($datas);

			print_r($decoded);

			echo "\n";
		}

	}

	public function getReportForDay($day) {
		if (!$day) return null;
		$formatDate= explode('-', $day);
		if (count($formatDate) != 3) return null;
		if (!checkdate ($formatDate[1], $formatDate[2], $formatDate[0])) return null;
		$parameters = array();

		$parameters['date_range'] = 'day';

		$parameters['metrics'] = 'page_avg_time,page_avg_scroll,page_scroll_starts,page_total_time,page_uniques,page_views,page_views_loyal,page_views_quality';
		$parameters['dimensions'] = 'path';
		$parameters['sort_columns'] = 'page_uniques';
		$parameters['sort_order'] = 'desc';

		$parameters['start'] = $day;
		$parameters['end'] = $day;

		$result = $this->query($this->report.self::REPORT_SUBMIT, $parameters);
		$httpcode = intval($result['httpcode']);

		if ($httpcode != 200) {
			return false;
		}

		$datas = $result['datas'];
		$infos = json_decode($datas);
		if (isset($infos->{'error'})) return null;
		$query_id = $infos->query_id;

		// Let's give it some time.
		sleep(7);

		$result = $this->query($this->report.self::REPORT_FETCH, array('query_id' => $query_id));
		$httpcode = intval($result['httpcode']);

		$datas = $result['datas'];

		if ($httpcode != 200) {
			echo "HTTPCODE : $httpcode\n";
			echo "DATAS : $datas\n";
			return false;
		}

		return $datas;
	}

	// return view of subpage history of article
	public function articleHistory($article) {
		if (!$article) return '';
		if (false) $article = new Article();

		$templating = $this->container->get('templating');

		$cbadrMgr = $this->container->get('seriel_chartbeat.cbadr_manager');
		if (false) $cbadrMgr = new ChartbeatArticleDayReportManager();

		// Let's get the elements for this article.

		$paths = $article->getUris();
		if (!$paths) return '';

		$cbadrs = $cbadrMgr->getAllChartbeatArticleDayReportForPath($paths, array('result_type' => 'search_object', 'orderBy' => array('day' => 'asc')));
		$datas = $cbadrs->getResult();

		$graphPagesUniques = array();
		if ($datas) {
			foreach($datas as $cbadr) {
				if (false) $cbadr = new ChartbeatArticleDayReport();
				$graphPagesUniques[] = array('day' => $cbadr->getDay()->format('Y-m-d'), 'pages_uniques' => $cbadr->getPageUniques());
			}
		}

		return $templating->render('SerielChartbeatBundle:Article:article_historique.html.twig', array('article' => $article, 'cbadrs' => $cbadrs, 'graphPagesUniques' => $graphPagesUniques));
	}

	// return all metrics Chartbeat of for article
	public function getMetricsObjectForArticle($article) {
		$article_id = null;
		if ($article instanceof Article) {
			$article_id = $article->getId();
		} else {
			$article_id = $article;
			$article = null;
		}

		$artMetricsMgr = $this->container->get('seriel_chartbeat.article_metrics_manager');
		if (false) $artMetricsMgr = new ChartbeatArticleMetricsManager();

		$artMetrics = $artMetricsMgr->getChartbeatArticleMetricsForArticleId($article_id);

		return $artMetrics;
	}

	// return check list of import and calculate data for Chartbeat
	public function getStateImports() {
		$stateInports = array();
		
		$cbadrMgr = $this->container->get('seriel_chartbeat.cbadr_manager');
		if (false) $cbadrMgr = new ChartbeatArticleDayReportManager();
		$date = $cbadrMgr->getLastCreatedAt();
		if (isset($date)) {
			$stateInports[] = New StateImport('Chartbeat - Dernière mesures', $date);
		}	
		
		$artMetricsMgr = $this->container->get('seriel_chartbeat.article_metrics_manager');
		if (false) $artMetricsMgr = new ChartbeatArticleMetricsManager();
		$date= $artMetricsMgr->getLastDateCalcul();
		if (isset($date)) {
			$stateInports[] = New StateImport('Chartbeat - Dernier calcul', $date);
		}
		return $stateInports;
	}
	
	// return label of indicator generique in parameter
	public function getLabelIndicator($idIndicator) {

		if (isset($this->chartbeat_params[$idIndicator])) {
			return $this->chartbeat_params[$idIndicator];
		}
		return $idIndicator;
	}
}
