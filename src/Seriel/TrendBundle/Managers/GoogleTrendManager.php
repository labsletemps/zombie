<?php

namespace Seriel\TrendBundle\Managers;

use GuzzleHttp\Client;
use ZombieBundle\API\Managers\TrendsModule;
use ZombieBundle\Utils\ZombieUtils;
use ZombieBundle\API\Managers\ManagerStateData;
use ZombieBundle\API\Entity\StateImport;

class GoogleTrendManager implements TrendsModule, ManagerStateData {

	const RISING_QUERIES = 'RISING_QUERIES_0_0';
	const TOP_QUERIES = 'TOP_QUERIES_0_0';
	
	const SEARCH_URL = 'http://www.google.com/trends/fetchComponent';
	const TRENDS_URL = 'http://www.google.com/trends/explore';
	const HOTTRENDS_URL = 'http://www.google.com/trends/hottrends/hotItems';
	
	protected static $module_name = 'google_news';
	protected static $module_descirption = "This module as been designed regarding Zombie Projet's Trends API\n."
			."It uses Google News to get trending topics.";
		
	private $location;
	private $codelocation;
	private $language;
	private $category;
	private $monthInterval;
	private $lastDays;
	private $wordsearch;

	private $container = null;
	private $logger = null;


	public function __construct($container, $logger) {
		$this->container = $container;
		$this->logger = $logger;
		
		$this->category= '';
		$this->location=  $this->container->getParameter('trend.google.location'); 
		$this->codelocation =  $this->container->getParameter('trend.google.codelocation'); 
		$this->language= $this->container->getParameter('trend.google.language'); 
		$this->setMonthInterval((new \DateTime('now'))->modify('-12 months'), new \DateTime('now'));
		$this->wordsearch= [];
	}

	public function getName() { return self::$module_name; }
	public function getDescription() { return self::$module_descirption; }
	
	/**
	 * @return string
	 */
	private function prepareSearchUrl()
	{
		return self::SEARCH_URL .
		'?hl=' . $this->language .
		'&cat=' . $this->category .
		'&geo=' . $this->location .
		'&q=' . implode(',+', $this->wordsearch) .
		'&cid=' . self::TOP_QUERIES.
		'&date=' . ($this->lastDays ?: $this->monthInterval) .
		'&cmpt=q&content=1&export=3';
	}
	
	/**
	 * Construct url hacking for google hot trends
	 * @return string
	 */
	public function prepareHotTrendsUrl($date)
	{
		if ( ! isset($date) OR  $date == '' ) return '';
		return self::HOTTRENDS_URL.
		'?ajax=1&htd=' . $date.
		'&pn=' . $this->codelocation .'&htv=l';
	}
	
	// return json response of google got trends
	public function getHotTrends($date) {
		
		$url = $this->prepareHotTrendsUrl($date);
		//echo "URL : ".$url."\n";
		
		$client = new Client();
		try {
			$response= $client->request('GET', $url);
		} catch (Exception $e) {
			echo 'HotTrend Exception : ',  $e->getMessage(), "\n";
		}	
		if ($response->getStatusCode()== 200) {	
			$responseDecoded = json_decode($response->getBody(), true);
			return $responseDecoded;
		}
		else {
			echo 'HotTrend Exception with  '.$url."\n";
			return false;
		}

	}
	
	//return array with key = word and value = position
	public function getLastSubject( \DateTime $date =null)
	{	
		// return date of day if null
		if ($date == null) {
			$date = new \DateTime();
		}
		if (new \DateTime() < $date) {
			return array();
		}
		$dateString = $date->format('Ymd');

		$responseDecoded = $this->getHotTrends($dateString);
		if (!$responseDecoded) {
			return array();
		}
		$response = $responseDecoded['trendsByDateList'];
		$subjects = array();
		$i = 0;
		foreach ($response as $row) {
			foreach ($row as $trend) {
				if (is_array($trend)) {
					foreach ($trend as $ligne) {
						$subjects [$ligne['title']] = $i;
						$i ++;
					}
				}

			}

		}
		return $subjects;
	}
	
	//return array with key = subject , value = weight
	public function  getTrends($qte, $startDate, $endDate = null, $options = array()) {
		if ($startDate == null ) $startDate = new \DateTime();
		if ($endDate== null ) $endDate= new \DateTime();
		if ($qte== null ) $qte= 0;
		$TrendMgr = $this->container->get('seriel_trend.manager');
		if (false) $TrendMgr= new TrendManager();
		
		$trends = $TrendMgr->getTrends($qte, $startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

		//average and max score trend
		$max_position = 0;
		$avg_position = 0;
		foreach ($trends as $trend) {
			if ($trend->getPosition() > $max_position) $max_position = $trend->getPosition();
			$avg_position += $trend->getPosition();
		}
		if (count($trends) > 0) $avg_position = $avg_position / count($trends);
		//format $Arraytrends array(trends => value)
		$Arraytrends = array();
		foreach ($trends as $trend) {
			$mark = ZombieUtils::getMarkOn100(($max_position - $trend->getPosition()), $avg_position);
			$Arraytrends[$trend->getName()] = $mark;
		}
		arsort($Arraytrends);
		return $Arraytrends;
	}
	
	//return array with key = subject , value = weight
	public function  getTrendsFutur(\DateTime $date, $precision = 100 ,$options = array()) {
		if ($date== null ) $date= new \DateTime();

		$day = $date->format('d');
		$month = $date->format('m');
		$TrendMgr = $this->container->get('seriel_trend.manager');
		if (false) $TrendMgr= new TrendManager();
		
		$trends = $TrendMgr->getTrendsByDayInYear($day, $month);
		
		//get managersemantique for similarity in parameters
		if (isset($options['manager_similarity'])) {
			$managersemantique = $options['manager_similarity'];
		}else {
			$managersemantique = $this->container->getParameter('zombie.modules.googleTrend')['semantique_similarity']; 
		}
		$semantiqueMgr = $this->container->get($managersemantique);
		
		//delete trends Non-recurring
		$trendsRecurring = array();
		foreach ($trends as $trend1) {
			foreach ($trends as $trend2) {
				if ( $trend1->getID() != $trend2->getId() ) {
					if ($trend1->getName() == $trend2->getName() ) {
						$trendsRecurring[$trend2->getId()] = $trend2;
					}
					else if ($precision < 100 and isset($semantiqueMgr)) {
						//use similarity semantique
						$similarity = $semantiqueMgr->getSimilarity($trend1->getName(),$trend2->getName());
						if ($similarity > $precision) {
							$trendsRecurring[$trend2->getId()] = $trend2;
							$trendsRecurring[$trend1->getId()] = $trend1;
						}
						
					}
				}

			}
		}
		
		//average and max score trend
		$max_position = 0;
		$avg_position = 0;
		foreach ($trendsRecurring as $trend) {
			if ($trend->getPosition() > $max_position) $max_position = $trend->getPosition();
			$avg_position += $trend->getPosition();
		}
		if (count($trendsRecurring) > 0) $avg_position = $avg_position / count($trendsRecurring);
		
		//format $Arraytrends array(trends => value)
		$Arraytrends = array();
		foreach ($trendsRecurring as $trend) {
			if (isset($Arraytrends[$trend->getName()])) {
				$mark = ZombieUtils::getMarkOn100(($max_position - $trend->getPosition()), $avg_position);
				$Arraytrends[(string) $trend->getName()] = (($mark + $Arraytrends[$trend->getName()]) / 2);
			} else {
				$mark = ZombieUtils::getMarkOn100(($max_position - $trend->getPosition()), $avg_position);
				$Arraytrends[(string) $trend->getName()] = $mark;
			}
			
		}
		arsort($Arraytrends);
		return $Arraytrends;
	}
	
	
	
	//return array with key = subject , value = weight
	public function  getTrendsDirect($qte, $startDate, $endDate = null, $options = array()) {
		if ($startDate == null ) $startDate = new \DateTime();
		if ($endDate == null) {
			//sort by position
			$trends = $this->getLastSubject($startDate);
		}
		else {
			// for several days, we use several getLastSubject
			$trends = array();
			$intervalDay = $startDate->diff($endDate)->format('%a') +1;
			$dateCurrent = $startDate;
			for ($iterator = 1;$iterator<= $intervalDay; $iterator++) {
				$trends = array_merge($trends, $this->getLastSubject($dateCurrent));
				$dateCurrent = $dateCurrent->add(new \DateInterval('P1D'));
			}
			asort($trends);
		}
		// Removes the subjects less well noted
		if ($qte < count($trends) and $qte > 0) {
			$trends = array_keys($trends);
			$trends = array_slice($trends, 0, $qte);
			$trends = array_flip($trends);
		}
		
		// calcul of the weight
		foreach($trends as $key => $value) {
			$trends[$key] = count($trends) - $value;
		}
		return $trends;
	}
	
	/*
	 *  Return trends by word in last 30 days (experimental)
	 */
	public function getTrendsBySubject($word) {

		$this->wordsearch= [];
		$this->addWord($word);
		$this->setLastDays(30);
		
		$url = $this->prepareSearchUrl();
		echo "URL : ".$url."\n";
		
		$client = new Client();
		$response= $client->request('GET', $url);
	 	
		$responseDecoded = json_decode($response->getBody());
		if ($response->getStatusCode()== 200) {
			/*
			$responseBody = substr($response->getBody(true), 62, -2);
			if (!$responseDecoded = json_decode($responseBody)) {
				return false;
				
			}
			if ($responseDecoded->status == 'error') {
				
				return false;
			}
			*/
			var_dump($responseDecoded) ;
		}
		else {
			return false;
		}
	}
	/**
	 * @param $initialMonth
	 * @param $finalMonth
	 * @return $this
	 */
	public function setMonthInterval(\DateTime $initialMonth, \DateTime $finalMonth)
	{
		if ($initialMonth->format('Ym') === $finalMonth->format('Ym')) {
			$this->monthInterval = $initialMonth->format('m/Y');
		}
		if ($initialMonth->format('Ym') !== $finalMonth->format('Ym')) {
			$monthsDifference = ($initialMonth->format('m') - $finalMonth->format('m')) * -1;
			$yearsDifference = ($initialMonth->format('Y') - $finalMonth->format('Y')) * 12;
			$this->monthInterval = $initialMonth->format('m/Y') . '+' . (($yearsDifference - $monthsDifference) * -1) . 'm';
		}
		return $this;
	}
	
	/**
	 * @param $lastDays
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function setLastDays($lastDays)
	{
		if (!in_array($lastDays, $allowedDays = [7, 30, 90, 365])) {
			throw new \InvalidArgumentException(
					'Allowed days: ' . implode(', ', $allowedDays) .
					'. Supplied: ' . strval($lastDays)
					);
		}
		if ($lastDays == 7) {
			$this->lastDays = 'today+' . intval($lastDays) . '-d';
		}
		if ($lastDays != 7) {
			$this->lastDays = 'today+' . ceil(bcdiv($lastDays, 30)) . '-m';
		}
		return $this;
	}
	
	/**
	 * @param $word
	 * @return $this
	 */
	public function addWord($word)
	{
		$this->wordsearch[$word] = $word;
		return $this;
	}
	
	// return check list of import and calculate data for google trend
	public function getStateImports() {
		$stateInports = array();
		
		$TrendMgr = $this->container->get('seriel_trend.manager');
		if (false) $TrendMgr= new TrendManager();
		
		$date = $TrendMgr->getLastDate();
		if (isset($date)) {
			$stateInports[] = New StateImport('GoogleTrends - Dernière mise à jour', $date);
		}
		return $stateInports;
	}
}
