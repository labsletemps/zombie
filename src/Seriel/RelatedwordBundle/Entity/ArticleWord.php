<?php

namespace Seriel\RelatedwordBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * ArticleWord
 *
 * @ORM\Entity
 * @ORM\Table(name="rw_article_word",options={"engine"="MyISAM"},uniqueConstraints={@UniqueConstraint(name="unique_articleword_idx", columns={"word_id", "article_id"})})
 * @UniqueEntity(fields={"word", "article"})
 */
class ArticleWord extends RootObject implements Listable
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
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var boolean
     *
     * @ORM\Column(name="intitle", type="boolean", nullable=false)
     */
    private $intitle;

    /**
     * @var boolean
     *
     * @ORM\Column(name="inchapeau", type="boolean", nullable=false)
     */
    private $inchapeau;
    
   
    /**
     * @ORM\ManyToOne(targetEntity="Word", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $word;

    /**
     * @ORM\ManyToOne(targetEntity="ZombieBundle\Entity\News\Article", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $article;

    public function __construct()
	{
		parent::__construct();
		$this->intitle = false;
		$this->inchapeau = false;
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return ArticleWord
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set intitle
     *
     * @param boolean $intitle
     *
     * @return ArticleWord
     */
    public function setIntitle($intitle)
    {
    	$this->intitle= $intitle;
    	
    	return $this;
    }
    
    /**
     * Get intitle
     *
     * @return boolean
     */
    public function getIntitle()
    {
    	return $this->intitle;
    }
    
    /**
     * Set inchapeau
     *
     * @param boolean $inchapeau
     *
     * @return ArticleWord
     */
    public function setInchapeau($inchapeau)
    {
    	$this->inchapeau= $inchapeau;
    	
    	return $this;
    }
    
    /**
     * Get inchapeau
     *
     * @return boolean
     */
    public function getInchapeau()
    {
    	return $this->inchapeau;
    }
    
    /**
     * Get word
     *
     * @return \Seriel\RelatedwordBundle\Entity\Word
     */
    public function getWord()
    {
        return $this->word;
    }

    /**
     * Set word
     *
     * @param \Seriel\RelatedwordBundle\Entity\Word $word
     *
     * @return Word
     */
    public function setWord(\Seriel\RelatedwordBundle\Entity\Word $word)
    {
        $this->word = $word;
        return $this;
    }

    /**
     * Set article
     *
     * @param \ZombieBundle\Entity\News\Article $article
     *
     * @return ArticleWord
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
