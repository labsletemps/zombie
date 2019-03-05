<?php

namespace ZombieBundle\API\Managers;

interface TrendsModule {
	
	/**
	 * returns a unique name that identifies the module
	 * @return string
	 */
	public function getName();
	
	/**
	 * returns a human readable description of the module
	 * @return string
	 */
	public function getDescription();
	
	/**
	 * returns qte trending subjects analysed on the periode startDate > endDate
	 * if endDate is null, consider it being now()
	 * @return array subject => mark(0 => 100)
	 */
	public function getTrends($qte, $startDate, $endDate = null, $options = array());
	
	/**
	 * returns subjects recurring for each year 
	 * $precision ( 0 => 100)
	 * @return array subject => mark(0 => 100)
	 */
	public function getTrendsFutur(\DateTime $date,$precision,$options= array());
}