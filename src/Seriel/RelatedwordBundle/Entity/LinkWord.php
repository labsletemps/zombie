<?php

namespace Seriel\RelatedwordBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\UniqueConstraint;


/**
 * LinkWord
 *
 * @ORM\Entity
 * @ORM\Table(name="rw_link_word",options={"engine"="MyISAM"},uniqueConstraints={@UniqueConstraint(name="unique_linkword_idx", columns={"word_source_id", "word_target_id"})})
 * @UniqueEntity(fields={"wordSource", "wordTarget"})
 */
class LinkWord extends RootObject implements Listable
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
     * @return LinkWord
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
     * @param \Seriel\RelatedwordBundle\Entity\Word $word
     *
     * @return Word
     */
    public function setWordSource(\Seriel\RelatedwordBundle\Entity\Word $word)
    {
        $this->wordSource = $word;

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
     * @param \Seriel\RelatedwordBundle\Entity\Word $word
     *
     * @return Word
     */
    public function setWordTarget(\Seriel\RelatedwordBundle\Entity\Word $word)
    {
        $this->wordTarget = $word;

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

}
