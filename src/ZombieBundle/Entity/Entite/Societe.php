<?php

namespace ZombieBundle\Entity\Entite;

use Doctrine\ORM\Mapping as ORM;
use ZombieBundle\Entity\Entite\ZombieEntite;



/**
 * @ORM\Entity
 */
class Societe extends ZombieEntite
{
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function getId() {
		return $this->id;
	}
        
	public function __toString() {
		return $this->getNom();
	}
}
