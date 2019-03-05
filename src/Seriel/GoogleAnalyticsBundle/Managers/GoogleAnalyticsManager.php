<?php

namespace Seriel\GoogleAnalyticsBundle\Managers;

use ZombieBundle\Entity\News\Article;
use Google_Client;
use Google_Service_Analytics;
use Symfony\Component\Validator\Constraints\DateTime;
use Seriel\GoogleAnalyticsBundle\Entity\GoogleAnalyticsDayReport;
use Seriel\GoogleAnalyticsBundle\Entity\GoogleAnalyticsArticleMetrics;
use Seriel\AppliToolboxBundle\Utils\StringUtils;
use ZombieBundle\API\Managers\ManagerMetrics;
use Seriel\GoogleAnalyticsBundle\Entity\DayReportEntrance;
use ZombieBundle\API\Managers\ManagerStateData;
use ZombieBundle\API\Entity\StateImport;

class GoogleAnalyticsManager implements ManagerMetrics, ManagerStateData{
	
	const MaxNBresult = 10000;
	protected $container = null;
	protected $logger = null;
	protected $client = null;
	protected $analytics= null;
	protected $stringUtil= null;
	public $filekey= null;
	public $idview= null;
	public $UrisStopPath = array();
	public $UrisAbonnement= array();
	public $UrisStopEntrance= array();
	public $dimensionSubscription;
	public $subscriberValue = array();
	
	public function __construct($container, $logger, $filekey, $idview) {
		$this->container = $container;
		$this->logger = $logger;
		$this->filekey = $filekey;
		$this->idview = $idview;
		
		//Connexion Google
		$this->client = new Google_Client();
		$this->client->setAuthConfig($this->filekey);
		$this->client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);
		
		// create service google analytics
		$this->analytics = new Google_Service_Analytics($this->client);
		
		//getParameters
		$this->UrisStopPath = $this->container->getParameter('google.analytics.uri.stopPath');
		$this->UrisAbonnement= $this->container->getParameter('google.analytics.uri.abonnement');
		$this->UrisStopEntrance= $this->container->getParameter('google.analytics.uri.stopEntrance');
		$this->dimensionSubscription= $this->container->getParameter('google.analytics.dimension.subscription');
		$this->subscriberValue= $this->container->getParameter('google.analytics.dimension.subscription.subscriber');		
		//utils
		$this->stringUtil = new StringUtils();
		
	}
	public function getSession(\DateTime $startDate=null, \DateTime $endDate=null){
		
		//if stardate is null => day-7
		if ($startDate == null) {
			$paramStartDate = '7daysAgo';
		}
		else {
			$paramStartDate = $startDate->format('Y-m-d');
		}
		//if stardate is null => Today
		if ($endDate== null) {
			$paramEndDate = 'today';
		}
		else {
			$paramEndDate= $endDate->format('Y-m-d');
		}
		$results = $this->analytics->data_ga->get('ga:'. $this->idview,$paramStartDate,$paramEndDate,'ga:sessions');
	
		if (count($results->getRows()) > 0) {
			// Get the entry for the first entry in the first row.
			$rows = $results->getRows();
			$sessions = $rows[0][0];
		}
		else {
			$sessions= 0;
		}
		return $sessions;
	}
	
	//generate request with all option and return all resultat
	public function execute($startDate,$endDate,$metric,$optParams){
		
		// Limit of google API of 10000 row by request see https://developers.google.com/analytics/devguides/reporting/core/v3/reference#maxResults
		$index = 1;
		$allResult = array();
		$optParams['max-results'] = self::MaxNBresult;
		$NBresult = self::MaxNBresult;
		while ($NBresult == self::MaxNBresult) {
			$optParams['start-index'] = $index;
			$results = $this->analytics->data_ga->get('ga:'. $this->idview,$startDate,$endDate,$metric,$optParams);
			if ($results->getRows() == null) {
				$results = array();
			}
			else {
				$results = $results->getRows();
			}
			$allResult = array_merge($allResult, $results);
			$NBresult = count($results);
			$index += $NBresult;
		}
		
		return $allResult;
		
	}
	// return pageviews between two date and filter (regular expression)
	public function getPageViews(\DateTime $startDate=null, \DateTime $endDate=null, $filterPage){
		
		//if stardate is null => day-7
		if ($startDate == null) {
			$paramStartDate = '7daysAgo';
		}
		else {
			$paramStartDate = $startDate->format('Y-m-d');
		}
		//if stardate is null => Today
		if ($endDate== null) {
			$paramEndDate = 'today';
		}
		else {
			$paramEndDate= $endDate->format('Y-m-d');
		}
		$optParams = array('dimensions'=> 'ga:pagePath');
		if ($filterPage != null) {
			//exemple 'ga:pagePath==/home'.
			$optParams['filters'] = $filterPage;
		}
		
		return $this->execute($paramStartDate,$paramEndDate,'ga:pageviews',$optParams);
		
	}
	
	// return uniquePageviews between two date and filter (regular expression) 
	public function getUniquePageViews(\DateTime $startDate=null, \DateTime $endDate=null, $filterPage){
		
		//if stardate is null => day-7
		if ($startDate == null) {
			$paramStartDate = '7daysAgo';
		}
		else {
			$paramStartDate = $startDate->format('Y-m-d');
		}
		//if stardate is null => Today
		if ($endDate== null) {
			$paramEndDate = 'today';
		}
		else {
			$paramEndDate= $endDate->format('Y-m-d');
		}
		$optParams = array('dimensions'=> 'ga:pagePath');
		if ($filterPage != null) {
			//example 'ga:pagePath==/home'.
			$optParams['filters'] = $filterPage;
		}
		
		return $this->execute($paramStartDate,$paramEndDate,'ga:uniquePageviews',$optParams);

	}
	
	// return TimeOnPage between two date and filter (regular expression)
	public function getTimeOnPage(\DateTime $startDate=null, \DateTime $endDate=null, $filterPage){
		
		//if stardate is null => day-7
		if ($startDate == null) {
			$paramStartDate = '7daysAgo';
		}
		else {
			$paramStartDate = $startDate->format('Y-m-d');
		}
		//if stardate is null => Today
		if ($endDate== null) {
			$paramEndDate = 'today';
		}
		else {
			$paramEndDate= $endDate->format('Y-m-d');
		}
		$optParams = array('dimensions'=> 'ga:pagePath');
		if ($filterPage != null) {
			$optParams['filters'] = $filterPage;
		}
		
		return $this->execute($paramStartDate,$paramEndDate,'ga:TimeOnPage',$optParams);
		
	}
	
	// return entrance for site between two date and filter (regular expression)
	public function getEntranceonPage(\DateTime $startDate=null, \DateTime $endDate=null, $filterPage, $filterSource){
		
		//if stardate is null => day-7
		if ($startDate == null) {
			$paramStartDate = '7daysAgo';
		}
		else {
			$paramStartDate = $startDate->format('Y-m-d');
		}
		//if stardate is null => Today
		if ($endDate== null) {
			$paramEndDate = 'today';
		}
		else {
			$paramEndDate= $endDate->format('Y-m-d');
		}
		$optParams = array('dimensions'=> 'ga:fullReferrer,ga:pagePath');
		$filters = array();
		//$filters[] = 'ga:source!=(direct)';
		if ($filterSource!= null) {
			$filters[]= $filterSource;
		}
		if ($filterPage!= null) {
			$filters[]=  $filterPage;
		}
		if (count($filters)> 0) {
			$optParams['filters'] = implode(';', $filters);
		}
		
		return $this->execute($paramStartDate,$paramEndDate,'ga:entrances',$optParams);

		
	}
	
	// return number of exits site between two date and filter (regular expression)
	public function getExitPage(\DateTime $startDate=null, \DateTime $endDate=null, $filterPage){
		
		//if stardate is null => day-7
		if ($startDate == null) {
			$paramStartDate = '7daysAgo';
		}
		else {
			$paramStartDate = $startDate->format('Y-m-d');
		}
		//if stardate is null => Today
		if ($endDate== null) {
			$paramEndDate = 'today';
		}
		else {
			$paramEndDate= $endDate->format('Y-m-d');
		}
		$optParams = array('dimensions'=> 'ga:pagePath');
		$filters = array();
		if ($filterPage!= null) {
			$filters[]=  $filterPage;
		}
		if (count($filters)> 0) {
			$optParams['filters'] = implode(';', $filters);
		}
		
		return $this->execute($paramStartDate,$paramEndDate,'ga:exits',$optParams);
	
	}
	
	// return NB link for naviguation between 2 pages.between two date. filterPageSource and filterPageTarget are regular expression
	public function getLinkNavigationPage(\DateTime $startDate=null, \DateTime $endDate=null, $filterPageSource, $filterPageTarget){
		
		//if stardate is null => day-7
		if ($startDate == null) {
			$paramStartDate = '7daysAgo';
		}
		else {
			$paramStartDate = $startDate->format('Y-m-d');
		}
		//if stardate is null => Today
		if ($endDate== null) {
			$paramEndDate = 'today';
		}
		else {
			$paramEndDate= $endDate->format('Y-m-d');
		}
		$optParams = array('dimensions'=> 'ga:previousPagePath,ga:pagePath');
		$filters = array();
		
		if ($filterPageSource!= null) {
			$filters[]= $filterPageSource;
		}
		if ($filterPageTarget!= null) {
			$filters[]= $filterPageTarget;
		}
		if (count($filters)> 0) {
			$optParams['filters'] = implode(';', $filters);
		}

		return $this->execute($paramStartDate,$paramEndDate,'ga:pageviews',$optParams);

	}
	
	// calculate DayReport with google analytics api between two Date ( return false if error)
	public function calculateDayReport(\DateTime $startDate, \DateTime $endDate=null){
		if ($startDate == null) {
			return false;
		}
		if ($endDate== null) {
			$endDate= new \DateTime();
		}

		$GADayReportMgr = $this->container->get('seriel_google_analytics.day_report_manager');
		if (false) $GADayReportMgr= new GoogleAnalyticsDayReportManager();
		$GADayReportEntranceMgr =$this->container->get('seriel_google_analytics.day_report_entrance_manager');
		if (false) $GADayReportEntranceMgr= new DayReportEntranceManager();
		
		//One calcul per day
		$intervalDay = $startDate->diff($endDate)->format('%a') +1;
		$dateCurrent = $startDate;
		for ($iterator = 1;$iterator<= $intervalDay; $iterator++) {
			$mapPath = array();
			$datedisplay = $dateCurrent->format('Y-m-d');
			echo 'Calcul for '.$datedisplay. PHP_EOL;
			
			
			// Measure pageviewmeasure
			if(count($this->UrisStopPath)> 0) {
				$filterPage = implode(';ga:pagePath!~', $this->UrisStopPath);
				$filterPage = 'ga:pagePath!~'.$filterPage;
			}else{
				$filterPage ='';
			}
			// all user
			$ArrayPageValue= $this->getPageViews($dateCurrent, $dateCurrent, $filterPage);
			foreach ($ArrayPageValue as $PageValue){
				$url = $this->cleanUrl($PageValue[0]);
				//if day report in list pre-save $mapPath
				if(isset($mapPath[$url])) {
					$mapPath[$url]->addPageview($PageValue[1]);
					$mapPath[$url]->calculPageviewVisitor();

				}else {
					$dayreport = new GoogleAnalyticsDayReport();
					$dayreport->setDay($dateCurrent);
					$dayreport->setPath($url);
					$dayreport->setPageview($PageValue[1]);
					$dayreport->calculPageviewVisitor();
					$mapPath[$url] = $dayreport;
				}
			}
			// subscriber
			$filterPage = $this->addFilterSubscriber($filterPage);
			$ArrayPageValue= $this->getPageViews($dateCurrent, $dateCurrent, $filterPage);
			foreach ($ArrayPageValue as $PageValue){
				$url = $this->cleanUrl($PageValue[0]);
				//if day report in list pre-save $mapPath
				if(isset($mapPath[$url])) {
					$mapPath[$url]->addPageviewSubscriber($PageValue[1]);
					$mapPath[$url]->calculPageviewVisitor();
					
				}else {
					$dayreport = new GoogleAnalyticsDayReport();
					$dayreport->setDay($dateCurrent);
					$dayreport->setPath($url);
					$dayreport->setPageviewSubscriber($PageValue[1]);
					$dayreport->calculPageviewVisitor();
					$mapPath[$url] = $dayreport;
				}
			}
			
			
			// Measure uniquepageviewmeasure
			if(count($this->UrisStopPath)> 0) {
				$filterPage = implode(';ga:pagePath!~', $this->UrisStopPath);
				$filterPage = 'ga:pagePath!~'.$filterPage;
			}else{
				$filterPage ='';
			}
			// all user
			$ArrayPageValue= $this->getUniquePageViews($dateCurrent, $dateCurrent, $filterPage);
			foreach ($ArrayPageValue as $PageValue){
				$url = $this->cleanUrl($PageValue[0]);
				//if day report in list pre-save $mapPath
				if(isset($mapPath[$url])) {
					$mapPath[$url]->addUniquepageview($PageValue[1]);
					$mapPath[$url]->calculUniquepageviewVisitor();
				}else {
					$dayreport = new GoogleAnalyticsDayReport();
					$dayreport->setDay($dateCurrent);
					$dayreport->setPath($url);
					$dayreport->setUniquepageview($PageValue[1]);
					$dayreport->calculUniquepageviewVisitor();
					$mapPath[$url] = $dayreport;
				}
			}
			// subscriber
			$filterPage = $this->addFilterSubscriber($filterPage);
			$ArrayPageValue= $this->getUniquePageViews($dateCurrent, $dateCurrent, $filterPage);
			foreach ($ArrayPageValue as $PageValue){
				$url = $this->cleanUrl($PageValue[0]);
				//if day report in list pre-save $mapPath
				if(isset($mapPath[$url])) {
					$mapPath[$url]->addUniquepageviewSubscriber($PageValue[1]);
					$mapPath[$url]->calculUniquepageviewVisitor();
				}else {
					$dayreport = new GoogleAnalyticsDayReport();
					$dayreport->setDay($dateCurrent);
					$dayreport->setPath($url);
					$dayreport->setUniquepageviewSubscriber($PageValue[1]);
					$dayreport->calculUniquepageviewVisitor();
					$mapPath[$url] = $dayreport;
				}
			}
			
			// Measure readtimeMeasure
			if(count($this->UrisStopPath)> 0) {
				$filterPage = implode(';ga:pagePath!~', $this->UrisStopPath);
				$filterPage = 'ga:pagePath!~'.$filterPage;
			}else{
				$filterPage ='';
			}
			// all user
			$ArrayPageValue= $this->getTimeOnPage($dateCurrent, $dateCurrent, $filterPage);
			foreach ($ArrayPageValue as $PageValue){
				$url = $this->cleanUrl($PageValue[0]);
				//if day report in list pre-save $mapPath
				if(isset($mapPath[$url])) {
					$mapPath[$url]->addReadtime($PageValue[1]);
					$mapPath[$url]->calculReadtimeVisitor();
				}else {
					$dayreport = new GoogleAnalyticsDayReport();
					$dayreport->setDay($dateCurrent);
					$dayreport->setPath($url);
					$dayreport->setReadtime($PageValue[1]);
					$dayreport->calculReadtimeVisitor();
					$mapPath[$url] = $dayreport;
							
				}
			}
			// subscriber
			$filterPage = $this->addFilterSubscriber($filterPage);
			$ArrayPageValue= $this->getTimeOnPage($dateCurrent, $dateCurrent, $filterPage);
			foreach ($ArrayPageValue as $PageValue){
				$url = $this->cleanUrl($PageValue[0]);
				//if day report in list pre-save $mapPath
				if(isset($mapPath[$url])) {
					$mapPath[$url]->addReadtimeSubscriber($PageValue[1]);
					$mapPath[$url]->calculReadtimeVisitor();
				}else {
					$dayreport = new GoogleAnalyticsDayReport();
					$dayreport->setDay($dateCurrent);
					$dayreport->setPath($url);
					$dayreport->setReadtimeSubscriber($PageValue[1]);
					$dayreport->calculReadtimeVisitor();
					$mapPath[$url] = $dayreport;
					
				}
			}
			
			// Measure entranceMeasure
			if(count($this->UrisStopPath)> 0) {
				$filterPage = implode(';ga:pagePath!~', $this->UrisStopPath);
				$filterPage = 'ga:pagePath!~'.$filterPage;			
			}else{
				$filterPage ='';
			}
			if(count($this->UrisStopEntrance)> 0) {
				$filterSource= implode(';ga:source!~', $this->UrisStopEntrance);
				$filterSource= 'ga:source!~'.$filterSource;
			}else {
				$filterSource = '';
			}
			// all user
			$ArrayPageValue= $this->getEntranceonPage($dateCurrent, $dateCurrent, $filterPage,$filterSource);
			foreach ($ArrayPageValue as $PageValue){
				$url = $this->cleanUrl($PageValue[1]);
				$source = $this->cleanUrl($PageValue[0]);
				$dayReportEntrance = new DayReportEntrance();
				$dayReportEntrance->setDay($dateCurrent);
				$dayReportEntrance->setPath($source);
				$dayReportEntrance->setCount($PageValue[2]);
				//if day report in list pre-save $mapPath
				if(isset($mapPath[$url])) {
					$mapPath[$url]->addEntrance($PageValue[2]);
					$mapPath[$url]->calculEntranceVisitor();
					$mapPath[$url]->addSourceEntrance($dayReportEntrance);
				}else {
					$dayreport = new GoogleAnalyticsDayReport();
					$dayreport->setDay($dateCurrent);
					$dayreport->setPath($url);
					$dayreport->setEntrance($PageValue[2]);
					$dayreport->calculEntranceVisitor();
					$dayreport->addSourceEntrance($dayReportEntrance);
					$mapPath[$url] = $dayreport;
				}
			}
			
			// Measure exitpage
			if(count($this->UrisStopPath)> 0) {
				$filterPage = implode(';ga:pagePath!~', $this->UrisStopPath);
				$filterPage = 'ga:pagePath!~'.$filterPage;
			}else{
				$filterPage ='';
			}
			// all user
			$ArrayPageValue= $this->getExitPage($dateCurrent, $dateCurrent, $filterPage);
			foreach ($ArrayPageValue as $PageValue){
				$url = $this->cleanUrl($PageValue[0]);
				//if day report in list pre-save $mapPath
				if(isset($mapPath[$url])) {
					$mapPath[$url]->addExitpage($PageValue[1]);
				}else {
					$dayreport = new GoogleAnalyticsDayReport();
					$dayreport->setDay($dateCurrent);
					$dayreport->setPath($url);
					$dayreport->setExitpage($PageValue[1]);
					$mapPath[$url] = $dayreport;
					
				}
			}
			
			// subscriber
			$filterPage = $this->addFilterSubscriber($filterPage);
			$ArrayPageValue= $this->getEntranceonPage($dateCurrent, $dateCurrent, $filterPage,$filterSource);
			foreach ($ArrayPageValue as $PageValue){
				$url = $this->cleanUrl($PageValue[1]);
				//if day report in list pre-save $mapPath
				if(isset($mapPath[$url])) {
					$mapPath[$url]->addEntranceSubscriber($PageValue[2]);
					$mapPath[$url]->calculEntranceVisitor();
				}else {
					$dayreport = new GoogleAnalyticsDayReport();
					$dayreport->setDay($dateCurrent);
					$dayreport->setPath($url);
					$dayreport->setEntranceSubscriber($PageValue[2]);
					$dayreport->calculEntranceVisitor();
					$mapPath[$url] = $dayreport;
				}
			}
			
			// Measure subscriptionMeasure			
			if(count($this->UrisStopPath)> 0) {
				$filterPageSource = implode(';ga:previousPagePath!~', $this->UrisStopPath);
				$filterPageSource = 'ga:previousPagePath!~'.$filterPageSource;
				$filterPageSource= $filterPageSource.';ga:previousPagePath!=(entrance)';
			}else {
				$filterPageSource= 'ga:previousPagePath!=(entrance)';
			}
			if(count($this->UrisAbonnement)> 0) {
				$filterPageTarget = implode(',ga:pagePath=@', $this->UrisAbonnement);
				$filterPageTarget = 'ga:pagePath=@'.$filterPageTarget;
			}else {
				$filterPageTarget = '';
			}
			$ArrayPageValue= $this->getLinkNavigationPage($dateCurrent, $dateCurrent, $filterPageSource,$filterPageTarget);
			foreach ($ArrayPageValue as $PageValue){
				$url = $this->cleanUrl($PageValue[0]);

				//if day report in list pre-save $mapPath
				if(isset($mapPath[$url])) {
					$mapPath[$url]->addSubscription($PageValue[2]);
				}else {
					$dayreport = new GoogleAnalyticsDayReport();
					$dayreport->setDay($dateCurrent);
					$dayreport->setPath($url);
					$dayreport->setSubscription($PageValue[2]);
					$mapPath[$url] = $dayreport;
				}
			}
		
			
			//delete and save in database
			foreach ($mapPath as $dayreport) {
				$GADayReportMgr->save($dayreport);
			}
			$NbRec = count($mapPath);
			echo 'Number report: '.$NbRec. PHP_EOL;
			
			$GADayReportMgr->deleteGoogleAnalyticsDayReportByDate($dateCurrent->format('Y-m-d'),$dateCurrent->format('Y-m-d'));
			$GADayReportEntranceMgr->deleteDayReportEntranceByDate($dateCurrent->format('Y-m-d'),$dateCurrent->format('Y-m-d'));	
			$GADayReportMgr->flush();
			
			$dateCurrent = $dateCurrent->add(new \DateInterval('P1D'));
		}
		
		return true;
	}
	
	// calculate Metrics with google analytics day report
	public function calculateMetrics(\DateTime $startDate=null, \DateTime $endDate=null){
		
		$ArticleMgr = $this->container->get('articles_manager');
		if (false) $ArticleMgr= new ArticleManager();
		$GADayReportMgr = $this->container->get('seriel_google_analytics.day_report_manager');
		if (false) $GADayReportMgr= new GoogleAnalyticsDayReportManager();
		$GAMetricsMgr = $this->container->get('seriel_google_analytics.article_metrics_manager');
		if (false) $GAMetricsMgr= new GoogleAnalyticsArticleMetricsManager();
		
		if ($startDate == null and  $endDate== null) {
			//Calcul for all articles
			$articles  = $ArticleMgr->getAllArticles();
		} elseif ($endDate== null){
			//Calcul for day (monday, tuesdays, ...
			$articles  = $ArticleMgr->getAllArticlesForDay($startDate);
		} else {
			//Calcul for periode
			$articles  = $ArticleMgr->getAllArticlesForPeriode($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));
		}

		$Nbarticles = count($articles);
		$day = new \DateTime();
		$iterateur = 1;
		echo 'Number articles: '.$Nbarticles. PHP_EOL;
		foreach($articles as $article) {
			
			echo $iterateur.' / '.$Nbarticles. PHP_EOL;
			$paths = $article->getUris();
			// search if GoogleAnalyticsMetric in Database
			$articleMetrics = $GAMetricsMgr->getGoogleAnalyticsArticleMetricsForArticleId($article);
			if ( !isset($articleMetrics)) {
				$articleMetrics = new GoogleAnalyticsArticleMetrics();
			}
			//if article have uri
			if (count($paths)> 0) {
				$resultat  = $GADayReportMgr->getSumMeasureGoogleAnalyticsDayReportByPaths($paths);
				$resultat= $resultat[0];
				$articleMetrics->setArticle($article);
				$articleMetrics->setDateCalcul($day);
				$articleMetrics->setReadtimeMeasure($resultat['readtime']);
				$articleMetrics->setReadtimeSubscriberMeasure($resultat['readtime_subscriber']);
				$articleMetrics->setReadtimeVisitorMeasure($resultat['readtime_visitor']);
				$articleMetrics->setPageviewMeasure($resultat['pageview']);
				$articleMetrics->setPageviewSubscriberMeasure($resultat['pageview_subscriber']);
				$articleMetrics->setPageviewVisitorMeasure($resultat['pageview_visitor']);
				$articleMetrics->setUniquepageviewMeasure($resultat['uniquepageview']);
				$articleMetrics->setUniquepageviewSubscriberMeasure($resultat['uniquepageview_subscriber']);
				$articleMetrics->setUniquepageviewVisitorMeasure($resultat['uniquepageview_visitor']);
				$articleMetrics->setSubscriptionMeasure($resultat['subscription']);
				$articleMetrics->setEntranceMeasure($resultat['entrance']);
				$articleMetrics->setEntranceSubscriberMeasure($resultat['entrance_subscriber']);
				$articleMetrics->setEntranceVisitorMeasure($resultat['entrance_visitor']);
				$articleMetrics->setExitpageMeasure($resultat['exitpage']);
				
				
			}
			else {
				//if not uri in article
				$articleMetrics->setArticle($article);
				$articleMetrics->setDateCalcul($day);
			}
			$articleMetrics->preCalcul();
			$GAMetricsMgr->save($articleMetrics);
			$iterateur ++;
		}
		echo 'Save in database'. PHP_EOL;
		$GAMetricsMgr->flush();
		
		echo 'Calcul average'. PHP_EOL;
		$GAMetricsMgr->updateAverageGeneral();
		
		echo 'Calcul indicator'. PHP_EOL;
		/*
		$ListarticleMetrics = $GAMetricsMgr->getAllGoogleAnalyticsArticleMetrics();
		foreach ($ListarticleMetrics as $articleMetrics){
			$articleMetrics->calculIndicator();
			$GAMetricsMgr->save($articleMetrics);
		}
		*/
		foreach($articles as $article) {
			$articleMetrics = $GAMetricsMgr->getGoogleAnalyticsArticleMetricsForArticleId($article->getId());
			$articleMetrics->calculIndicator();
			$GAMetricsMgr->save($articleMetrics);
		}
		
		$GAMetricsMgr->flush();
		
		echo 'Calcul average'. PHP_EOL;
		$GAMetricsMgr->updateAverageGeneral();
		return true;
	}
	
	// return all metrics GoogleAnalytics of for article
	public function getMetricsObjectForArticle($article) {
		$article_id = null;
		if ($article instanceof Article) {
			$article_id = $article->getId();
		} else {
			$article_id = $article;
			$article = null;
		}
		
		$artMetricsMgr = $this->container->get('seriel_google_analytics.article_metrics_manager');
		if (false) $artMetricsMgr = new GoogleAnalyticsArticleMetricsManager();
		
		$artMetrics = $artMetricsMgr->getGoogleAnalyticsArticleMetricsForArticleId($article_id);
		
		return $artMetrics;
	}
	
	// remove special charactere
	public function cleanUrl($url) {
		$url = $this->stringUtil->removeAccents(stripcslashes(strtolower($url)));
		$url= iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $url);
		$url= trim($url);
		$url= strtolower($url);
		if (strlen($url)> 330) {
			$url = substr($url, 0, 330);
		}

		
		return $url;
	}
	
	// add filter Subscriber in filter
	public function addFilterSubscriber($filter) {
		if ($filter != '' ) {
			$filter.= ';';
		}
		$i = 0;
		$len = count($this->subscriberValue);
		foreach ($this->subscriberValue as $val) {
			if ($i == 0) {
				$filter.= $this->dimensionSubscription.'=='.$val;
			}else {
				$filter.= ','.$this->dimensionSubscription.'=='.$val;
			}
			$i++;
		}
		return $filter;
	}
	
	// return view of subpage history of article
	public function articleHistory($article) {
		if (!$article) return '';
		if (false) $article = new Article();
		
		$templating = $this->container->get('templating');
		
		$GADayReportMgr = $this->container->get('seriel_google_analytics.day_report_manager');
		if (false) $GADayReportMgr= new GoogleAnalyticsDayReportManager();
		
		$GADayReportEntranceMgr =$this->container->get('seriel_google_analytics.day_report_entrance_manager');
		if (false) $GADayReportEntranceMgr= new DayReportEntranceManager();
		
		// Let's get the elements for this article.
		
		$paths = $article->getUris();
		if (!$paths) return '';
		
		$dayReports = $GADayReportMgr->getGoogleAnalyticsDayReportByPaths($paths, array('result_type' => 'search_object', 'orderBy' => array('day' => 'asc')));
		$entranceReports = $GADayReportEntranceMgr->getDayReportEntranceByPaths($paths, array('result_type' => 'search_object', 'orderBy' => array('day' => 'asc')));
		
		$datas = $dayReports->getResult();
		$graphPagesUniques = array();
		if ($datas) {
			foreach($datas as $dayreport) {
				if (false) $dayreport= new GoogleAnalyticsDayReport();
				$graphPagesUniques[] = array('day' => $dayreport->getDay()->format('Y-m-d'), 'pages_uniques' => $dayreport->getUniquepageview());
			}
		}
		
		
		return $templating->render('SerielGoogleAnalyticsBundle:Article:article_historique.html.twig', array('article' => $article, 'dayReports' => $dayReports, 'entranceReports' => $entranceReports, 'graphPagesUniques' => $graphPagesUniques));
	}

	// return check list of import and calculate data for GoogleAnalytics
	public function getStateImports() {
		$stateInports = array();
		
		$GADayReportMgr = $this->container->get('seriel_google_analytics.day_report_manager');
		if (false) $GADayReportMgr= new GoogleAnalyticsDayReportManager();
		
		$date = $GADayReportMgr->getLastCreatedAt();
		if (isset($date)) {
			$stateInports[] = New StateImport('GoogleAnalytics - Dernière mesures', $date);
		}
		$artMetricsMgr = $this->container->get('seriel_google_analytics.article_metrics_manager');
		if (false) $artMetricsMgr = new GoogleAnalyticsArticleMetricsManager();
		
		$date= $artMetricsMgr->getLastDateCalcul();
		if (isset($date)) {
			$stateInports[] = New StateImport('GoogleAnalytics - Dernier calcul', $date);
		}
		return $stateInports;
	}
	
	// return label of indicator generique in parameter
	public function getLabelIndicator($idIndicator) {
		
		if ($this->container->hasparameter('google.analytics.label.'.$idIndicator)) {
			return $this->container->getParameter('google.analytics.label.'.$idIndicator);
		}
		return $idIndicator;
	}
}