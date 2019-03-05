<?php

namespace ZombieBundle\API;

interface NotationModule {
	
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
	 * returns a value between 0 (worst) and 100 (best)
	 * @return integer
	 */
	public function giveMark($article, $options = array());
}