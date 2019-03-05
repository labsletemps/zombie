<?php

namespace Seriel\DandelionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;

/**
 * @ORM\Entity
 * @ORM\Table(name="dandelion_entity_type",options={"engine"="MyISAM"})
 */
class DandelionEntityType  extends RootObject implements Listable
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
     * @ORM\Column(name="dbpediaId", type="string", length=300, unique=true)
     */
    private $dbpediaId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


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
     * Set dbpediaId
     *
     * @param string $dbpediaId
     *
     * @return DandelionEntityType
     */
    public function setDbpediaId($dbpediaId)
    {
        $this->dbpediaId = $dbpediaId;

        return $this;
    }

    /**
     * Get dbpediaId
     *
     * @return string
     */
    public function getDbpediaId()
    {
        return $this->dbpediaId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return DandelionEntityType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


}
