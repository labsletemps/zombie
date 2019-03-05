<?php

namespace ZombieBundle\API\Managers;

interface ManagerCrossMetrics {
	public function getIndicator($article,$indicator);
	public function getMeasure($article,$measure);
	public function getAllIndicators($article); 
	public function getAllMeasures($article);
}