<?php

namespace ZombieBundle\Entity\Recherche;

use Doctrine\ORM\Mapping as ORM;
use ZombieBundle\Entity\RootObject;
use ZombieBundle\Entity\Individu\Individu;
use Seriel\AppliToolboxBundle\Managers\ManagersManager;
use ZombieBundle\Entity\Entite\Societe;

/**
 * @ORM\Entity
 * @ORM\Table(name="recherche_sauvegarde",options={"engine"="MyISAM"})
 */
class RechercheSauvegarde extends RootObject
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
	protected $chaine_recherche;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $mode_colonnes;
	
	/**
	 * @ORM\Column(type="string", length=2000, unique=false, nullable=true)
	 */
	protected $chaine_colonnes;
	
	/**
	 * @ORM\Column(type="string", length=2000, unique=false, nullable=true)
	 */
	protected $chaine_editables;
	
	/**
	 * @ORM\ManyToOne(targetEntity="ZombieBundle\Entity\Individu\Individu", inversedBy="saved_searches")
	 * @ORM\JoinColumn(name="individu_id", referencedColumnName="id")
	 */
	protected $individu;
	
	/**
	 * @ORM\Column(type="string", length=200, unique=false, nullable=true)
	 */
	protected $context_name;
	
	/**
	 * @ORM\Column(type="boolean", unique=false, nullable=true, options={"default":false})
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
	 * @ORM\ManyToMany(targetEntity="ZombieBundle\Entity\Individu\Individu", inversedBy="shared_search", cascade={"persist", "remove"})
	 * @ORM\JoinTable(name="recherche_sauvegarde_shared_with")
	 */
	protected $shared_with;
	
	public static function sort_by_name($rs1, $rs2) {
		if (false) $rs1 = $rs2 = new RechercheSauvegarde();
		
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
	}

	public function getId() {
		return $this->id;
	}

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return RechercheSauvegarde
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
     * @return RechercheSauvegarde
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
     * @param string $chaineRecherche
     *
     * @return RechercheSauvegarde
     */
    public function setChaineRecherche($chaineRecherche)
    {
        $this->chaine_recherche = $chaineRecherche;

        return $this;
    }

    /**
     * Get chaineRecherche
     *
     * @return string
     */
    public function getChaineRecherche()
    {
        return $this->chaine_recherche;
    }

    /**
     * Set individu
     *
     * @param Individu $individu
     *
     * @return RechercheSauvegarde
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
    	if ($this->type == 'article') return 'ZombieBundle\Entity\News\Article';
    	
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
    		//split search string.
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
     * @return RechercheSauvegarde
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
     * Set modeColonnes
     *
     * @param integer $modeColonnes
     *
     * @return RechercheSauvegarde
     */
    public function setModeColonnes($modeColonnes)
    {
        $this->mode_colonnes = $modeColonnes;

        return $this;
    }

    /**
     * Get modeColonnes
     *
     * @return integer
     */
    public function getModeColonnes()
    {
        return $this->mode_colonnes;
    }

    /**
     * Set chaineColonnes
     *
     * @param string $chaineColonnes
     *
     * @return RechercheSauvegarde
     */
    public function setChaineColonnes($chaineColonnes)
    {
        $this->chaine_colonnes = $chaineColonnes;

        return $this;
    }

    /**
     * Get chaineColonnes
     *
     * @return string
     */
    public function getChaineColonnes()
    {
        return $this->chaine_colonnes;
    }

    /**
     * Set chaineEditables
     *
     * @param string $chaineEditables
     *
     * @return RechercheSauvegarde
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
     * @return RechercheSauvegarde
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
     * @return RechercheSauvegarde
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
     * @return RechercheSauvegarde
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
    	
    	// remove others.
    	foreach ($individus_orig as $indiv) {
    		if (!isset($individus_new[$indiv->getId()])) $this->removeSharedWith($indiv);
    	}
    }

    /**
     * Add sharedWith
     *
     * @param Individu $sharedWith
     *
     * @return RechercheSauvegarde
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
    		
    		$entite_autre = new Societe();
    		$entite_autre->setNom('AUTRE');
    		

    		foreach ($individus as $indiv) {
    			$entite = $indiv->getEntitePrincipale();
    			$entite_id = 0;
    			if (!$entite) {
    				$entite = $entite_autre;
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
