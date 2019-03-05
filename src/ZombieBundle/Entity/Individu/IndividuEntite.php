<?php

namespace ZombieBundle\Entity\Individu;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Annotation as SER;
use ZombieBundle\Entity\RootObject;

/**
 * @ORM\Entity
 * @ORM\Table(name="individu_entite",options={"engine"="MyISAM"})
 */
class IndividuEntite extends RootObject
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\ManyToOne(targetEntity="ZombieBundle\Entity\Entite\ZombieEntite", inversedBy="individus")
	 * @ORM\JoinColumn(name="entite_id", referencedColumnName="id")
	 */
	protected $entite;
    
	/**
	 * @ORM\ManyToOne(targetEntity="Individu", inversedBy="entites")
	 * @ORM\JoinColumn(name="individu_id", referencedColumnName="id")
	 */
	protected $individu;
                
 	/**
	 * @ORM\ManyToOne(targetEntity="Fonction")
	 * @ORM\JoinColumn(name="fonction_id", referencedColumnName="id")
	 */
	protected $fonction;
        
        
	public function __construct()
	{
		parent::__construct();
	}

	public function getId() {
		return $this->id;
	}
        
	public function setEntite($entite){
		$this->entite = $entite;
	}
	public function setIndividu($individu){
		$this->individu = $individu;
	}
	public function setFonction($fonction){
		$this->fonction = $fonction;
	}
        
	/**
	 * @return Entite
	 */
	public function getEntite(){
		return $this->entite;
	}

	/**
	 * @return Individu
	 * 
	 * @SER\ListeProperty("individu", label="Individu", type="object", objectClass="ZombieBundle\Entity\Individu\Individu", objectPostfix="", dbfield="individu")
	 */
	public function getIndividu(){
		return $this->individu; 
	}
	
	public function getFonction(){
		return $this->fonction;
	}

	public function getTuilesParamsSupp() {
		return array('individu_id' => $this->individu->getId());
	}
}
