<?php

namespace Seriel\LdaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;

/**
 * Topic
 *
 * @ORM\Entity
 * @ORM\Table(name="lda_topic",options={"engine"="MyISAM"})
 */
class Topic extends RootObject implements Listable
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="calculate_at", type="datetime")
     */
    private $calculateAt;

    /**
     * @ORM\OneToMany(targetEntity="TopicWord", mappedBy="topic" , cascade={"persist", "remove"})
     */
    private $topicWords;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->topicWords = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Topic
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

    /**
     * Set calculateAt
     *
     * @param \DateTime $calculateAt
     *
     * @return Topic
     */
    public function setCalculateAt($calculateAt)
    {
        $this->calculateAt = $calculateAt;

        return $this;
    }

    /**
     * Get calculateAt
     *
     * @return \DateTime
     */
    public function getCalculateAt()
    {
        return $this->calculateAt;
    }


    /**
     * Add topicWord
     *
     * @param \Seriel\LdaBundle\Entity\TopicWord $topicWord
     *
     * @return Topic
     */
    public function addTopicWord(\Seriel\LdaBundle\Entity\TopicWord $topicWord)
    {
        $this->topicWords[] = $topicWord;

        return $this;
    }

    /**
     * Remove topicWord
     *
     * @param \Seriel\LdaBundle\Entity\TopicWord $topicWord
     */
    public function removeTopicWord(\Seriel\LdaBundle\Entity\TopicWord $topicWord)
    {
        $this->topicWords->removeElement($topicWord);
    }

    /**
     * Get topicWords
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTopicWords()
    {
        return $this->topicWords;
    }
}
