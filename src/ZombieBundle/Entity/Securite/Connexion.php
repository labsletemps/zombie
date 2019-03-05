<?php

namespace ZombieBundle\Entity\Securite;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Annotation as SER;
use ZombieBundle\Entity\RootObject;
use ZombieBundle\Entity\Entite\ZombieEntite;

/**
 * @ORM\Entity
 * @ORM\Table(name="connexion",options={"engine"="MyISAM"})
 */
class Connexion extends RootObject
{	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="string", length=255, unique=false, nullable=true)
	 */
	protected $infos_nav;
	
	/**
	 * @ORM\Column(type="string", length=255, unique=false, nullable=true)
	 */
	protected $ip_source;
	
	/**
	 * @ORM\ManyToOne(targetEntity="ZombieBundle\Entity\Individu\Individu")
	 * @ORM\JoinColumn(name="individu_id", referencedColumnName="id")
	 */
	protected $individu;
	
	/**
	 * @ORM\Column(type="string", length=255, unique=false, nullable=false)
	 */
	protected $individu_nom;
	
	/**
	 * @ORM\Column(type="string", length=255, unique=false, nullable=false)
	 */
	protected $individu_profil;
	
	/**
	 * @ORM\Column(type="datetime", nullable=false)
	 */
	protected $dateConnexion;
	
	/**
	 * @ORM\ManyToOne(targetEntity="ZombieBundle\Entity\Entite\ZombieEntite")
	 * @ORM\JoinColumn(name="entite_id", referencedColumnName="id")
	 */
	protected $entite;
	
	
	public function __construct()
	{
		parent::__construct();
	}


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @SER\ListeProperty("infos_nav", label="Infos navigateur", dbfield="infos_nav")
     */
    public function getInfosNav(){
    	return $this->infos_nav;
    }
    
    public function setIfosNav($infos){
    	$this->infos_nav = $infos;
    	return $this;
    }
    
    /**
     * @SER\ListeProperty("ip_source", label="Adresse IP", dbfield="ip_source")
     * @SER\ReportingColRowProperty("ip_source", label="Adresse IP")
     */
    public function getIpSource(){
    	return $this->ip_source;
    }
    
    public function setIpSource($ip){
    	$this->ip_source = $ip;
    	return $this;
    }
    
    public function getIndividu(){
    	return $this->individu;
    }
    
    public function setIndividu($individu){
    	$this->individu = $individu;
    	return $this;
    }
    
    /**
     * @SER\ListeProperty("individ_nom", label="Individu", dbfield="individu_nom")
     * @SER\ReportingColRowProperty("individu_nom", label="Individu")
     */
    public function getIndividuNom(){
    	return $this->individu_nom;
    }
    public function setIndividuNom($nom){
    	$this->individu_nom = $nom;
 		return $this;
    }
    
    /**
     * @SER\ListeProperty("individ_profil", label="Profil", dbfield="individu_profil")
     * @SER\ReportingColRowProperty("individu_profil", label="Profil")
     */
    public function getIndividuProfil(){
    	return $this->individu_profil;
    }
    
    public function setIndividuProfil($profil){
    	$this->individu_profil = $profil;
    	return $this;
    }
    
    public function setDateConnexion($date){
    	$this->dateConnexion = $date;
    	return $this;
    }
    
    /**
     * @SER\ListeProperty("dateConnexion", label="Date Connexion", sort="date_heure", format="date_heure", dbfield="dateConnexion")
     * @SER\ReportingColRowProperty("dateConnexion", label="Date Connexion", sort="date_heure", format="date_heure", option="date_heure")
     */
    public function getDateConnexion(){
    	return $this->dateConnexion;
    }


    /**
     * Set infosNav
     *
     * @param string $infosNav
     *
     * @return Connexion
     */
    public function setInfosNav($infosNav)
    {
        $this->infos_nav = $infosNav;

        return $this;
    }

    /**
     * Set entite
     *
     * @param Entite $entite
     *
     * @return Connexion
     */
    public function setEntite(ZombieEntite $entite = null)
    {
        $this->entite = $entite;

        return $this;
    }

    /**
     * Get entite
     *
     * @return Entite
     * @SER\ListeProperty("entite", label="Entité", type="object", objectClass="\ZombieBundle\Entity\Entite\ZombieEntite", objectPostfix=" du compte", dbfield="entite")
	 * @SER\ReportingColRowProperty("entite", label="Entité")
     */
    public function getEntite()
    {
        return $this->entite;
    }
}
