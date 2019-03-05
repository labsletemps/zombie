<?php

namespace Seriel\DandelionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;
use Seriel\AppliToolboxBundle\Managers\ManagersManager;

/**
 * @ORM\Entity
 * @ORM\Table(name="dandelion_entity",options={"engine"="MyISAM"})
 */
class DandelionEntity extends RootObject implements Listable
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="integer", unique=true, nullable=false)
	 */
	protected $dandelion_id;

	/**
	 * @ORM\Column(type="string", length=200, nullable=false)
	 */
	protected $title;

	/**
	 * @ORM\Column(type="string", length=600, nullable=false)
	 */
	protected $uri;

	/**
	 * @ORM\Column(type="string", length=200, nullable=false)
	 */
	protected $label;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="quantity", type="integer")
	 */
	protected $quantity;
	
	/**
	 * @ORM\OneToMany(targetEntity="DandelionArticleEntityLink", mappedBy="entity")
	 */
	protected $articles;

	/**
	*
	* @ORM\ManyToMany(targetEntity="DandelionEntityType", cascade={"persist", "remove"})
	* @ORM\JoinTable(name="dandelion_entity_dandelion_entity_type",
	*      joinColumns={@ORM\JoinColumn(name="dandelion_entity_id", referencedColumnName="id")},
	*      inverseJoinColumns={@ORM\JoinColumn(name="dandelion_entity_type_id", referencedColumnName="id")}
	*      )
	*/
	protected $types;


	
	public function __construct()
	{
		parent::__construct();
		$this->types = new \Doctrine\Common\Collections\ArrayCollection();
		$this->quantity = 0;
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
     * Set dandelionId
     *
     * @param integer $dandelionId
     *
     * @return DandelionEntity
     */
    public function setDandelionId($dandelionId)
    {
        $this->dandelion_id = $dandelionId;

        return $this;
    }

    /**
     * Get dandelionId
     *
     * @return integer
     */
    public function getDandelionId()
    {
        return $this->dandelion_id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return DandelionEntity
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set uri
     *
     * @param string $uri
     *
     * @return DandelionEntity
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Get uri
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return DandelionEntity
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return DandelionEntity
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
     * Add article
     *
     * @param \Seriel\DandelionBundle\Entity\DandelionArticleEntityLink $article
     *
     * @return DandelionEntity
     */
    public function addArticle(\Seriel\DandelionBundle\Entity\DandelionArticleEntityLink $article)
    {
        $this->articles[] = $article;

        return $this;
    }

    /**
     * Remove article
     *
     * @param \Seriel\DandelionBundle\Entity\DandelionArticleEntityLink $article
     */
    public function removeArticle(\Seriel\DandelionBundle\Entity\DandelionArticleEntityLink $article)
    {
        $this->articles->removeElement($article);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Add type
     *
     * @param \Seriel\DandelionBundle\Entity\DandelionEntityType $type
     *
     * @return DandelionEntity
     */
    public function addType(\Seriel\DandelionBundle\Entity\DandelionEntityType $type)
    {
        $this->types[] = $type;

        return $this;
    }

    /**
     * Remove type
     *
     * @param \Seriel\DandelionBundle\Entity\DandelionEntityType $type
     */
    public function removeType(\Seriel\DandelionBundle\Entity\DandelionEntityType $type)
    {
        $this->types->removeElement($type);
    }

	/**
     * set types
     *
     * @return DandelionEntity
     */
    public function setTypes($arrayDbpediaId, $DandelionEntityTypeDone)
    {
		$entitiesTypeMgr = ManagersManager::getManager()->getContainer()->get('seriel_dandelion.dandelion_entities_type_manager');
		// remove if row in database dandelion_entity_dandelion_entity_type
		$this->types = new \Doctrine\Common\Collections\ArrayCollection();

		foreach ($arrayDbpediaId as $dbpediaId) {
			$dbpediaId=trim($dbpediaId);
			if ($dbpediaId != null and $dbpediaId != '') {
				// check if Type exist in database
				$typeDB = $entitiesTypeMgr->getDandelionEntityTypeForDbpediaId($dbpediaId);

				if ($typeDB == null) {
					//search if type is in list pre-save
					$type = isset($DandelionEntityTypeDone[$dbpediaId]) ? $DandelionEntityTypeDone[$dbpediaId] : null;
					if ($type == null) {
						$type = new DandelionEntityType();
						$type->setDbpediaId($dbpediaId);
						$name = substr($dbpediaId, strrpos($dbpediaId, "/")+1);
						$type->setName($name);
					}
					$this->addType($type);
				}
				else {
					$this->addType($typeDB);
				}
			}
		}
    }

    /**
     * Get types
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTypes()
    {
        return $this->types;
    }

	/**
     * Get isType
     *
     * @return boolean
     */
    public function isType($dbpediaId)
    {
		foreach ($this->types as $type) {
			if ($type->getDbpediaId() == $dbpediaId) {
				return true;
			}
		}
        return false;
    }

}
