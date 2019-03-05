<?php

namespace ZombieBundle\Entity\Fichier;

use Doctrine\ORM\Mapping as ORM;
use ZombieBundle\Entity\RootObject;
use ZombieBundle\Entity\Securite\ZombieProfil;

/**
 * @ORM\Entity
 * @ORM\Table(name="file_visibilite_profil",options={"engine"="MyISAM"})
 * 
 */
class FileVisibiliteProfil extends RootObject
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="File", inversedBy="visibilites_profil")
	 * @ORM\JoinColumn(name="file_id", referencedColumnName="id")
	 */
	protected $file;
	
	/**
	 * @ORM\ManyToOne(targetEntity="ZombieBundle\Entity\Securite\ZombieProfil")
	 * @ORM\JoinColumn(name="profil_id", referencedColumnName="id")
	 */
	protected $profil;
	
	/**
	 * @ORM\Column(type="boolean", unique=false, nullable=false, options={"default":false})
	 */
	protected $visible;
	
	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}


    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return FileVisibiliteProfil
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set evenement
     *
     * @param File $file
     *
     * @return FileVisibiliteProfil
     */
    public function setFile(File $file = null)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return \ZombieBundle\Entity\Fichier\File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set profil
     *
     * @param ZombieProfil $profil
     *
     * @return FileVisibiliteProfil
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
