<?php

namespace ZombieBundle\Entity\Securite;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Model\SerielCredentialInterface;
use Seriel\AppliToolboxBundle\Entity\CredentialMultiChoice;
use ZombieBundle\Entity\RootObject;

/**
 * @ORM\Entity
 * @ORM\Table(name="credential",uniqueConstraints={@ORM\UniqueConstraint(name="cred_code_obj_idx", columns={"code", "object"})},options={"engine"="MyISAM"})
 */
class ZombieCredential extends RootObject implements SerielCredentialInterface
{
	const ACCESS_LEVEL_SELF = 1;
	const ACCESS_LEVEL_ENTITE = 2;
	const ACCESS_LEVEL_COMPANY = 3;
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="string", length=50, unique=false, nullable=false)
	 */
	protected $code;
	
	/**
	 * @ORM\Column(type="string", length=150, unique=false, nullable=true)
	 */
	protected $object;
	
	/**
	 * @ORM\Column(type="string", length=300, unique=false, nullable=true)
	 */
	protected $group;

	/**
	 * @ORM\Column(type="string", length=300, unique=false, nullable=false)
	 */
	protected $name;
	
	/**
	 * @ORM\Column(type="string", length=100, unique=false, nullable=false)
	 */
	protected $short_name;
	
	/**
	 * @ORM\Column(type="boolean", unique=false, nullable=false, options={"default":false})
	 */
	protected $has_level;
	
	/**
	 * @ORM\Column(type="array", unique=false, nullable=true)
	 */
	protected $available_levels_filter;
	
	/**
	 * @ORM\ManyToOne(targetEntity="ZombieCredential", inversedBy="childrens_credentials")
	 * @ORM\JoinColumn(name="parent_credential_id", referencedColumnName="id", nullable=true)
	 */
	protected $parent_credential;
	
	/**
	 * @ORM\OneToMany(targetEntity="ZombieCredential", mappedBy="parent_credential")
	 */
	protected $childrens_credentials;
	
	/**
	 * @ORM\Column(type="boolean", unique=false, nullable=false, options={"default":false})
	 */
	protected $compatible_redacteur;
	
	/**
	 * @ORM\Column(type="boolean", unique=false, nullable=false, options={"default":true})
	 */
	protected $compatible_exterieur;
	
	/**
	 * @ORM\OneToMany(targetEntity="Seriel\AppliToolboxBundle\Entity\CredentialMultiChoice", mappedBy="credential")
	 */
	protected $choices;

	public function __construct()
	{
		parent::__construct();
	}

	public function getId() {
		return $this->id;
	}
	
	public function setCode($code) {
		$this->code = $code;
	}
	public function getCode() {
		return $this->code;
	}
	
	public function setObject($object) {
		$this->object = $object;
	}
	public function getObject() {
		return $this->object;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}
	
	public function setShortName($short_name) {
		$this->short_name = $short_name;
	}
	public function getShortName() {
		return $this->short_name;
	}
	
	/**
	 * Set hasLevel
	 *
	 * @param boolean $hasLevel
	 *
	 * @return ZombieCredential
	 */
	public function setHasLevel($hasLevel)
	{
		$this->has_level = $hasLevel;
	
		return $this;
	}
	
	/**
	 * Get hasLevel
	 *
	 * @return boolean
	 */
	public function hasLevel()
	{
		return $this->has_level;
	}
	
        
    /**
     * Set parentCredential
     *
     * @param ZombieCredential $parentCredential
     *
     * @return ZombieCredential
     */
    public function setParentCredential(ZombieCredential $parentCredential = null)
    {
        $this->parent_credential = $parentCredential;

        return $this;
    }

    /**
     * Get parentCredential
     *
     * @return ZombieCredential
     */
    public function getParentCredential()
    {
        return $this->parent_credential;
    }

    /**
     * Add childrensCredential
     *
     * @param ZombieCredential $childrensCredential
     *
     * @return ZombieCredential
     */
    public function addChildrensCredential(ZombieCredential $childrensCredential)
    {
        $this->childrens_credentials[] = $childrensCredential;

        return $this;
    }

    /**
     * Remove childrensCredential
     *
     * @param ZombieCredential $childrensCredential
     */
    public function removeChildrensCredential(ZombieCredential $childrensCredential)
    {
        $this->childrens_credentials->removeElement($childrensCredential);
    }

    /**
     * Get childrensCredentials
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildrensCredentials()
    {
        return $this->childrens_credentials;
    }
    
    public function __toString(){
    	return $this->getName();
    }
    

    /**
     * Set availableLevelsFilter
     *
     * @param array $availableLevelsFilter
     *
     * @return ZombieCredential
     */
    public function setAvailableLevelsFilter(array $availableLevelsFilter)
    {
        $this->available_levels_filter = $availableLevelsFilter;

        return $this;
    }

    /**
     * Get availableLevelsFilter
     *
     * @return array
     */
    public function getAvailableLevelsFilter()
    {
        return $this->available_levels_filter;
    }
    
    public function isLevelAvailable($level) {
    	if (!$level) return false;
    	if (!$this->has_level) return false;
    	
    	if ($level != self::ACCESS_LEVEL_SELF && $level != self::ACCESS_LEVEL_ENTITE && $level != self::ACCESS_LEVEL_COMPANY) return false;
    	
    	if ($this->available_levels_filter === null) return true;
    	
    	foreach ($this->available_levels_filter as $l) if ($l == $level) return true;
    	
    	return false;
    }
    

    /**
     * Set group
     *
     * @param string $group
     *
     * @return ZombieCredential
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }
    
    public function getUID() {
    	if (!$this->object) return $this->code;
    	
    	return $this->object.' >> '.$this->code;
    }

    /**
     * Get hasLevel
     *
     * @return boolean
     */
    public function getHasLevel()
    {
        return $this->has_level;
    }

    /**
     * Add choice
     *
     * @param CredentialMultiChoice $choice
     *
     * @return ZombieCredential
     */
    public function addChoice(CredentialMultiChoice $choice)
    {
        $this->choices[] = $choice;

        return $this;
    }

    /**
     * Remove choice
     *
     * @param CredentialMultiChoice $choice
     */
    public function removeChoice(CredentialMultiChoice $choice)
    {
        $this->choices->removeElement($choice);
    }

    /**
     * Get choices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChoices()
    {
    	$res = array();
    	if ($this->choices) {
    		foreach ($this->choices as $choice) {
    			$res[] = $choice;
    		}
    	}
    	
    	usort($res, array('Seriel\AppliToolboxBundle\Entity\CredentialMultiChoice', 'order'));
    	
        return $res;
    }
    
    public function hasChoices() {
    	$choices = $this->getChoices();
    	if ($choices) return true;
    	
    	return false;
    }


    /**
     * Set compatibleSalarie
     *
     * @param boolean $compatibleSalarie
     *
     * @return ZombieCredential
     */
    public function setCompatibleSalarie($compatibleSalarie)
    {
        $this->compatible_salarie = $compatibleSalarie;

        return $this;
    }

    /**
     * Get compatibleSalarie
     *
     * @return boolean
     */
    public function isCompatibleSalarie()
    {
        return $this->compatible_salarie;
    }

    /**
     * Set compatiblePatient
     *
     * @param boolean $compatiblePatient
     *
     * @return ZombieCredential
     */
    public function setCompatiblePatient($compatiblePatient)
    {
        $this->compatible_patient = $compatiblePatient;

        return $this;
    }

    /**
     * Get compatiblePatient
     *
     * @return boolean
     */
    public function isCompatiblePatient()
    {
        return $this->compatible_patient;
    }

    /**
     * Set compatibleExterieur
     *
     * @param boolean $compatibleExterieur
     *
     * @return ZombieCredential
     */
    public function setCompatibleExterieur($compatibleExterieur)
    {
        $this->compatible_exterieur = $compatibleExterieur;

        return $this;
    }

    /**
     * Get compatibleExterieur
     *
     * @return boolean
     */
    public function isCompatibleExterieur()
    {
        return $this->compatible_exterieur;
    }
}
