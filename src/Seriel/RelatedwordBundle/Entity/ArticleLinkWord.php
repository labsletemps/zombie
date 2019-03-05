<?php

namespace Seriel\RelatedwordBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;

/**
 * ArticleLinkWord
 *
 * @ORM\Entity
 * @ORM\Table(name="rw_article_link_word",options={"engine"="MyISAM"})
 */
class ArticleLinkWord extends RootObject implements Listable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(name="weight", type="float")
     */
    private $weight;

    /**
     * @ORM\ManyToOne(targetEntity="Word", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $wordSource;

    /**
     * @ORM\ManyToOne(targetEntity="Word", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $wordTarget;

    /**
     * @ORM\ManyToOne(targetEntity="ZombieBundle\Entity\News\Article", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $article;

    public function __construct()
	{
		parent::__construct();
	}

    public function getListUid() {
    	return $this->getId();
    }

    public function getTuilesParamsSupp() {
    	return array();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }



    /**
     * Set weight
     *
     * @param float $weight
     *
     * @return ArticleLinkWord
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set wordSource
     *
     * @param \Seriel\RelatedwordBundle\Entity\Word $wordSource
     *
     * @return ArticleLinkWord
     */
    public function setWordSource(\Seriel\RelatedwordBundle\Entity\Word $wordSource)
    {
        $this->wordSource = $wordSource;

        return $this;
    }

    /**
     * Get wordSource
     *
     * @return \Seriel\RelatedwordBundle\Entity\Word
     */
    public function getWordSource()
    {
        return $this->wordSource;
    }

    /**
     * Set wordTarget
     *
     * @param \Seriel\RelatedwordBundle\Entity\Word $wordTarget
     *
     * @return ArticleLinkWord
     */
    public function setWordTarget(\Seriel\RelatedwordBundle\Entity\Word $wordTarget)
    {
        $this->wordTarget = $wordTarget;

        return $this;
    }

    /**
     * Get wordTarget
     *
     * @return \Seriel\RelatedwordBundle\Entity\Word
     */
    public function getWordTarget()
    {
        return $this->wordTarget;
    }

    /**
     * Set article
     *
     * @param \ZombieBundle\Entity\News\Article $article
     *
     * @return ArticleLinkWord
     */
    public function setArticle(\ZombieBundle\Entity\News\Article $article)
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
}
