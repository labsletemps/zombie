<?php

namespace ZombieBundle\Entity\Securite;

use Doctrine\ORM\Mapping as ORM;
use ZombieBundle\Entity\Individu\Individu;
use ZombieBundle\Entity\Securite\ZombieProfil;
use ZombieBundle\Entity\RootObject;

/**
 * @ORM\Entity
 * @ORM\Table(name="individu_profil",options={"engine"="MyISAM"})
 */
class IndividuProfil extends RootObject
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\ManyToOne(targetEntity="ZombieBundle\Entity\Individu\Individu", inversedBy="profils")
	 * @ORM\JoinColumn(name="individu_id", referencedColumnName="id")
	 */
	protected $individu;
	
	/**
	 * @ORM\ManyToOne(targetEntity="ZombieProfil")
	 * @ORM\JoinColumn(name="profil_id", referencedColumnName="id")
	 */
	protected $profil;
	        
	public function __construct()
	{
		parent::__construct();
	}

	public function getId() {
		return $this->id;
	}

    /**
     * Set individu
     *
     * @param Individu $individu
     *
     * @return IndividuProfil
     */
    public function setIndividu(Individu $individu = null)
    {
        $this->individu = $individu;

        return $this;
    }

    /**
     * Get individu
     *
     * @return Individu
     */
    public function getIndividu()
    {
        return $this->individu;
    }

    /**
     * Set profil
     *
     * @param ZombieProfil $profil
     *
     * @return IndividuProfil
     */
    public function setProfil(ZombieProfil $profil = null)
    {
        $this->profil = $profil;

        return $this;
    }

    /**
     * Get profil
     *
     * @return ZombieProfil
     */
    public function getProfil()
    {
        return $this->profil;
    }

}
