<?php

namespace Seriel\TrendBundle\Managers;

use ZombieBundle\API\Managers\TrendsModule;


class CrossTrendManager implements TrendsModule {
	
	protected static $module_name = 'crosstrend';
	protected static $module_descirption = "Agregation of modules trends";

	private $container = null;
	private $logger = null;
	private $modulesTrend = array();
	private $managerSimilarity = null;
	private $percentAgregation = null;
	public function __construct($container, $logger) {
		$this->container = $container;
		$this->logger = $logger;
	
		/*
		 * Modules
		 */
		$modulesMgr = $this->container->get('zombie_modules_manager');
		if (false) $modulesMgr = new ModulesManager();
		$modules = $modulesMgr->getZombieModules();
		if ($modules) {
			foreach ($modules as $name => $paramsModule) {
				// Trend
				if (isset($paramsModule['service_trend']) && $paramsModule['service_trend']) {
					if ($name != $this->getName()) {
						$this->modulesTrend[] = $paramsModule['service_trend'];
					}
				}		
			}
		}
		
		// Parameters
		$this->managerSimilarity = $this->container->getParameter('zombie.modules.crossTrend')['semantique_similarity'];
		$this->percentAgregation = $this->container->getParameter('zombie.modules.crossTrend')['percent_agregation'];
	}

	public function getName() { return self::$module_name; }
	public function getDescription() { return self::$module_descirption; }
	
	
	
	//return array with key = subject , value = weight
	public function  getTrends($qte, $startDate, $endDate = null, $options = array()) {
		if ($startDate == null ) $startDate = new \DateTime();
		if ($endDate== null ) $endDate= new \DateTime();
		if ($qte== null ) $qte= 0;
		
		if (!isset($options['manager_similarity'])) $options['manager_similarity'] = $this->managerSimilarity;
		if (!isset($options['percent_agregation'])) $options['percent_agregation'] = $this->percentAgregation;
		
		$managerSimilarity = $this->container->get($options['manager_similarity']);
		$ArraytrendsAll = array();

		foreach ($this->modulesTrend as $manager) {
			$manager = $this->container->get($manager);
			$trends = $manager->getTrends($qte, $startDate, $endDate , $options);
			
			foreach($trends as $name => $value) {
				if (isset($ArraytrendsAll[$name])) {
					$ArraytrendsAll[$name] += $value;
				}else {
					$ArraytrendsAll[$name] = $value;
				}
				
			}
		}

		//Agregation
		$Arraytrends = array();
		if (count($this->modulesTrend)> 1) {
			foreach($ArraytrendsAll as $name1 => $value1) {
				foreach($ArraytrendsAll as $name2 => $value2) {
					if ($name1!= $name2) {
						//use similarity semantique
						$similarity = $managerSimilarity->getSimilarity($name1,$name2);
						if ($similarity > $options['percent_agregation']) {
							if ($value1 > $value2) {
								$Arraytrends[(string)$name1] = $value1 + $value2;
							}
							else {
								$Arraytrends[(string)$name2] = $value1 + $value2;
							}
						}
						else {
							$Arraytrends[(string)$name2] = $value2;
						}
					}
				}
			}
		}
		else {
			arsort($ArraytrendsAll);
			return $ArraytrendsAll;
		}
		arsort($Arraytrends);
		return $Arraytrends;
	}
	
	//return array with key = subject , value = weight
	public function  getTrendsFutur(\DateTime $date, $precision = 100 ,$options = array()) {
		if ($date== null ) $date= new \DateTime();

		$Arraytrends = array();
		if (!isset($options['manager_similarity'])) $options['manager_similarity'] = $this->managerSimilarity;
		foreach ($this->modulesTrend as $manager) {
			$manager = $this->container->get($manager);
			$trends = $manager->getTrendsFutur($date, $precision, $options);
			foreach($trends as $name => $value) {
				if (isset($Arraytrends[$name])) {
					$Arraytrends[(string)$name] += $value;
				}else {
					$Arraytrends[(string)$name] = $value;
				}
			}
		}
		arsort($Arraytrends);
		return $Arraytrends;
	}
}
