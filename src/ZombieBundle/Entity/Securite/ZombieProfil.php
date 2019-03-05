<?php

namespace ZombieBundle\Entity\Securite;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Annotation as SER;
use Seriel\AppliToolboxBundle\Entity\CredentialMultiChoice;
use Seriel\AppliToolboxBundle\Model\SerielProfilInterface;
use ZombieBundle\Entity\RootObject;

/**
 * @ORM\Entity
 * @ORM\Table(name="profil",options={"engine"="MyISAM"})
 * @ORM\HasLifecycleCallbacks
 */
class ZombieProfil extends RootObject implements SerielProfilInterface
{
	const PROFIL_TYPE_REDACTEUR = 1;
	const PROFIL_TYPE_EXTERIEUR = 2;
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", length=200, unique=false, nullable=false)
	 */
	protected $nom;
	
	/**
	 * @ORM\Column(type="integer", unique=false, nullable=false)
	 */
	protected $profil_type;
	
	/**
	 * @ORM\OneToMany(targetEntity="ZombieProfilCredential", mappedBy="profil", cascade={"persist", "remove"})
	 */
	protected $profils_credentials;

	public function __construct()
	{
		parent::__construct();
	}

	public function getId() {
		return $this->id;
	}
        
    public function __toString(){
    	return $this->getNom();
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Profil
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     * 
     * @SER\ListeProperty("nom", label="Libellé", sort="string", format="none")
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Add profilsCredential
     *
     * @param ZombieProfilCredential $profilCredentialial
     *
     * @return Profil
     */
    public function addProfilsCredential(ZombieProfilCredential $profilCredential)
    {
    	if ($profilCredential) $profilCredential->setProfil($this);
        $this->profils_credentials[] = $profilCredential;

        return $this;
    }

    /**
     * Remove profilsCredential
     *
     * @param ZombieProfilCredential $profilsCredential
     */
    public function removeProfilsCredential(ZombieProfilCredential $profilsCredential)
    {
        $this->profils_credentials->removeElement($profilsCredential);
    }
    
    public function removeAllProfilsCredentials() {
    	$pcs = array();
    	if ($this->profils_credentials) {
    		foreach ($this->profils_credentials as $profCred) {
    			$pcs[] = $profCred;
    		}
    		
    		foreach ($pcs as $profCred) {
    			$this->removeProfilsCredential($profCred);
    		}
	   	}
    }

    /**
     * Get profilsCredentials
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProfilsCredentials()
    {
        return $this->profils_credentials;
    }

    /**
     * Set profilType
     *
     * @param integer $profilType
     *
     * @return Profil
     */
    public function setProfilType($profilType)
    {
        $this->profil_type = $profilType;

        return $this;
    }

    /**
     * Get profilType
     *
     * @return integer
     */
    public function getProfilType()
    {
        return $this->profil_type;
    }
    
    /**
     * @SER\ListeProperty("type", label="Type", sort="string", format="none")
     */
    public function getProfilTypeLabel() {
    	if ($this->profil_type == self::PROFIL_TYPE_REDACTEUR) return 'Interne';
    	else if ($this->profil_type == self::PROFIL_TYPE_EXTERIEUR) return 'Externe';
    	
    	return '';
    }
    
    public function getTuilesParamsSupp() {
    	return array('nom' => $this->getNom());
    }
    
    public function getProfCredForCred($obj, $code) {
    	$prof_creds = $this->getProfilsCredentials();
    	if ($prof_creds) {
    		foreach($prof_creds as $prof_cred) {
    			if (false) $prof_cred = new ZombieProfilCredential();
    			$cred = $prof_cred->getCredential();
    			
    			if (trim(strtoupper($cred->getCode())) == trim(strtoupper($code))) {
    				// Test object
    				$cred_obj = $cred->getObject();
    				if ((!$cred_obj) && (!$obj)) return $prof_cred;
    				
    				if ($cred_obj == $obj) return $prof_cred;
    			}
    		}    		
    	}
    }
    
    // Test security directly of profil.
    public function hasCred($cred_obj, $cred_code, $level = null, $choice = null) {
    	$prof_cred = $this->getProfCredForCred($cred_obj, $cred_code);
    	if (!$prof_cred) return false;
    	
    	$cred = $prof_cred->getCredential();
    	
    	if ($cred->hasLevel()) {
    		$prof_cred_level = $prof_cred->getAccessLevel();
			
    		// TODO : gérer le level.    		
    	}

    	// choice.
    	if ($cred->hasChoices()) {
    		$prof_cred_choice = $prof_cred->getChoice();
    		if ((!$prof_cred_choice) || (!$choice)) return false;
    		if ($choice instanceof CredentialMultiChoice) {
    			if ($choice->getId() != $prof_cred_choice->getId()) return false;
    		} else if (is_string($choice)) {
    			if (trim(strtoupper($choice)) != trim(strtoupper($prof_cred_choice->getCode()))) return false;
    		} else {
    			return false;
    		}
    	}
    	
    	return true;
    }
    
    /**
     * 
     * @return CredentialMultiChoice
     */
    public function getChoiceForCredential($cred_obj, $cred_code) {
    	$prof_cred = $this->getProfCredForCred($cred_obj, $cred_code);
    	if (!$prof_cred) return null;
    	
    	$cred = $prof_cred->getCredential();
    	
    	if ($cred->hasChoices()) return $prof_cred->getChoice();
    	
    	return null;
    }
}
