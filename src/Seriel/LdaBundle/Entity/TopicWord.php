<?php

namespace Seriel\LdaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;

/**
 * TopicWord
 *
 * @ORM\Entity
 * @ORM\Table(name="lda_topic_word",options={"engine"="MyISAM"})
 */
class TopicWord extends RootObject implements Listable
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
     * @ORM\ManyToOne(targetEntity="Topic")
     * @ORM\JoinColumn(nullable=false)
     */
    private $topic;

    /**
     * @ORM\ManyToOne(targetEntity="Word", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $word;

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
     * @return TopicWord
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
     * Set topic
     *
     * @param \Seriel\LdaBundle\Entity\Topic $topic
     *
     * @return TopicWord
     */
    public function setTopic(\Seriel\LdaBundle\Entity\Topic $topic)
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * Get topic
     *
     * @return \Seriel\LdaBundle\Entity\Topic
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * Set word
     *
     * @param \Seriel\LdaBundle\Entity\Word $word
     *
     * @return TopicWord
     */
    public function setWord(\Seriel\LdaBundle\Entity\Word $word)
    {
        $this->word = $word;

        return $this;
    }

    /**
     * Get word
     *
     * @return \Seriel\LdaBundle\Entity\Word
     */
    public function getWord()
    {
        return $this->word;
    }
}
