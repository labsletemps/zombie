<?php

namespace ZombieBundle\Entity\Individu;

use Doctrine\ORM\Mapping as ORM;
use ZombieBundle\Entity\RootObject;

/**
 * @ORM\Entity
 * @ORM\Table(name="fonction",options={"engine"="MyISAM"})
 */
class Fonction extends RootObject {

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

    /**
     * @ORM\Column(type="integer")
     */
    protected $ordre;


    public function __construct() {
        parent::__construct();
    }

    public function getId() {
        return $this->id;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return Metier
     */
    public function setLabel($label) {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel() {
        return $this->label;
    }
    
    /**
     * Set ordre
     *
     * @param string $label
     * @return Metier
     */
    public function setOrdre($ordre) {
        $this->ordre = $ordre;

        return $this;
    }
    
    public function __toString() {
        return $this->getLabel();
    }
    
    
    public function getOrdre(){
        return $this->ordre;
        
    }
    
    
}
