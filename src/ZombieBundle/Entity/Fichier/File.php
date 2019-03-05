<?php

namespace ZombieBundle\Entity\Fichier;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Managers\ManagersManager;
use Seriel\AppliToolboxBundle\Entity\CredentialMultiChoice;
use ZombieBundle\Entity\RootObject;
use ZombieBundle\Managers\Securite\SecurityManager;
use ZombieBundle\Entity\Securite\ZombieProfil;

/**
 * @ORM\Entity
 * @ORM\Table(name="fichier",options={"engine"="MyISAM"})
 */
class File extends RootObject
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
	protected $nom;
	
	/**
	 * @ORM\Column(type="string", length=40, unique=false, nullable=true)
	 */
	protected $extension;
	
	/**
	 * @ORM\Column(type="string", length=100, unique=false, nullable=true)
	 */
	protected $mime;
	
	/**
	 * @ORM\Column(type="integer", length=255, unique=false, nullable=false)
	 */
	protected $size;
	
	/**
	 * @ORM\Column(type="string", length=50, unique=false, nullable=true)
	 */
	protected $md5;
	
	/**
	 * @ORM\Column(type="boolean", nullable=false, unique=false, options={"default": false})
	 */
	protected $has_thumbnail;
	
	/**
	 * @ORM\OneToOne(targetEntity="ZombieBundle\Entity\Fichier\FileData", cascade={"persist", "remove"})
	 **/
	protected $file_data;
	
	/**
	 * @ORM\OneToMany(targetEntity="FileLink", mappedBy="file", cascade={"persist", "remove"})
	 */
	protected $file_links;
	
	/**
	 * @ORM\OneToMany(targetEntity="FileVisibiliteProfil", mappedBy="file", orphanRemoval=true, cascade={"persist", "remove"})
	 **/
	protected $visibilites_profil;
	
	/**
	 * @ORM\Column(type="string", length=300, nullable=true)
	 */
	protected $option1;
	
	/**
	 * @ORM\Column(type="string", length=300, nullable=true)
	 */
	protected $option2;
	
	/**
	 * @ORM\Column(type="string", length=300, nullable=true)
	 */
	protected $option3;
	
	/**
	 * @ORM\Column(type="string", length=300, nullable=true)
	 */
	protected $option4;
	
	/**
	 * @ORM\Column(type="string", length=300, nullable=true)
	 */
	protected $option5;
	
	/**
	 * @ORM\Column(type="string", length=300, nullable=true)
	 */
	protected $option6;

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
     * @return File
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
     * Set extension
     *
     * @param string $extension
     * @return File
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string 
     */
    public function getExtension()
    {
        return $this->extension;
    }
    
    public function getNomComplet() {
    	if (!$this->extension) return $this->nom;
    	return $this->nom.'.'.$this->extension;
    }

    /**
     * Set mime
     *
     * @param string $mime
     * @return File
     */
    public function setMime($mime)
    {
        $this->mime = $mime;

        return $this;
    }

    /**
     * Get mime
     *
     * @return string 
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return File
     */
    protected function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set md5
     *
     * @param string $md5
     * @return File
     */
    protected function setMd5($md5)
    {
        $this->md5 = $md5;

        return $this;
    }

    /**
     * Get md5
     *
     * @return string 
     */
    public function getMd5()
    {
        return $this->md5;
    }


    /**
     * Set file_data
     *
     * @param FileData $fileData
     * @return File
     */
    protected function setFileData(ZombieBundle\Entity\Fichier\FileData $fileData = null)
    {
        $this->file_data = $fileData;

        return $this;
    }

    /**
     * Get file_data
     *
     * @return FileData 
     */
    protected function getFileData()
    {
        return $this->file_data;
    }
    
    public function setBinaryData($binary_data) {
    	$fileData = $this->getFileData();
    	if (!$fileData) {
    		$fileData = new FileData();
    		$fileData->setFile($this);
    		$this->setFileData($fileData);
    	}
    	$fileData->setData($binary_data);
    	
    	$this->setSize(strlen($binary_data));
    	$this->setMd5(md5($binary_data));
    	
    	$finfo = new \finfo(FILEINFO_MIME);
		$mime = $finfo->buffer($binary_data);
		
		if ($mime) {
			$splitted = explode(';', $mime);
			$mime = $splitted[0];
			$this->setMime($mime);
		}
    }
    
	public function getBinaryData() {
		$fileData = $this->getFileData();
		if (!$fileData) return '';
		
		return $fileData->getData();
	}
	

    /**
     * Add fileLink
     *
     * @param FileLink $fileLink
     *
     * @return File
     */
    public function addFileLink(FileLink $fileLink)
    {
        $this->file_links[] = $fileLink;

        return $this;
    }

    /**
     * Remove fileLink
     *
     * @param FileLink $fileLink
     */
    public function removeFileLink(FileLink $fileLink)
    {
        $this->file_links->removeElement($fileLink);
    }

    /**
     * Get fileLinks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFileLinks()
    {
        return $this->file_links;
    }
    
    protected function getRootFileDirectory() {
    	return ManagersManager::getManager()->getContainer()->get('kernel')->getRootDir().'/../web/documents/files/';
    }
    protected function getRootTumbnailDirectory() {
    	return ManagersManager::getManager()->getContainer()->get('kernel')->getRootDir().'/../web/documents/thumbnails/';
    }
    
    public function getFilePathAndName() {
    	return $this->md5.'/'.$this->getNomComplet();
    }
    
    public function moveSrcFileToDest($src_file) {
    	$root = $this->getRootFileDirectory();
    	 
    	// Repertoy MD5 exist ?
    	$dir = $root.$this->md5;
    	if (!file_exists($dir)) {
    		mkdir($dir, 0777);
    		chmod($dir, 0777);
    	}
    	 
    	// move file.
    	$dest_file_name = $dir.'/'.$this->getNomComplet();
    	if (file_exists($dest_file_name)) unlink($dest_file_name);
    	rename($src_file, $dest_file_name);
    	
    	chmod($dest_file_name, 0777);
    }
    
    public function fileExistsOnFS() {
    	$root = $this->getRootFileDirectory();
    	
    	$dir = $root.$this->md5;
    	$dest_file_name = $dir.'/'.$this->getNomComplet();
    	
    	if (file_exists($dest_file_name)) return true;
    	
    	return false;
    }
    
    public function writeFileToFS($content = null) {
    	$root = $this->getRootFileDirectory();
    	
    	// Repertoy MD5 exist ?
    	$dir = $root.$this->md5;
    	if (!file_exists($dir)) {
    		mkdir($dir, 0777);
    		chmod($dir, 0777);
    	}
    	
    	// write content
    	if (!$content) $content = $this->getBinaryData();
    	$dest_file_name = $dir.'/'.$this->getNomComplet();
    	file_put_contents($dest_file_name, $content);
    	chmod($dest_file_name, 0777);
    }
    
    public function createThumbnail() {
    	$root = $this->getRootTumbnailDirectory();
    	
    	$srcFile = $this->getRootFileDirectory().$this->getFilePathAndName();
    	 
    	// Repertoy MD5 exist ?
    	$dir = $root.$this->md5;
    	
    	$thumbnail_failed = false;
    	
    	try {
    		$imagick = new \Imagick();
    		$imagick->setbackgroundcolor('white');
    		if (strtoupper($this->extension) == 'PDF') $imagick->readimage($srcFile.'[0]');
    		else $imagick->readimage($srcFile);
    		
    		
    		$width = $size = $imagick->getimagewidth();
    		$height = $imagick->getimageheight();
    		
    		$ratio = $height / $width;

    		
    		$thumb_width = 144;
    		$thumb_height = 201;
    		
    		if ($thumb_width * $ratio < $thumb_height) {
    			$thumb_width = ceil($thumb_height / $ratio);
    		} else {
    			$thumb_height = $thumb_width * $ratio;
    		}
    		

    		$imagick->thumbnailImage($thumb_width, $thumb_height, true, false);
    		$imagick = $imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
    		
    		if (!file_exists($dir)) {
    			mkdir($dir, 0777);
    			chmod($dir, 0777);
    		}
    	
    		$dest_file_name = $dir.'/'.$this->md5.'.jpg';
    		
    		if (file_exists($dest_file_name)) unlink($dest_file_name);
    		$imagick->writeimage($dest_file_name);
    		
    		$imagick->destroy();
    		
    		chmod($dest_file_name, 0777);
    	} catch (\ImagickException $ex) {
    		//  thumbnail is not create.
    		error_log($ex->getCode()." >> ".$ex->getMessage());
    		$thumbnail_failed = true;
    	}
    	
    	$this->setHasThumbnail(!$thumbnail_failed);
    }

    /**
     * Set hasThumbnail
     *
     * @param boolean $hasThumbnail
     *
     * @return File
     */
    public function setHasThumbnail($hasThumbnail)
    {
        $this->has_thumbnail = $hasThumbnail;

        return $this;
    }

    /**
     * Get hasThumbnail
     *
     * @return boolean
     */
    public function hasThumbnail()
    {
        return $this->has_thumbnail;
    }

    public function getThumbnailFilePath() {
    	return '/documents/thumbnails/'.$this->md5.'/'.$this->md5.'.jpg';
    }
    
    public function getFilePath() {
    	return '/documents/files/'.$this->getFilePathAndName();
    }

    /**
     * Get hasThumbnail
     *
     * @return boolean
     */
    public function getHasThumbnail()
    {
        return $this->has_thumbnail;
    }

    /**
     * Set option1
     *
     * @param string $option1
     *
     * @return File
     */
    public function setOption1($option1)
    {
        $this->option1 = $option1;

        return $this;
    }

    /**
     * Get option1
     *
     * @return string
     */
    public function getOption1()
    {
        return $this->option1;
    }

    /**
     * Set option2
     *
     * @param string $option2
     *
     * @return File
     */
    public function setOption2($option2)
    {
        $this->option2 = $option2;

        return $this;
    }

    /**
     * Get option2
     *
     * @return string
     */
    public function getOption2()
    {
        return $this->option2;
    }

    /**
     * Set option3
     *
     * @param string $option3
     *
     * @return File
     */
    public function setOption3($option3)
    {
        $this->option3 = $option3;

        return $this;
    }

    /**
     * Get option3
     *
     * @return string
     */
    public function getOption3()
    {
        return $this->option3;
    }

    /**
     * Set option4
     *
     * @param string $option4
     *
     * @return File
     */
    public function setOption4($option4)
    {
        $this->option4 = $option4;

        return $this;
    }

    /**
     * Get option4
     *
     * @return string
     */
    public function getOption4()
    {
        return $this->option4;
    }

    /**
     * Set option5
     *
     * @param string $option5
     *
     * @return File
     */
    public function setOption5($option5)
    {
        $this->option5 = $option5;

        return $this;
    }

    /**
     * Get option5
     *
     * @return string
     */
    public function getOption5()
    {
        return $this->option5;
    }

    /**
     * Set option6
     *
     * @param string $option6
     *
     * @return File
     */
    public function setOption6($option6)
    {
        $this->option6 = $option6;

        return $this;
    }

    /**
     * Get option6
     *
     * @return string
     */
    public function getOption6()
    {
        return $this->option6;
    }
    
    /**
     * Add visibilitesProfil
     *
     * @param FileVisibiliteProfil $visibilitesProfil
     *
     * @return Evenement
     */
    public function addVisibilitesProfil(FileVisibiliteProfil $visibilitesProfil)
    {
    	$this->visibilites_profil[] = $visibilitesProfil;
    
    	return $this;
    }
    
    /**
     * Remove visibilitesProfil
     *
     * @param FileVisibiliteProfil $visibilitesProfil
     */
    public function removeVisibilitesProfil(FileVisibiliteProfil $visibilitesProfil)
    {
    	$this->visibilites_profil->removeElement($visibilitesProfil);
    }
    
    /**
     * Get visibilitesProfil
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVisibilitesProfil()
    {
    	return $this->visibilites_profil;
    }
    
    public function hasVisibilityLinkForAtLeastOneProfilId($profils_ids) {
    	if (!$profils_ids) return false;
    	 
    	// Lets build a map.
    	$idsMap = array();
    	foreach ($profils_ids as $id) {
    		$idsMap[$id] = $id;
    	}
    	 
    	if ($this->visibilites_profil) {
    		foreach ($this->visibilites_profil as $visProfil) {
    			if (false) $visProfil = new FileVisibiliteProfil();
    			if ($visProfil->getVisible() == false) continue;
    			$prof_id = $visProfil->getProfil()->getId();
    			 
    			if (isset($idsMap[$prof_id])) return true;
    		}
    	}
    	 
    	return false;
    }
    
    public function hasVisibilityLinkForCurrentUser() {
    	$securityMgr = ManagersManager::getManager()->getContainer()->get('security_manager');
    	if (false) $securityMgr = new SecurityManager();
    	 
    	$individu = $securityMgr->getCurrentIndividu();
    	if (!$individu) return false;
    	 
    	if ($individu instanceof Gestionnaire) return true;
    	
    	//  if is in base co, algorithm is different .
    	$base_de_co = $this->getLinkBaseDeCo();
    	if ($base_de_co) return true;
    	$base_de_co_rep = $this->getLinkRepBaseDeCo();
    	if ($base_de_co_rep) return true;
    	$mat = $this->getLinkMateriel();
    	if ($mat) return true;
    	
    	 
    	// get all profil for the individu
    	$profilsById = $individu->getProfilsByProfilId();
    	$profils_ids = $profilsById ? array_keys($profilsById) : array();
    	 
    	return $this->hasVisibilityLinkForAtLeastOneProfilId($profils_ids);
    }
    
    public function getLinkBaseDeCo() {
    	if ($this->file_links) {
    		foreach ($this->file_links as $link) {
    			if (false) $link = new FileLink();
    			$bdc = $link->getBaseDeCo();
    			
    			if ($bdc) return $bdc;
    		}
    	}
    	return null;
    }
    
    public function getLinkRepBaseDeCo() {
    	if ($this->file_links) {
    		foreach ($this->file_links as $link) {
    			if (false) $link = new FileLink();
    			$bdcr = $link->getBaseDeCoRep();
    			 
    			if ($bdcr) return $bdcr;
    		}
    	}
    	return null;
    }
    
    public function getLinkMateriel() {
    	if ($this->file_links) {
    		foreach ($this->file_links as $link) {
    			if (false) $link = new FileLink();
    			$mat = $link->getMateriel();
    			 
    			if ($mat) return $mat;
    		}
    	}
    	return null;
    }
    
    public function setVisibilityProfils($profils) {
    	$orig_visibilities = $this->getVisibilitesProfil();
    
    	$orig_visibilities_map = array();
    	if ($orig_visibilities) {
    		foreach ($orig_visibilities as $visi) {
    			if (false) $visi = new FileVisibiliteProfil();
    			$orig_visibilities_map[$visi->getProfil()->getId()] = $visi;
    		}
    	}
    	 
    	$new_visibilities_map = array();
    	if ($profils) {
    		foreach ($profils as $profil) {
    			$profil_id = $profil->getId();
    			$new_visibilities_map[$profil_id] = $profil_id;
    			 
    			if (isset($orig_visibilities_map[$profil_id])) continue;
    
    			$file_vis_profil = new FileVisibiliteProfil ();
    			$file_vis_profil->setFile ( $this );
    			$file_vis_profil->setProfil ( $profil );
    			$file_vis_profil->setVisible ( true );
    				
    			$this->addVisibilitesProfil( $file_vis_profil );
    		}
    	}
    	 
    	if ($orig_visibilities_map) {
    		foreach ($orig_visibilities_map as $profil_id => $link) {
    			if (!isset($new_visibilities_map[$profil_id])) {
    				// remove link.
    				$this->removeVisibilitesProfil($link);
    			}
    		}
    	}
    }
    
    public function addVisibilityForCurrentUser() {
    	error_log('ADD VISI CURR USER 0');
    	
    	$secuMgr= ManagersManager::getManager()->getContainer()->get('security_manager');
		if (false) $secuMgr = new SecurityManager();
	
		$indiv = $secuMgr->getCurrentIndividu();
		
		error_log('ADD VISI CURR USER 1');
		
		if (!$indiv) return;
		
		error_log('ADD VISI CURR USER 2');
		
		if ($indiv instanceof Gestionnaire) {
			// NO NEED.
			return;
		}
		
		error_log('ADD VISI CURR USER 3');
		
		$profils = $indiv->getProfilsByProfilId();
		
		error_log('ADD VISI CURR USER 4');
		
		if (!$profils) return;
		
		error_log('ADD VISI CURR USER 5');
		
		foreach ($profils as $profil) {
			if (false) $profil = new ZombieProfil();
			error_log('ADD VISI CURR USER 6');
			if ($this->hasVisibilityLinkForAtLeastOneProfilId(array($profil->getId()))) continue;
			
			error_log('ADD VISI CURR USER 7');
			
			// Add visibility for the profil
			$file_vis_profil = new FileVisibiliteProfil();
			$file_vis_profil->setFile($this);
			$file_vis_profil->setProfil($profil);
			$file_vis_profil->setVisible(true);
			
			error_log('ADD VISI CURR USER 8');
			
			$this->addVisibilitesProfil($file_vis_profil);
			
			error_log('ADD VISI CURR USER 9');
		}
		
		error_log('ADD VISI CURR USER 10');
    }
    
    public function appliquerVisibiliteParDefaut() {
    	$objs = array();
    	
     	
    	$links = $this->getFileLinks();
    	
    	if ($links) {
    		foreach ($links as $file_link) {
    			if (false) $file_link = new FileLink();
    			$obj = $file_link->getObject();
    			if ($obj) $objs[] = $obj;
    		}
    	}
    	
    	if (!$objs) {
    		$this->setVisibilityProfils(array());
    		return;
    	}
    	
    	$profilsMgr = ManagersManager::getManager()->getContainer()->get('profils_comptes_manager');
    	if (false) $profilsMgr = new ProfilsComptesManager();
    	
    	foreach ($objs as $obj) {
    		$className = null;
    		$client = null;

    		
    		if (!$client) continue;
    		if (!$className) continue;
    		
    		$profils = $profilsMgr->getAllProfilsForClient($client->getStructure()->getId());
    		
    		$profilsShowResChoice = array();
    		$profilsToSet = array();
    		if ($profils) {
    			foreach ($profils as $profil) {
    				if (false) $profil = new ZombieProfil();    		
    		
    				$choice = $profil->getChoiceForCredential($className, 'view_docs');
    		
    				if ($choice) {
    					if (false) $choice = new CredentialMultiChoice();
    					if ($choice->getCode() == CredentialMultiChoice::CODE_ALWAYS || $choice->getCode() == CredentialMultiChoice::CODE_DEFAULT_YES) {
    						$profilsToSet[$profil->getId()] = $profil;
    					}
    				}
    			}
    		}
    	}
    	 
    	$this->setVisibilityProfils(array_values($profilsToSet));
    }
    
    public function getMimeIconPath() {
    	$mime = $this->getMime();
    	if ($mime) {
    		return '/bundles/zombie/images/charte/mimetypes/'.str_replace('/', '_', strtolower($mime).'.png');
    	}
    	
    	return '';
    	
    }
}
