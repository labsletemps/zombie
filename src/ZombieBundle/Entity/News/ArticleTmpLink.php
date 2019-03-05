<?php

namespace ZombieBundle\Entity\News;

use Doctrine\ORM\Mapping as ORM;
use ZombieBundle\Entity\RootObject;

/**
 * @ORM\Entity
 * @ORM\Table(name="article_tmp_link",options={"engine"="MyISAM"})
 */
class ArticleTmpLink extends RootObject
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="datetime", nullable=false)
	 */
	protected $date_creation;
	
	/**
	 * @ORM\Column(type="integer", unique=false, nullable=false)
	 */
	protected $duration_in_seconds;
	
	/**
	 * @ORM\Column(type="datetime", nullable=false)
	 */
	protected $date_deadline;

	/**
	 * @ORM\ManyToOne(targetEntity="Article")
	 * @ORM\JoinColumn(name="article_id", referencedColumnName="id")
	 */
	protected $article;
	
	/**
	 * @ORM\Column(type="string", length=300, nullable=false)
	 */
	protected $code;
	

	public function __construct()
	{
		parent::__construct();
	}

	public function getId() {
		return $this->id;
	}
	

    /**
     * Set durationInSeconds
     *
     * @param integer $durationInSeconds
     *
     * @return ArticleTmpLink
     */
    public function setDurationInSeconds($durationInSeconds)
    {
        $this->duration_in_seconds = $durationInSeconds;
        
        $this->checkDateDeadLine();

        return $this;
    }

    /**
     * Get durationInSeconds
     *
     * @return integer
     */
    public function getDurationInSeconds()
    {
        return $this->duration_in_seconds;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return ArticleTmpLink
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set article
     *
     * @param \ZombieBundle\Entity\News\Article $article
     *
     * @return ArticleTmpLink
     */
    public function setArticle(\ZombieBundle\Entity\News\Article $article = null)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return \ZombieBundle\Entity\News\Article
     */
    public function getArticle()
    {
        return $this->article;
    }
    
    public function checkDateDeadLine() {
    	if ($this->duration_in_seconds !== null && $this->date_creation) {
    		$deadline = new \DateTime();
    		$deadline->setTimestamp($this->date_creation->getTimestamp() + $this->duration_in_seconds);
    		
    		$this->date_deadline = $deadline;
    	}
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return ArticleTmpLink
     */
    public function setDateCreation($dateCreation)
    {
        $this->date_creation = $dateCreation;
        
        $this->checkDateDeadLine();

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->date_creation;
    }

    /**
     * Get dateDeadline
     *
     * @return \DateTime
     */
    public function getDateDeadline()
    {
        return $this->date_deadline;
    }
}
