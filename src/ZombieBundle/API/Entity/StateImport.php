<?php

namespace ZombieBundle\API\Entity;

/**
 * StateImport
 *
 */
class StateImport
{
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * @var Datetime
	 */
	private $date;
	
	public function __construct($name, \DateTime $date)
	{
		$this->setName($name);
		$this->setDate($date);
	}
	
	/**
	 * Set name
	 *
	 * @param string
	 *
	 * @return StateImport
	 */
	public function setName($name)
	{
		$this->name = $name;
		
		return $this;
	}
	
	/**
	 * Get name
	 *
	 * @return String
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * Set date
	 *
	 * @param \DateTime
	 *
	 * @return StateImport
	 */
	public function setDate($date)
	{
		$this->date = $date;
		
		return $this;
	}
	
	/**
	 * Get date
	 *
	 * @return \DateTime
	 */
	public function getDate()
	{
		return $this->date;
	}
	
	/**
	 * Get date
	 *
	 * @return \DateTime
	 */
	public function isRecent()
	{
		if (! isset($this->date)) {
			return false;
		}else {
			$nbDay = $this->date->diff(New \DateTime())->days;
		}
		var_dump($nbDay);
		if ($nbDay > 2 ) {
			return false;
		}else {
			return true;
		}
		
	}
	
	
}

