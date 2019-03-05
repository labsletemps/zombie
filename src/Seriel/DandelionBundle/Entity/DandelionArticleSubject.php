<?php

namespace Seriel\DandelionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;

/**
 * DandelionArticleSubject
 *
 * @ORM\Entity
 * @ORM\Table(name="dandelion_article_subject",options={"engine"="MyISAM"})
 */
class DandelionArticleSubject extends RootObject implements Listable
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
     * @ORM\ManyToOne(targetEntity="ZombieBundle\Entity\News\Article")
     * @ORM\JoinColumn(nullable=false)
     */
    private $article;

    /**
     * @ORM\ManyToOne(targetEntity="DandelionSubject")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subject;

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
     * @return DandelionArticleSubject
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
     * Set article
     *
     * @param \ZombieBundle\Entity\News\Article $article
     *
     * @return DandelionArticleSubject
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

    /**
     * Set subject
     *
     * @param \Seriel\DandelionBundle\Entity\DandelionSubject $subject
     *
     * @return DandelionArticleSubject
     */
    public function setSubject(\Seriel\DandelionBundle\Entity\DandelionSubject $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return \Seriel\DandelionBundle\Entity\DandelionSubject
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
