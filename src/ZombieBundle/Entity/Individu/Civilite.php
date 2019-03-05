<?php

namespace ZombieBundle\Entity\Individu;

use Doctrine\ORM\Mapping as ORM;
use ZombieBundle\Entity\RootObject;

/**
 * @ORM\Entity
 * @ORM\Table(name="civilite",options={"engine"="MyISAM"})
 */
class Civilite extends RootObject
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", length=200, unique=true, nullable=false)
	 */
	protected $label;

	public function __construct()
	{
		parent::__construct();
	}

	public function getId() {
		return $this->id;
	}
	
        
    public function __toString(){
    	return $this->getLabel();
    }
        
	
    /**
     * Set label
     *
     * @param string $label
     * @return Civilite
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

}
