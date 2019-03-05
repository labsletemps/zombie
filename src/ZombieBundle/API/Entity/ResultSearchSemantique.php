<?php

namespace ZombieBundle\API\Entity;

use ZombieBundle\Utils\ZombieUtils;
/**
 * ResultSearchSemantique
 *
 */
class ResultSearchSemantique
{
	/**
	 * @var array(id=>integer,totalweight=>float)
	 */
	private $result;

	/**
	 * @var float
	 */
	private $indice;
	
	/**
	 * @var float
	 */
	private $filtreMin;
	
	/**
	 * @var float
	 */
	private $filtreMax;
	
	/**
	 * @var array(id=>float)
	 * id => totalweight
	 */
	private $calculResult;

	public function __construct(array $result)
	{
		$this->setResult($result);
		$this->calculIndice();
	}

	/**
	 * Set result
	 *
	 * @param array(id=>integer,totalweight=>float)
	 *
	 * @return ResultSearchSemantique
	 */
	public function setResult($result)
	{
		$this->result = $result;
		
		return $this;
	}
	
	/**
	 * Get result
	 *
	 * @return array(id=>integer,totalweight=>float)
	 */
	public function getResult()
	{
		return $this->result;
	}
	
	/**
	 * Set indice
	 *
	 * @param  float $indice
	 *
	 * @return ResultSearchSemantique
	 */
	public function setIndice($indice)
	{
		$this->indice = $indice;
		
		return $this;
	}
	
	/**
	 * Get indice
	 *
	 * @return float
	 */
	public function getIndice()
	{
		return $this->indice;
	}
	
	/**
	 * Set filtreMin
	 *
	 * @param  float $min
	 *
	 * @return ResultSearchSemantique
	 */
	public function setFiltreMin($min)
	{
		$this->filtreMin = $min;
		
		return $this;
	}
	
	/**
	 * Get filtreMin
	 *
	 * @return float
	 */
	public function getFiltreMin()
	{
		return $this->filtreMin;
	}
	
	/**
	 * Set filtreMax
	 *
	 * @param  float $max
	 *
	 * @return ResultSearchSemantique
	 */
	public function setFiltreMax($max)
	{
		$this->filtreMax = $max;
		
		return $this;
	}
	
	/**
	 * Get filtreMax
	 *
	 * @return float
	 */
	public function getFiltreMax()
	{
		return $this->filtreMax;
	}
	
	/**
	 * Set calculResult
	 *
	 * @param array(id=>float)
	 *
	 * @return ResultSearchSemantique
	 */
	public function setCalculResult($CalculResult)
	{
		$this->calculResult = $CalculResult;
		
		return $this;
	}
	
	/**
	 * Get calculResult
	 *
	 * @return array(id=>float)
	 */
	public function getCalculResult()
	{
		return $this->calculResult;
	}
	
	/**
	 * Get result
	 *
	 * @return array(id)
	 */
	public function getCalculResultId()
	{
		return array_keys($this->calculResult);
	}

	/**
	 * calcul indice
	 *
	 * @return ResultSearchSemantique
	 */
	public function calculIndice()
	{
		$NbResult = count($this->getResult());
		if ($NbResult > 0) {
			// calcul for Third quartile
			$thirdquartile = $this->getResult()[$NbResult/4]['totalweight'];
			$this->setIndice($thirdquartile);
		}
		return $this;
	}

	/**
	 * calcul result
	 *
	 * @return ResultSearchSemantique
	 */
	public function calculResult() {

		$this->calculResult = array();
		foreach ($this->getResult() as $row) {
			$weight = $row['totalweight'];
			$score = ZombieUtils::getMarkOn100($weight, $this->getIndice());
			// filter min max 
			if (($this->getFiltreMax() == null OR $this->getFiltreMax() >= $score ) and ($this->getFiltreMin()== null OR $this->getFiltreMin()<= $score ) ) {
				$this->calculResult[$row['id']] = $score;
			}
		}
		return $this->getCalculResult();
	}
	

}

