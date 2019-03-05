<?php

namespace Seriel\TrendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;

/**
 * Trend
 *
 * @ORM\Entity
 * @ORM\Table(name="trend",options={"engine"="MyISAM"})
 */
class Trend extends RootObject implements Listable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;
    
    /**
     * @var string
     *
     * @ORM\Column(name="module", type="string", length=50)
     */
    private $module;

    public function __construct()
    {
    	parent::__construct();
    }
    
    public function getListUid() {
    	return $this->getId();
    }
    
    public function getTuilesParamsSupp() {
    	return array();
    }
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \Date $date
     *
     * @return Trend
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \Date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Trend
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Trend
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }
    
    
    /**
     * Set module
     *
     * @param string $module
     *
     * @return Trend
     */
    public function setModule($module)
    {
    	$this->module = $module;
    	
    	return $this;
    }
    
    /**
     * Get module
     *
     * @return string
     */
    public function getModule()
    {
    	return $this->module;
    }
}

