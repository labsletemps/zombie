<?php

namespace ZombieBundle\API\Entity;

interface ArticleCrossMetrics {
	public function getAllIndicators(); // Indexed Array
	public static function getAllIdIndicators(); // Indexed Array
	public static function getAllLogoIndicators(); // Indexed Array
	public function getIndicator($indicator);

}