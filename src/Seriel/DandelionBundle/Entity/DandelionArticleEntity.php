<?php

namespace Seriel\DandelionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * DandelionArticleEntity
 *
 * @ORM\Entity
 * @ORM\Table(name="dandelion_article_entity",options={"engine"="MyISAM"},uniqueConstraints={@UniqueConstraint(name="unique_dandelionarticleentity_idx", columns={"spot_racin", "article_id"})})
 * @UniqueEntity(fields={"spotRacin", "article"})
 */
class DandelionArticleEntity  extends RootObject implements Listable
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
     * @var float
     *
     * @ORM\Column(name="weight", type="float")
     */
    private $weight;
    
    /**
     * @ORM\ManyToOne(targetEntity="ZombieBundle\Entity\News\Article")
     * @ORM\JoinColumn(name="article_id", referencedColumnName="id")
     **/
    private $article;

    /**
     * @ORM\Column(name="spot_racin", type="string", length=200, nullable=false)
     */
    private $spotRacin;
    
 
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return DandelionArticleEntity
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
     * Set article
     *
     * @param \ZombieBundle\Entity\News\Article $article
     *
     * @return DandelionArticleEntity
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

    /**
     * Set weight
     *
     * @param float $weight
     *
     * @return DandelionArticleEntity
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
}
