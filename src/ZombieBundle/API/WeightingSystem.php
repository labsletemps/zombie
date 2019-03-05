<?php

namespace ZombieBundle\API;

interface WeightingSystem {
	
	/**
	 * returns a mapped array of weights from 0 (lightweight) to 100 (heavy)
	 * for each notation_module given in parameter 2
	 * 
	 * @return array
	 */
	public function getWeights($article, $notationModules, $options = array());
}