<?php

namespace Seriel\DandelionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;
use ZombieBundle\Utils\StopWordTokenizer;

/**
 * @ORM\Entity
 * @ORM\Table(name="dandelion_article_entity_link",options={"engine"="MyISAM"})
 */
class DandelionArticleEntityLink extends RootObject implements Listable
{

	const TEXT_TYPE_TITLE = 1;
	const TEXT_TYPE_CHAPEAU = 2;
	const TEXT_TYPE_CONTENT = 3;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\ManyToOne(targetEntity="DandelionArticleSemantics", inversedBy="entities")
	 * @ORM\JoinColumn(name="article_semantics_id", referencedColumnName="id")
	 **/
	protected $article_semantics;

	/**
	 * @ORM\ManyToOne(targetEntity="ZombieBundle\Entity\News\Article")
	 * @ORM\JoinColumn(name="article_id", referencedColumnName="id")
	 **/
	protected $article;

	/**
	 * @ORM\ManyToOne(targetEntity="DandelionEntity", inversedBy="articles")
	 * @ORM\JoinColumn(name="dandelion_entity_id", referencedColumnName="id")
	 **/
	protected $entity;

	/**
	 * @ORM\Column(type="integer", nullable=false)
	 */
	protected $type;

	/**
	 * @ORM\Column(type="decimal", precision=6, scale=4, nullable=false)
	 */
	protected $confidence;

	/**
	 * @ORM\Column(type="integer", nullable=false)
	 */
	protected $start;

	/**
	 * @ORM\Column(type="integer", nullable=false)
	 */
	protected $end;

	/**
	 * @ORM\Column(type="string", length=200, nullable=false)
	 */
	protected $spot;

	/**
	 * @ORM\Column(type="string", length=200, nullable=false)
	 */
	protected $spotRacin;


	public function __construct()
	{
		parent::__construct();
	}

	public function getId() {
		return $this->id;
	}

    public function getListUid() {
    	return $this->getId();
    }

    public function getTuilesParamsSupp() {
    	return array();
    }


    /**
     * Set type
     *
     * @param integer $type
     *
     * @return DandelionArticleEntityLink
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set confidence
     *
     * @param string $confidence
     *
     * @return DandelionArticleEntityLink
     */
    public function setConfidence($confidence)
    {
        $this->confidence = $confidence;

        return $this;
    }

    /**
     * Get confidence
     *
     * @return string
     */
    public function getConfidence()
    {
        return $this->confidence;
    }

    /**
     * Set start
     *
     * @param integer $start
     *
     * @return DandelionArticleEntityLink
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return integer
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param integer $end
     *
     * @return DandelionArticleEntityLink
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return integer
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set articleSemantics
     *
     * @param \Seriel\DandelionBundle\Entity\DandelionArticleSemantics $articleSemantics
     *
     * @return DandelionArticleEntityLink
     */
    public function setArticleSemantics(\Seriel\DandelionBundle\Entity\DandelionArticleSemantics $articleSemantics = null)
    {
        $this->article_semantics = $articleSemantics;

        $this->setArticle($articleSemantics->getArticle());

        return $this;
    }

    /**
     * Get articleSemantics
     *
     * @return \Seriel\DandelionBundle\Entity\DandelionArticleSemantics
     */
    public function getArticleSemantics()
    {
        return $this->article_semantics;
    }

    /**
     * Set article
     *
     * @param \ZombieBundle\Entity\News\Article $article
     *
     * @return DandelionArticleEntityLink
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

    /**
     * Set entity
     *
     * @param \Seriel\DandelionBundle\Entity\DandelionEntity $entity
     *
     * @return DandelionArticleEntityLink
     */
    public function setEntity(\Seriel\DandelionBundle\Entity\DandelionEntity $entity = null)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return \Seriel\DandelionBundle\Entity\DandelionEntity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set spot
     *
     * @param string $spot
     *
     * @return DandelionArticleEntityLink
     */
    public function setSpot($spot)
    {
    	
        $this->spot = $spot;
		$tokenizer = new StopWordTokenizer('fr',true);
		$words = explode(' ', strtolower($spot));
		$listword = $tokenizer->wordsToRacinisation($words);
		$this->spotRacin = implode(' ', $listword);
        return $this;
    }

    /**
     * Get spot
     *
     * @return string
     */
    public function getSpot()
    {
        return $this->spot;
    }

	/**
     * Set spotRacin
     *
     * @param string $spotRacin
     *
     * @return DandelionArticleEntityLink
     */
    public function setSpotRacin($spotRacin)
    {
        $this->spotRacin = $spotRacin;

        return $this;
    }

    /**
     * Get spotRacin
     *
     * @return string
     */
    public function getSpotRacin()
    {
        return $this->spotRacin;
    }
}
