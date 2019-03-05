<?php

namespace ZombieBundle\Entity\Individu;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Annotation as SER;
use ZombieBundle\Entity\RootObject;
use ZombieBundle\Entity\Geo\Region;
use ZombieBundle\Entity\Fichier\FileLink;
use ZombieBundle\Entity\Fichier\File;
use ZombieBundle\Entity\Recherche\RechercheSauvegarde;
use ZombieBundle\Entity\Reporting\ReportingSauvegarde;
use ZombieBundle\Entity\Securite\IndividuProfil;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="individu",options={"engine"="MyISAM"},indexes={@ORM\Index(name="deleted_individu_idx", columns={"deleted"})})
  * @ORM\HasLifecycleCallbacks
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="orm_type", type="string")
 * @ORM\DiscriminatorMap({ "user" = "ZombieBundle\Entity\Individu\Utilisateur",
 *                         "invite" = "ZombieBundle\Entity\Individu\Invite",
 *  })
 * @UniqueEntity("email")
 */
abstract class Individu extends RootObject
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
	 * @ORM\ManyToOne(targetEntity="Civilite")
	 * @ORM\JoinColumn(name="civilite_id", referencedColumnName="id")
	 */
	protected $civilite;

	/**
	 * @ORM\Column(type="string", length=300, nullable=false)
	 */
	protected $nom;
	
	/**
	 * @ORM\Column(type="string", length=300, nullable=true)
	 */
	protected $prenom;
	
	/**
	 * @ORM\Column(type="string", length=601, nullable=true)
	 */
	protected $nom_complet;
	
	/**
	 * @ORM\Column(type="string", length=400, nullable=true)
	 */
	protected $addr1;
	
	/**
	 * @ORM\Column(type="string", length=400, nullable=true)
	 */
	protected $addr2;
	
	/**
	 * @ORM\Column(type="string", length=400, nullable=true)
	 */
	protected $addr3;
	
	/**
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	protected $cp;
	
	/**
	 * @ORM\Column(type="string", length=400, nullable=true)
	 */
	protected $ville;
	
	/**
	 * @ORM\ManyToOne(targetEntity="ZombieBundle\Entity\Geo\Region")
	 * @ORM\JoinColumn(name="pays_id", referencedColumnName="id")
	 */
	protected $pays;
	
	/**
	 * @ORM\Column(type="string", length=40, nullable=true)
	 */
	protected $telephone1;
	
	/**
	 * @ORM\Column(type="string", length=40, nullable=true)
	 */
	protected $telephone2;
	
	/**
	 * @ORM\Column(type="string", length=40, nullable=true)
	 */
	protected $fax;
	
	/**
	 * @ORM\Column(type="string", length=120, nullable=true)
	 */
	protected $email;
	
	/**
	 * @ORM\Column(type="date", nullable=true)
	 */
	protected $dateNaissance;
	
	/**
	 * @ORM\OneToMany(targetEntity="ZombieBundle\Entity\Securite\IndividuProfil", mappedBy="individu", orphanRemoval=true, cascade={"persist", "remove"})
	 */
	protected $profils;
	
	/**
	 * @ORM\OneToOne(targetEntity="Seriel\UserBundle\Entity\User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 **/
	protected $user;
	
	/**
	 * @ORM\OneToMany(targetEntity="ZombieBundle\Entity\Recherche\RechercheSauvegarde", mappedBy="individu", cascade={"persist", "remove"})
	 */
	protected $saved_searches;
	
	/**
	 * @ORM\OneToMany(targetEntity="ZombieBundle\Entity\Reporting\ReportingSauvegarde", mappedBy="individu", cascade={"persist", "remove"})
	 */
	protected $saved_reports;
	
	/**
	 * @ORM\Column(type="boolean", nullable=true,options={"default": false})
	 */
	protected $contact_principal;
	
	/**
	 * @ORM\Column(type="boolean", nullable=true,options={"default": false})
	 */
	protected $is_regisseur;
	
	/**
	 * @ORM\Column(type="boolean", unique=false, nullable=true, options={"default":false})
	 */
	protected $deleted;
	
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	protected $dateDelete;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Individu")
	 * @ORM\JoinColumn(name="individu_delete_id", referencedColumnName="id")
	 */
	protected $individuDelete;
	
	/**
	 * @ORM\ManyToMany(targetEntity="ZombieBundle\Entity\Recherche\RechercheSauvegarde", mappedBy="shared_with")
	 */
	protected $shared_search;
	
	/**
	 * @ORM\ManyToMany(targetEntity="ZombieBundle\Entity\Reporting\ReportingSauvegarde", mappedBy="shared_with")
	 */
	protected $shared_report;
	
	/**
	 * @ORM\OneToMany(targetEntity="IndividuEntite", mappedBy="individu", cascade={"persist"})
	 */
	protected $entites;
	
	/**
	 * @ORM\OneToMany(targetEntity="ZombieBundle\Entity\Fichier\FileLink", mappedBy="individu", orphanRemoval=true, cascade={"persist", "remove"})
	 */
	protected $files;
	
	public static function sortByName($ind1, $ind2) {
		if ((!$ind1) && (!$ind2)) return 0;
		if (!$ind1) return -1;
		if (!$ind2) return 1;
		
 		$nom1 = $ind1->getNom();
 		$nom2 = $ind2->getNom();
 		
 		if ($nom1 && $nom2) {
 			$cmp = strcmp($nom1, $nom2);
 			if ($cmp != 0) return $cmp;
 		} else if (!$nom1) {
 			return -1;
 		} else if (!$nom2) {
 			return 1;
 		}
 		
 		$prenom1 = $ind1->getPrenom();
 		$prenom2 = $ind2->getPrenom();
 			
 		if ($prenom1 && $prenom2) {
 			$cmp = strcmp($prenom1, $prenom2);
 			if ($cmp != 0) return $cmp;
 		} else if (!$prenom1) {
 			return -1;
 		} else if (!$prenom2) {
 			return 1;
 		}
 		
 		return 0;
	}
	
	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

    /**
     * Set civilite
     *
     * @param Civilite $civilite
     * @return Individu
     */
    public function setCivilite($civilite)
    {
        $this->civilite = $civilite;

        return $this;
    }

    /**
     * Get civilite
     *
     * @return Civilite
     * 
     */
    public function getCivilite()
    {
        return $this->civilite;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Individu
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
        $this->updateNomComplet();

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     * 
     * @SER\ListeProperty("nom", label="Nom", sort="string", format="none", dbfield="nom")
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     * @return Individu
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
        $this->updateNomComplet();

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     * 
     * @SER\ListeProperty("prenom", label="Prénom", sort="string", format="none", dbfield="prenom")
     */
    public function getPrenom()
    {
        return $this->prenom;
    }
    
    /**
     * Get nom_complet
     *
     * @return string
     * 
     */
    public function getNomComplet()
    {
    	$this->updateNomComplet();
    	return $this->nom_complet;
    }
    
    protected function updateNomComplet() {
    	$n = $this->nom ? $this->nom : '';
    	$p = $this->prenom ? $this->prenom : '';
    	
    	$nc = trim($p.' '.$n);
    	
    	$this->nom_complet = $nc;
    }

    /**
     * Set addr1
     *
     * @param string $addr1
     * @return Individu
     */
    public function setAddr1($addr1)
    {
        $this->addr1 = $addr1;

        return $this;
    }

    /**
     * Get addr1
     *
     * @return string
     * 
     */
    public function getAddr1()
    {
        return $this->addr1;
    }

    /**
     * Set addr2
     *
     * @param string $addr2
     * @return Individu
     */
    public function setAddr2($addr2)
    {
        $this->addr2 = $addr2;

        return $this;
    }

    /**
     * Get addr2
     *
     * @return string 
     * 
     */
    public function getAddr2()
    {
        return $this->addr2;
    }

    /**
     * Set addr3
     *
     * @param string $addr3
     * @return Individu
     */
    public function setAddr3($addr3)
    {
        $this->addr3 = $addr3;

        return $this;
    }

    /**
     * Get addr3
     *
     * @return string 
     */
    public function getAddr3()
    {
        return $this->addr3;
    }

    /**
     * Set cp
     *
     * @param string $cp
     * @return Individu
     */
    public function setCp($cp)
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * Get cp
     *
     * @return string 
     * 
      */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * Set ville
     *
     * @param string $ville
     * @return Individu
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string 
     * 
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set telephone1
     *
     * @param string $telephone1
     * @return Individu
     */
    public function setTelephone1($telephone1)
    {
        $this->telephone1 = $telephone1;

        return $this;
    }

    /**
     * Get telephone1
     *
     * @return string
     * 
    */
    public function getTelephone1()
    {
        return $this->telephone1;
    }

    /**
     * Set telephone2
     *
     * @param string $telephone2
     * @return Individu
     */
    public function setTelephone2($telephone2)
    {
        $this->telephone2 = $telephone2;

        return $this;
    }

    /**
     * Get telephone2
     *
     * @return string
     * 
     */
    public function getTelephone2()
    {
        return $this->telephone2;
    }

    /**
     * Set fax
     *
     * @param string $fax
     * @return Individu
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string 
     * 
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Individu
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     * 
     * @SER\ListeProperty("email", label="Email", sort="string", format="none", dbfield="email")
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     * @return Individu
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime
     * 
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set pays
     *
     * @param Region $pays
     * @return Individu
     */
    public function setPays(Region $pays = null)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return Region 
     */
    public function getPays()
    {
        return $this->pays;
    }
    
    
    public function __toString() {
        return trim($this->getPrenom().' '.$this->getNom());
    }

    /**
     * Set user
     *
     * @param \Seriel\UserBundle\Entity\User $user
     *
     * @return Individu
     */
    public function setUser(\Seriel\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Seriel\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    public function getNiceName() {
    	return trim($this->prenom.' '.$this->nom);
    }
    
    public function getNomPrenom() {
    	$nom = strtoupper($this->nom);
    	$prenom = ucwords(strtolower($this->prenom));
    	
    	$splitted = explode('-', $prenom);
    	$elems = array();
    	foreach ($splitted as $split) {
    		$elems[] = ucfirst($split);
    	}
    	$prenom = implode('-', $elems);
    	
    	return trim($nom.' '.$prenom);
    }
    
    public function emptyProfils() {
    	if ($this->profils) {
    		foreach ($this->profils as $profil) {
    			$this->removeProfil($profil);
    		}
    	}
    }

    /**
     * Add profil
     *
     * @param IndividuProfil $profil
     *
     * @return Individu
     */
    public function addProfil(IndividuProfil $profil)
    {
        $this->profils[] = $profil;

        return $this;
    }

    /**
     * Remove profil
     *
     * @param IndividuProfil $profil
     */
    public function removeProfil(IndividuProfil $profil)
    {
        $this->profils->removeElement($profil);
    }

    /**
     * Get profils
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProfils()
    {
    	if ($this->profils) return $this->profils;
        return array();
    }
    
    public function getProfilsByProfilId() {
    	$profilsById = array();
    	
    	if ($this->profils) {
    		foreach ($this->profils as $indiv_profil) {
    			if (false) $indiv_profil = new IndividuProfil();
    			$profil = $indiv_profil->getProfil();
    			if ($profil) {
    				$profilsById[$profil->getId()] = $profil;
    			}
    		}
    	}
    	
    	return $profilsById;
    }
    
    /**
     */
    public function getProfilsStr() {
    	$res = array();
    	if ($this->profils) {
    		foreach ($this->profils as $prof) {
    			$res[] = ''.$prof;
    		}
    		
    		return implode(', ', $res);
    	}
    	
    	return "";
    }
 
    public function setProfil(IndividuProfil $profil = null) {
    	if ($this->profils) {
    		foreach ($this->profils as $prof) {
    			$this->removeProfil($prof);
    		}
    	}
  
    	if (isset($profil)) $this->profils[] = $profil;
    }
    
    public function getProfilPrincipal() {
    	if ($this->profils) {
    		foreach ($this->profils as $individuprofil) {
    			if (false) $individuprofil= new IndividuProfil();
    			$profil = $individuprofil->getProfil();
    			
    			if ($profil) return $profil;
    		}
    	}
    }
    
    /**
     * Add savedSearch
     *
     * @param \ZombieBundle\Entity\Recherche\RechercheSauvegarde $savedSearch
     *
     * @return Individu
     */
    public function addSavedSearch(\ZombieBundle\Entity\Recherche\RechercheSauvegarde $savedSearch)
    {
        $this->saved_searches[] = $savedSearch;

        return $this;
    }

    /**
     * Remove savedSearch
     *
     * @param \ZombieBundle\Entity\Recherche\RechercheSauvegarde $savedSearch
     */
    public function removeSavedSearch(\ZombieBundle\Entity\Recherche\RechercheSauvegarde $savedSearch)
    {
        $this->saved_searches->removeElement($savedSearch);
    }

    /**
     * Get savedSearches
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSavedSearches()
    {
        return $this->saved_searches;
    }
    
    public function getContactPrincipal(){
    	return $this->contact_principal;
    }
    
    public function setContactPrincipal($contact) {
    	$this->contact_principal = $contact;
    	return $this;
    }
    
    public function isGestionnaire() {
    	return ($this instanceof Gestionnaire);
    }
    
    public function isRegisseur() {
    	return $this->is_regisseur;
    }
    
    public function setIsRegisseur($bool) {
    	$this->is_regisseur = $bool;
    	return $this;
    }
    
    /**
     * Get isRegisseur
     *
     * @return boolean
     */
    public function getIsRegisseur()
    {
        return $this->is_regisseur;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return Individu
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
     * @return Individu
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
     * @param \ZombieBundle\Entity\Individu\Individu $individuDelete
     *
     * @return Individu
     */
    public function setIndividuDelete(\ZombieBundle\Entity\Individu\Individu $individuDelete = null)
    {
        $this->individuDelete = $individuDelete;

        return $this;
    }

    /**
     * Get individuDelete
     *
     * @return \ZombieBundle\Entity\Individu\Individu
     */
    public function getIndividuDelete()
    {
        return $this->individuDelete;
    }

    /**
     * Set nomComplet
     *
     * @param string $nomComplet
     *
     * @return Individu
     */
    public function setNomComplet($nomComplet)
    {
        $this->nom_complet = $nomComplet;

        return $this;
    }

    public function getMainEntity() {
    	return $this->getEntitePrincipale();
    }
    public function getEntitePrincipale() {
    	// TODO : better
    	if ($this->entites) {
    		foreach ($this->entites as $entiteLink) {
    			if (false) $entiteLink = new IndividuEntite();
    			$entite = $entiteLink->getEntite();
    			 
    			if ($entite) return $entite;
    		}
    	}
    }
    
    public function getEntiteLinkForEntiteAndFonction($entite, $fonction) {
    	if (!$entite) return null;
    	if ($entite instanceof Entite) $entite = $entite->getId();
    	
    	if ($fonction && $fonction instanceof Fonction) $fonction = $fonction->getId();
    	 
    	if ($this->entites) {
    		foreach ($this->entites as $entiteLink) {
    			if (false) $entiteLink = new IndividuEntite();
    			if ($entiteLink->getEntite()->getId() == $entite) {

    				// first step is good, now verify the function
    				$entiteFonction = $entiteLink->getFonction();
    				if (!$fonction && !$entiteFonction) return $entiteLink;
    				if ($entiteFonction && $entiteFonction->getId() == $fonction) return $entiteLink;
    				
    			}
    		}
    	}
    	 
    	return null;
    }

    /**
     * Add entite
     *
     * @param IndividuEntite $entite
     *
     * @return Individu
     */
    public function addEntite(IndividuEntite $entite)
    {
        $this->entites[] = $entite;

        return $this;
    }

    /**
     * Remove entite
     *
     * @param IndividuEntite $entite
     */
    public function removeEntite(IndividuEntite $entite)
    {
        $this->entites->removeElement($entite);
    }

    /**
     * Get entites
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEntites()
    {
        return $this->entites;
    }

    /**
     * Add savedReport
     *
     * @param \ZombieBundle\Entity\Reporting\ReportingSauvegarde $savedReport
     *
     * @return Individu
     */
    public function addSavedReport(\ZombieBundle\Entity\Reporting\ReportingSauvegarde $savedReport)
    {
        $this->saved_reports[] = $savedReport;

        return $this;
    }

    /**
     * Remove savedReport
     *
     * @param \ZombieBundle\Entity\Reporting\ReportingSauvegarde $savedReport
     */
    public function removeSavedReport(\ZombieBundle\Entity\Reporting\ReportingSauvegarde $savedReport)
    {
        $this->saved_reports->removeElement($savedReport);
    }

    /**
     * Get savedReports
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSavedReports()
    {
        return $this->saved_reports;
    }

    /**
     * Add file
     *
     * @param \ZombieBundle\Entity\Fichier\FileLink $file
     *
     * @return Individu
     */
    public function addFileLink(\ZombieBundle\Entity\Fichier\FileLink $file)
    {
        $this->files[] = $file;

        return $this;
    }

    /**
     * Remove file
     *
     * @param \ZombieBundle\Entity\Fichier\FileLink $file
     */
    public function removeFileLink(\ZombieBundle\Entity\Fichier\FileLink $file)
    {
        $this->files->removeElement($file);
    }
    
    /**
     * Get fileLinks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    protected function getFileLinks() {
    	return $this->files;
    }
    protected function getFileLinkFromFileId($id) {
    	foreach ( $this->files as $fileLink ) {
    		if (false)
    			$fileLink = new FileLink();
    			if ($fileLink->getFileId () == $id) {
    				return $fileLink;
    			}
    	}
    
    	return null;
    }
    
    /**
     * Add file
     *
     * @param File $file
     * @return ModelArticle
     */
    public function addFile(File $file) {
    	$fileLink = $this->getFileLinkFromFileId ( $file->getId () );
    	if ($fileLink)
    		return;
    
    		$fileLink = new FileLink();
    		$fileLink->setFile ( $file );
    		$fileLink->setIntervention ( $this );
    		$this->addFileLink ( $fileLink );
    
    		return $this;
    }
    
    /**
     * Remove file
     *
     * @param File $file
     */
    public function removeFile(File $file) {
    	$fileLink = $this->getFileLinkFromFileId ( $file->getId () );
    	if (! $fileLink)
    		return;
    
    	return $this->removeFileLink ( $fileLink );
    }

    public function getFiles() {
		$res = array ();
		if ($this->files) {
			foreach ( $this->files as $fileLink ) {
				$res [] = $fileLink->getFile ();
			}
		}
		return $res;
	}

    /**
     * Add sharedSearch
     *
     * @param RechercheSauvegarde $sharedSearch
     *
     * @return Individu
     */
    public function addSharedSearch(RechercheSauvegarde $sharedSearch)
    {
    	$this->shared_search[] = $sharedSearch;
    
    	return $this;
    }
    
    /**
     * Remove sharedSearch
     *
     * @param ZombieBundle\Entity\Recherche\RechercheSauvegarde $sharedSearch
     */
    public function removeSharedSearch(RechercheSauvegarde $sharedSearch)
    {
    	$this->shared_search->removeElement($sharedSearch);
    }
    
    /**
     * Get sharedSearch
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSharedSearch()
    {
    	$res = array();
    
    	if ($this->shared_search) {
    		foreach ($this->shared_search as $search) {
    			if ($search->getDeleted()) continue;
    			$res[] = $search;
    		}
    	}
    
    	return $res;
    }
    
    public function getSharedSearchByType() {
    	$res = array();
    	 
    	$searches = $this->getSharedSearch();
    	 
    	if ($searches) {
    		foreach ($searches as $search) {
    			if (false) $search = new RechercheSauvegarde();
    			 
    			$type = $search->getType();
    			 
    			if (!isset($res[$type])) $res[$type] = array();
    			 
    			$res[$type][] = $search;
    		}
    	}
    	 
    	return $res;
    }
    
    /**
     * Add sharedReport
     *
     * @param ZombieBundle\Entity\Reporting\ReportingSauvegarde $sharedReport
     *
     * @return Individu
     */
    public function addSharedReport(\ZombieBundle\Entity\Reporting\ReportingSauvegarde $sharedReport)
    {
    	$this->shared_report[] = $sharedReport;
    
    	return $this;
    }
    
    /**
     * Remove sharedReport
     *
     * @param ZombieBundle\Entity\Reporting\ReportingSauvegarde $sharedReport
     */
    public function removeSharedReport(\ZombieBundle\Entity\Reporting\ReportingSauvegarde $sharedReport)
    {
    	$this->shared_report->removeElement($sharedReport);
    }
    
    /**
     * Get sharedReport
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSharedReport()
    {
    	$res = array();
    
    	if ($this->shared_report) {
    		foreach ($this->shared_report as $report) {
    			if ($report->getDeleted()) continue;
    			$res[] = $report;
    		}
    	}
    
    	return $res;
    }
    
    public function getSharedReportByType() {
    	$res = array();
    
    	$reports = $this->getSharedReport();
    
    	if ($reports) {
    		foreach ($reports as $report) {
    			if (false) $report = new ReportingSauvegarde();
    
    			$type = $report->getType();
    
    			if (!isset($res[$type])) $res[$type] = array();
    
    			$res[$type][] = $report;
    		}
    	}
    
    	return $res;
    }
    
    public function getSharedReportByTypeActive() {
    	$res = array();
    	
    	$reports = $this->getSharedReport();
    	
    	if ($reports) {
    		foreach ($reports as $report) {
    			if ($report->getDeleted()!= true) {
    				if (false) $report = new ReportingSauvegarde();
    				
    				$type = $report->getType();
    				
    				if (!isset($res[$type])) $res[$type] = array();
    				
    				$res[$type][] = $report;
    			}

    		}
    	}
    	
    	return $res;
    }
}
