<?php

namespace ZombieBundle\Entity\Geo;

use Doctrine\ORM\Mapping as ORM;
use ZombieBundle\Entity\RootObject;

/**
 * @ORM\Entity
 * @ORM\Table(name="region",options={"engine"="MyISAM"})
 */
class Region extends RootObject
{
	const TYPE_REGION_PAYS = 1;
	const TYPE_REGION_REGION = 2;
	const TYPE_REGION_DEPARTEMENT = 3;
	const TYPE_REGION_PERSO = 4;
	
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="integer", unique=false, nullable=false)
	 */
	protected $type;
	
	/**
	 * @ORM\Column(type="string", length=100, unique=false, nullable=false)
	 */
	protected $code;

	/**
	 * @ORM\Column(type="string", length=200, unique=false, nullable=false)
	 */
	protected $label;
	
	/**
	 * @ORM\Column(type="text", unique=false, nullable=true)
	 */
	protected $description;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Region", inversedBy="regionsEnfants")
	 * @ORM\JoinColumn(name="regtionParent_id", referencedColumnName="id")
	 */
	protected $regionParent;
	
	/**
	 * @ORM\OneToMany(targetEntity="Region", mappedBy="regionParent")
	 */
	protected $regionsEnfants;
	

	public function __construct()
	{
		parent::__construct();
	}

	public function getId() {
		return $this->id;
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

    public function __toString(){
    	return $this->getLabel();
    }
    

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Region
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Region
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Region
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set regionParent
     *
     * @param Region $regionParent
     *
     * @return Region
     */
    public function setRegionParent(Region $regionParent = null)
    {
        $this->regionParent = $regionParent;

        return $this;
    }

    /**
     * Get regionParent
     *
     * @return Region
     */
    public function getRegionParent()
    {
        return $this->regionParent;
    }

    /**
     * Add regionsEnfant
     *
     * @param Region $regtionsEnfant
     *
     * @return Region
     */
    public function addRegionsEnfant(Region $regtionsEnfant)
    {
        $this->regionsEnfants[] = $regtionsEnfant;

        return $this;
    }

    /**
     * Remove regionsEnfant
     *
     * @param Region $regtionsEnfant
     */
    public function removeRegionsEnfant(Region $regtionsEnfant)
    {
        $this->regionsEnfants->removeElement($regtionsEnfant);
    }

    /**
     * Get regionsEnfants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegionsEnfants()
    {
        return $this->regionsEnfants;
    }
}
