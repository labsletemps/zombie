<?php

namespace Seriel\DandelionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;

/**
 * DandelionArticleEntityLinkEntity
 *
 * @ORM\Entity
 * @ORM\Table(name="dandelion_article_entity_link_entity",options={"engine"="MyISAM"})
 */
class DandelionArticleEntityLinkEntity  extends RootObject implements Listable
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
     * @var string
     *
     * @ORM\Column(name="spotSource", type="string", length=255)
     */
    private $spotSource;

    /**
     * @var string
     *
     * @ORM\Column(name="spotTarget", type="string", length=255)
     */
    private $spotTarget;

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
     * Set spotSource
     *
     * @param string $spotSource
     *
     * @return DandelionArticleEntityLinkEntity
     */
    public function setSpotSource($spotSource)
    {
        $this->spotSource = $spotSource;

        return $this;
    }

    /**
     * Get spotSource
     *
     * @return string
     */
    public function getSpotSource()
    {
        return $this->spotSource;
    }

    /**
     * Set spotTarget
     *
     * @param string $spotTarget
     *
     * @return DandelionArticleEntityLinkEntity
     */
    public function setSpotTarget($spotTarget)
    {
        $this->spotTarget = $spotTarget;

        return $this;
    }

    /**
     * Get sportTarget
     *
     * @return string
     */
    public function getSpotTarget()
    {
        return $this->spotTarget;
    }

    /**
     * Set weight
     *
     * @param float $weight
     *
     * @return DandelionArticleEntityLinkEntity
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
     * @return DandelionArticleEntityLinkEntity
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
    
}

