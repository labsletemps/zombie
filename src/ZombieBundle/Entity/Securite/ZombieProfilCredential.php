<?php

namespace ZombieBundle\Entity\Securite;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Entity\CredentialMultiChoice;
use ZombieBundle\Entity\RootObject;

/**
 * @ORM\Entity
 * @ORM\Table(name="profil_credential",options={"engine"="MyISAM"})
 */
class ZombieProfilCredential extends RootObject
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="ZombieCredential")
	 * @ORM\JoinColumn(name="credential_id", referencedColumnName="id", nullable=false)
	 */
	protected $credential;
	
	/**
	 * @ORM\Column(type="integer", unique=false, nullable=true)
	 */
	protected $access_level;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Seriel\AppliToolboxBundle\Entity\CredentialMultiChoice"))
	 * @ORM\JoinColumn(name="cred_multi_choice_id", referencedColumnName="id", nullable=true)
	 */
	protected $choice;
	
	/**
	 * @ORM\ManyToOne(targetEntity="ZombieProfil", inversedBy="profils_credentials"))
	 * @ORM\JoinColumn(name="profil_id", referencedColumnName="id", nullable=false)
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
     * Set accessLevel
     *
     * @param integer $accessLevel
     *
     * @return ProfilCredential
     */
    public function setAccessLevel($accessLevel)
    {
        $this->access_level = $accessLevel;

        return $this;
    }

    /**
     * Get accessLevel
     *
     * @return integer
     */
    public function getAccessLevel()
    {
        return $this->access_level;
    }

    /**
     * Set credential
     *
     * @param ZombieCredential $credential
     *
     * @return ProfilCredential
     */
    public function setCredential(ZombieCredential $credential)
    {
        $this->credential = $credential;

        return $this;
    }

    /**
     * Get credential
     *
     * @return ZombieCredential
     */
    public function getCredential()
    {
        return $this->credential;
    }

    /**
     * Set profil
     *
     * @param Profil $profil
     *
     * @return ProfilCredential
     */
    public function setProfil(ZombieProfil $profil)
    {
        $this->profil = $profil;

        return $this;
    }

    /**
     * Get profil
     *
     * @return Profil
     */
    public function getProfil()
    {
        return $this->profil;
    }

    /**
     * Set choice
     *
     * @param CredentialMultiChoice $choice
     *
     * @return ProfilCredential
     */
    public function setChoice(CredentialMultiChoice $choice)
    {
        $this->choice = $choice;

        return $this;
    }

    /**
     * Get choice
     *
     * @return CredentialMultiChoice
     */
    public function getChoice()
    {
        return $this->choice;
    }
}
