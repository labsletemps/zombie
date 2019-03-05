<?php

namespace ZombieBundle\Entity\Entite;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Annotation as SER;
use Seriel\AppliToolboxBundle\Model\SerielEntiteInterface;
use Seriel\AppliToolboxBundle\Managers\ManagersManager;
use Seriel\AppliToolboxBundle\Utils\StringUtils;
use ZombieBundle\Entity\RootObject;
use ZombieBundle\Entity\Individu\IndividuEntite;
use ZombieBundle\Entity\Geo\Region;
use ZombieBundle\Entity\Fichier\FileLink;
use ZombieBundle\Entity\Fichier\File;

/**
 * @ORM\Entity
 * @ORM\Table(name="entite",options={"engine"="MyISAM"})
 * @ORM\HasLifecycleCallbacks
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="orm_type", type="string")
 * @ORM\DiscriminatorMap({ "societe" = "ZombieBundle\Entity\Entite\Societe",
 *  })
 */
abstract class ZombieEntite extends RootObject implements SerielEntiteInterface
{
	protected function __construct()
	{
		parent::__construct();
	}
	
	public static function order($entite1, $entite2) {
		if (false) $entite1 = new ZombieEntite();
		if (false) $entite2 = new ZombieEntite();
		
		if ((!$entite1) && (!$entite2)) return 0;
		if (!$entite1) return -1;
		if (!$entite2) return 1;
		
		return strcmp(trim($entite1->getNom()), trim($entite2->getNom()));
	}
	
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\OneToMany(targetEntity="ZombieBundle\Entity\Individu\IndividuEntite", mappedBy="entite", orphanRemoval=true, cascade={"persist", "remove"})
	 */
	protected $individus;
	
	/**
	 * @ORM\Column(type="string", length=300, nullable=false)
	 */
	protected $nom;
	
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
	 * @ORM\JoinColumn(name="departement_id", referencedColumnName="id")
	 */
	protected $departement;
	
	/**
	 * @ORM\ManyToOne(targetEntity="ZombieBundle\Entity\Geo\Region")
	 * @ORM\JoinColumn(name="region_id", referencedColumnName="id")
	 */
	protected $region;
	
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
	 * @ORM\Column(type="string", length=40, nullable=true)
	 */
	protected $telephone1_uid;
	
	/**
	 * @ORM\Column(type="string", length=40, nullable=true)
	 */
	protected $telephone2_uid;
	
	/**
	 * @ORM\Column(type="string", length=40, nullable=true)
	 */
	protected $fax_uid;
	
	/**
	 * @ORM\Column(type="string", length=120, nullable=true)
	 */
	protected $email;
	
	/**
	 * @ORM\Column(type="string", length=120, nullable=true)
	 */
	protected $email2;
	
	/**
	 * @ORM\Column(type="string", length=120, nullable=true)
	 */
	protected $email3;
	
	/**
	 * @ORM\Column(type="string", length=120, nullable=true)
	 */
	protected $email4;
	
	/*************** CACHES *****************/
	public function _setCacheFullAddr($fullAddr) { $this->_fullAddr = $fullAddr; }
	
	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $memo;
	
	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $infosComplementaires;
	
	/**
	 * @ORM\Column(type="string", length=1250, nullable=true)
	 */
	protected $_fullAddr;
	
	/**
	 * @ORM\Column(type="string", length=200, nullable=true)
	 */
	protected $_paysNom;
	
	/**
	 * @ORM\OneToMany(targetEntity="ZombieBundle\Entity\Fichier\FileLink", mappedBy="entite", orphanRemoval=true, cascade={"persist", "remove"})
	 */
	protected $files;
	
	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}
	

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Entite
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
     * @SER\ListeProperty("nom", label="Nom", sort="string", format="none", width=40, dbfield="fournisseur.nom")
     * @SER\ReportingColRowProperty("nom", label="Nom", sort="string", format="none")
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set addr1
     *
     * @param string $addr1
     *
     * @return Entite
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
     */
    public function getAddr1()
    {
        return $this->addr1;
    }

    /**
     * Set addr2
     *
     * @param string $addr2
     *
     * @return Entite
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
     */
    public function getAddr2()
    {
        return $this->addr2;
    }

    /**
     * Set addr3
     *
     * @param string $addr3
     *
     * @return Entite
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
     *
     * @return Entite
     */
    public function setCp($cp)
    {
        $this->cp = $cp;
        $this->updateGeoInfos();

        return $this;
    }

    /**
     * Get cp
     *
     * @return string
     * 
     * @SER\ListeProperty("cp", label="Code postal", sort="string", format="none", dbfield="fournisseur.cp")
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return Entite
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
     * @SER\ListeProperty("ville", label="Ville", sort="string", format="none", dbfield="fournisseur.ville")
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set telephone1
     *
     * @param string $telephone1
     *
     * @return Entite
     */
    public function setTelephone1($telephone1)
    {
        $this->telephone1 = $telephone1;
        
        if (!$telephone1) $this->telephone1_uid = null;
        else $this->telephone1_uid = StringUtils::phoneUid($telephone1);

        return $this;
    }

    /**
     * Get telephone1
     *
     * @return string
     * 
     * @SER\ListeProperty("telephon1", label="Téléphone", sort="string", format="tel", dbfield="fournisseur.telephone1")
     */
    public function getTelephone1()
    {
        return $this->telephone1;
    }

    /**
     * Set telephone2
     *
     * @param string $telephone2
     *
     * @return Entite
     */
    public function setTelephone2($telephone2)
    {
        $this->telephone2 = $telephone2;
        
        if (!$telephone2) $this->telephone2_uid = null;
        else $this->telephone2_uid = StringUtils::phoneUid($telephone2);

        return $this;
    }

    /**
     * Get telephone2
     *
     * @return string
     * 
     * @SER\ListeProperty("telephone2", label="Téléphone 2", sort="string", format="tel", dbfield="fournisseur.telephone2")
     */
    public function getTelephone2()
    {
        return $this->telephone2;
    }

    /**
     * Set fax
     *
     * @param string $fax
     *
     * @return Entite
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
        
        if (!$fax) $this->fax_uid = null;
        else $this->fax_uid = StringUtils::phoneUid($fax);

        return $this;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set telephone1Uid
     *
     * @param string $telephone1Uid
     *
     * @return Entite
     */
    public function setTelephone1Uid($telephone1Uid)
    {
        $this->telephone1_uid = $telephone1Uid;

        return $this;
    }

    /**
     * Get telephone1Uid
     *
     * @return string
     */
    public function getTelephone1Uid()
    {
        return $this->telephone1_uid;
    }

    /**
     * Set telephone2Uid
     *
     * @param string $telephone2Uid
     *
     * @return Entite
     */
    public function setTelephone2Uid($telephone2Uid)
    {
        $this->telephone2_uid = $telephone2Uid;

        return $this;
    }

    /**
     * Get telephone2Uid
     *
     * @return string
     */
    public function getTelephone2Uid()
    {
        return $this->telephone2_uid;
    }

    /**
     * Set faxUid
     *
     * @param string $faxUid
     *
     * @return Entite
     */
    public function setFaxUid($faxUid)
    {
        $this->fax_uid = $faxUid;

        return $this;
    }

    /**
     * Get faxUid
     *
     * @return string
     */
    public function getFaxUid()
    {
        return $this->fax_uid;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Entite
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
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email2
     *
     * @param string $email2
     *
     * @return Entite
     */
    public function setEmail2($email2)
    {
        $this->email2 = $email2;

        return $this;
    }

    /**
     * Get email2
     *
     * @return string
     */
    public function getEmail2()
    {
        return $this->email2;
    }
    
    /**
     * Set infosComplementaires
     *
     * @param string $infosComplementaires
     * @return Entite
     */
    public function setInfosComplementaires($infosComplementaires)
    {
    	$this->infosComplementaires = $infosComplementaires;
    
    	return $this;
    }
    
    /**
     * Get infosComplementaires
     *
     * @return string
     */
    public function getInfosComplementaires()
    {
    	return $this->infosComplementaires;
    }

    /**
     * Set fullAddr
     *
     * @param string $fullAddr
     *
     * @return Entite
     */
    public function setFullAddr($fullAddr)
    {
        $this->_fullAddr = $fullAddr;

        return $this;
    }

    /**
     * Get fullAddr
     *
     * @SER\ListeProperty("addr", label="Adresse", sort="string", format="none", dbfield="fournisseur._fullAddr")
     * @SER\CacheMethodProperty("addr", setter="_setCacheFullAddr")
     */
    public function getFullAddr()
    {
        return $this->_fullAddr;
    }

    /**
     * Set email3
     *
     * @param string $email3
     *
     * @return Entite
     */
    public function setEmail3($email3)
    {
        $this->email3 = $email3;

        return $this;
    }

    /**
     * Get email3
     *
     * @return string
     */
    public function getEmail3()
    {
        return $this->email3;
    }

    /**
     * Set email4
     *
     * @param string $email4
     *
     * @return Entite
     */
    public function setEmail4($email4)
    {
        $this->email4 = $email4;

        return $this;
    }

    /**
     * Get email4
     *
     * @return string
     */
    public function getEmail4()
    {
        return $this->email4;
    }

    /**
     * Add individus
     *
     * @param IndividuEntite $individus
     *
     * @return Entite
     */
    public function addIndividus(IndividuEntite $individus)
    {
        $this->individus[] = $individus;

        return $this;
    }

    /**
     * Remove individus
     *
     * @param \ZombieBundle\Entity\Individu\IndividuEntite $individus
     */
    public function removeIndividus(IndividuEntite $individus)
    {
        $this->individus->removeElement($individus);
    }

    /**
     * Get individus
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIndividus()
    {
        return $this->individus;
    }

    /**
     * Set memo
     *
     * @param string $memo
     *
     * @return Entite
     */
    public function setMemo($memo)
    {
        $this->memo = $memo;

        return $this;
    }

    /**
     * Get memo
     *
     * @return string
     */
    public function getMemo()
    {
        return $this->memo;
    }

    /**
     * Set paysNom
     *
     * @param string $paysNom
     *
     * @return Entite
     */
    public function setPaysNom($paysNom)
    {
        $this->_paysNom = $paysNom;

        return $this;
    }

    /**
     * Get paysNom
     *
     * @return string
     */
    public function getPaysNom()
    {
        return $this->_paysNom;
    }

    /**
     * Set departement
     *
     * @param Region $departement
     *
     * @return Entite
     */
    public function setDepartement(Region $departement = null)
    {
        $this->departement = $departement;

        return $this;
    }

    /**
     * Get departement
     *
     * @return Region
     */
    public function getDepartement()
    {
    	if ($this->cp && (!$this->departement)) $this->updateGeoInfos();
        return $this->departement;
    }

    /**
     * Set region
     *
     * @param Region $region
     *
     * @return Entite
     */
    public function setRegion(Region $region = null)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return Region
     */
    public function getRegion()
    {
    	if ($this->cp && (!$this->region)) $this->updateGeoInfos();
        return $this->region;
    }

    /**
     * Set pays
     *
     * @param Region $pays
     *
     * @return Entite
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
    	if ($this->cp && (!$this->pays)) $this->updateGeoInfos();
        return $this->pays;
    }
    
    public function updateGeoInfos() {
    	$cp = $this->getCp();
    	
    	if (!$cp) {
    		$this->setDepartement(null);
    		$this->setRegion(null);
    		$this->setPays(null);
    	} else {
    		$code_dep = substr($this->cp, 0, 2);
    		 
    		$regionsMgr = ManagersManager::getManager()->getManagerForType('region');
    		if (false) $regionsMgr = new RegionsManager();
    		 
    		$departement = $regionsMgr->getRegionForTypeAndCode(Region::TYPE_REGION_DEPARTEMENT, $code_dep);
    		$reg = $departement ? $departement->getRegionParent() : null;
    		$pays = $reg ? $reg->getRegionParent() : null;
    		
    		$this->setDepartement($departement);
    		$this->setRegion($reg);
    		$this->setPays($pays);
    	}
    }

    /**
     * Add file
     *
     * @param \FileLink $file
     *
     * @return Entite
     */
    public function addFileLink(FileLink $file)
    {
        $this->files[] = $file;

        return $this;
    }

    /**
     * Remove file
     *
     * @param FileLink $file
     */
    public function removeFileLink(FileLink $file)
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
    			$fileLink = new FileLink ();
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
}
