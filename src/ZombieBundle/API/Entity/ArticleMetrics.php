<?php

namespace ZombieBundle\API\Entity;

interface ArticleMetrics {
	public function getAllMeasures(); // Indexed Array
	public function getAllIndicators(); // Indexed Array
	public static function getAllIdIndicators(); // Indexed Array
	public static function getAllLogoIndicators(); // Indexed Array
	public function getMeasure($measure);
	public function getIndicator($indicator);
}