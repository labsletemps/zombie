<?php

namespace ZombieBundle\Entity\Reporting;

use Doctrine\ORM\Mapping as ORM;
use ZombieBundle\Entity\RootObject;
use ZombieBundle\Entity\Individu\Individu;
use Seriel\AppliToolboxBundle\Managers\ManagersManager;

/**
 * @ORM\Entity
 * @ORM\Table(name="reporting_sauvegarde",options={"engine"="MyISAM"})
 */
class ReportingSauvegarde extends RootObject
{
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
	 * @ORM\Column(type="string", length=200, unique=false, nullable=false)
	 */
	protected $type;
	
	/**
	 * @ORM\Column(type="string", length=2000, unique=false, nullable=false)
	 */
	protected $chaine_reporting;
	
	/**
	 * @ORM\Column(type="string", length=2000, unique=false, nullable=true)
	 */
	protected $chaine_editables;
	
	/**
	 * @ORM\ManyToOne(targetEntity="ZombieBundle\Entity\Individu\Individu", inversedBy="saved_reports")
	 * @ORM\JoinColumn(name="individu_id", referencedColumnName="id")
	 */
	protected $individu;
	
	/**
	 * @ORM\Column(type="string", length=200, unique=false, nullable=true)
	 */
	protected $context_name;
	
	/**
	 * @ORM\Column(type="boolean", unique=false, options={"default":false})
	 */
	protected $deleted;
	
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	protected $dateDelete;
	
	/**
	 * @ORM\ManyToOne(targetEntity="ZombieBundle\Entity\Individu\Individu")
	 * @ORM\JoinColumn(name="individu_delete_id", referencedColumnName="id")
	 */
	protected $individuDelete;
	
	/**
	 * @ORM\ManyToMany(targetEntity="ZombieBundle\Entity\Individu\Individu", inversedBy="shared_report", cascade={"persist", "remove"})
	 * @ORM\JoinTable(name="reporting_sauvegarde_shared_with")
	 */
	protected $shared_with;
	
	public static function sort_by_name($rs1, $rs2) {
		if (false) $rs1 = $rs2 = new ReportingSauvegarde();
		
		if ((!$rs1) && (!$rs2)) return 0;
		if (!$rs1) return 1;
		if (!$rs2) return -1;
		
		$nom1 = $rs1->getNom();
		$nom2 = $rs2->getNom();
		
		if ((!$nom1) && (!$nom2)) return 0;
		if (!$nom1) return 1;
		if (!$nom2) return -1;
		
		return strcmp($nom1, $nom2);
	}

	public function __construct()
	{
		parent::__construct();
		$this->deleted = false;
		
	}

	public function getId() {
		return $this->id;
	}

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return ReportingSauvegarde
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
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return ReportingSauvegarde
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set chaineRecherche
     *
     * @param string $chaineReporting
     *
     * @return ReportingSauvegarde
     */
    public function setChaineReporting($chaineReporting)
    {
        $this->chaine_reporting = $chaineReporting;

        return $this;
    }

    /**
     * Get chaineReporting
     *
     * @return string
     */
    public function getChaineReporting()
    {
        return $this->chaine_reporting;
    }

    /**
     * Set individu
     *
     * @param Individu $individu
     *
     * @return ReportingSauvegarde
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
    
    public function getEntiteClassName() {
    	if ($this->type == 'article') return 'ZombieBundle\Entity\Article';
    	
    	return null;
    }

    public function addSearchItem($item){
        $this->chaine_recherche.=",".$item;
    }

    
    public function executeSearch($options = null) {
    	return $this->executeSearchWithSuppParams(array(), $options);
    }
    
    public function executeSearchWithSuppParams($suppParams, $options = null) {
    	$mgr = ManagersManager::getManager()->getManagerForType($this->type);
    	 
    	if ($mgr) {
    		$params = array();
    		error_log("RECHERCHE ".$this->chaine_recherche);
    		// split search string.
    		$splitted = explode(',', $this->chaine_recherche);
    		foreach ($splitted as $p) {
    			$splitted2 = explode('=', $p);
    			$key = $splitted2[0];
    			$val = $splitted2[1];
    			 
    			$params[$key] = $val;
    		}
    
    		$search_params = $mgr->getSearchParamsFromRequest($params);
    		foreach ($suppParams as $key => $val) {
    			$search_params[$key] = $val; 
    		}
    		 
    		return $mgr->search($search_params, $options);
    	}
    	 
    	return array();
    }

    /**
     * Set contextName
     *
     * @param string $contextName
     *
     * @return ReportingSauvegarde
     */
    public function setContextName($contextName)
    {
        $this->context_name = $contextName;

        return $this;
    }

    /**
     * Get contextName
     *
     * @return string
     */
    public function getContextName()
    {
        return $this->context_name;
    }
    
    /**
     * Set chaineEditables
     *
     * @param string $chaineEditables
     *
     * @return ReportingSauvegarde
     */
    public function setChaineEditables($chaineEditables)
    {
    	$this->chaine_editables = $chaineEditables;
    
    	return $this;
    }
    
    /**
     * Get chaineEditables
     *
     * @return string
     */
    public function getChaineEditables()
    {
    	return $this->chaine_editables;
    }
    
    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return ReportingSauvegarde
     */
    public function setDeleted($deleted)
    {
    	$this->deleted = $deleted;
    
    	return $this;
    }
    
    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
    	return $this->deleted;
    }
    
    /**
     * Set dateDelete
     *
     * @param \DateTime $dateDelete
     *
     * @return ReportingSauvegarde
     */
    public function setDateDelete($dateDelete)
    {
    	$this->dateDelete = $dateDelete;
    
    	return $this;
    }
    
    /**
     * Get dateDelete
     *
     * @return \DateTime
     */
    public function getDateDelete()
    {
    	return $this->dateDelete;
    }
    
    /**
     * Set individuDelete
     *
     * @param Individu $individuDelete
     *
     * @return ReportingSauvegarde
     */
    public function setIndividuDelete(Individu $individuDelete = null)
    {
    	$this->individuDelete = $individuDelete;
    
    	return $this;
    }
    
    /**
     * Get individuDelete
     *
     * @return Individu
     */
    public function getIndividuDelete()
    {
    	return $this->individuDelete;
    }
    
    public function setSharedWith($individus) {
    	$individus_orig = array();
    	if ($this->shared_with) {
    		foreach ($this->shared_with as $indiv) {
    			$individus_orig[$indiv->getId()] = $indiv;
    		}
    	}
    	 
    	$individus_new = array();
    	if ($individus) {
    		foreach ($individus as $indiv) {
    			if (!isset($individus_orig[$indiv->getId()])) {
    				$this->addSharedWith($indiv);
    			}
    			$individus_new[$indiv->getId()] = $indiv;
    		}
    	}
    	 
    	// remove other.
    	foreach ($individus_orig as $indiv) {
    		if (!isset($individus_new[$indiv->getId()])) $this->removeSharedWith($indiv);
    	}
    }
    
    /**
     * Add sharedWith
     *
     * @param Individu $sharedWith
     *
     * @return ReportingSauvegarde
     */
    public function addSharedWith(Individu $sharedWith)
    {
    	$this->shared_with[] = $sharedWith;
    
    	return $this;
    }
    
    /**
     * Remove sharedWith
     *
     * @param Individu $sharedWith
     */
    public function removeSharedWith(Individu $sharedWith)
    {
    	$this->shared_with->removeElement($sharedWith);
    }
    
    /**
     * Get sharedWith
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSharedWith()
    {
    	$res = array();
    	if ($this->shared_with) {
    		foreach ($this->shared_with as $sw) {
    			$res[] = $sw;
    		}
    	}
    	return $res;
    }
    
    public function getSharedWithByEntite() {
    	$res = array();
    	 
    	$individus = $this->getSharedWith();
    	if ($individus) {
    
    		foreach ($individus as $indiv) {
    			$entite = $indiv->getEntitePrincipale();
    			$entite_id = 0;
    			if (!$entite) {
    				$entite_id = 999999;
    			} else {
    				$entite_id = $entite->getId();
    			}
    			 
    			 
    			if (!isset($res[$entite_id])) $res[$entite_id] = array('entite' => $entite, 'individus' => array());
    			$res[$entite_id]['individus'][] = $indiv;
    		}
    	}
    	 
    	return $res;
    }
}
