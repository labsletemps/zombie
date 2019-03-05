<?php

namespace ZombieBundle\Entity\Fichier;

use Doctrine\ORM\Mapping as ORM;
use ZombieBundle\Entity\RootObject;
use ZombieBundle\Entity\Entite\ZombieEntite;
use ZombieBundle\Entity\News\Article;
use ZombieBundle\Entity\Individu\Individu;

/**
 * @ORM\Entity
 * @ORM\Table(name="fichier_lien",options={"engine"="MyISAM"})
 */
class FileLink extends RootObject
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="File",inversedBy="file_links", cascade={"persist"})
	 * @ORM\JoinColumn(name="file_id", referencedColumnName="id")
	 **/
	protected $file;
	
	/**
	 * @ORM\Column(name="file_id", type="integer", nullable=true)
	 */
	protected $fileId;
	
	/**
	 * @ORM\Column(type="string", length=255, unique=false, nullable=true)
	 */
	protected $tmp_uid;

	/**
	 * @ORM\ManyToOne(targetEntity="ZombieBundle\Entity\News\Article")
	 * @ORM\JoinColumn(name="article_id", referencedColumnName="id")
	 **/
	protected $article;
	
	/**
	 * @ORM\ManyToOne(targetEntity="ZombieBundle\Entity\Entite\ZombieEntite",inversedBy="files")
	 * @ORM\JoinColumn(name="entite_id", referencedColumnName="id")
	 **/
	protected $entite;
	
	/**
	 * @ORM\ManyToOne(targetEntity="ZombieBundle\Entity\Individu\Individu",inversedBy="files")
	 * @ORM\JoinColumn(name="individu_id", referencedColumnName="id")
	 **/
	protected $individu;
	
	public function __construct()
	{
		parent::__construct();
	}

	public function getId() {
		return $this->id;
	}
	
	public function getFileId() {
		return $this->fileId;
	}

    /**
     * Set file
     *
     * @param \ZombieBundle\Entity\Fichier\File $file
     * @return FileLink
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
     * Set fileId
     *
     * @param integer $fileId
     *
     * @return FileLink
     */
    public function setFileId($fileId)
    {
        $this->fileId = $fileId;

        return $this;
    }
    
    public function setObject($obj) {
    	if (!$obj) return;
    	
    	if ($obj instanceof Article) {
    		$this->setArticle($obj);
    	} else if ($obj instanceof ZombieEntite) {
    		$this->setEntite($obj);
    	} else if ($obj instanceof Individu) {
    		$this->setIndividu($obj);
    	}
    }

    /**
     * Set article
     *
     * @param Article $article
     *
     * @return FileLink
     */
    public function setArticle(Article $article = null)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return \ZombieBundle\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }
    
    /**
     * Set individu
     *
     * @param Individu $individu
     *
     * @return FileLink
     */
    public function setIndividu(Individu $individu = null)
    {
    	$this->individu = $individu;
    	
    	return $this;
    }
    
    /**
     * Get article
     *
     * @return \ZombieBundle\Entity\Individu\Individu
     */
    public function getIndividu()
    {
    	return $this->individu;
    }
    
    public function setEntite(ZombieEntite $entite = null)
    {
        $this->entite = $entite;

        return $this;
    }

    /**
     * Get entite
     *
     * @return ZombieEntite
     */
    public function getEntite()
    {
        return $this->entite;
    }

    /**
     * Set tmpUid
     *
     * @param string $tmpUid
     *
     * @return FileLink
     */
    public function setTmpUid($tmpUid)
    {
        $this->tmp_uid = $tmpUid;

        return $this;
    }

    /**
     * Get tmpUid
     *
     * @return string
     */
    public function getTmpUid()
    {
        return $this->tmp_uid;
    }
    
    public function getObject() {

    	

    	$entite = $this->getEntite();
    	if ($entite) return $entite;
    	$article = $this->getArticle();
    	if ($article) return $article;
    	$individu = $this->getIndividu();
    	if ($individu) return $individu;
    
    	return null;
    }
}

	
