<?php

namespace Seriel\RelatedwordBundle\Entity;

/**
 * LinkWordTemp
 *
 */
class LinkWordTemp
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $wordSource;

    /**
     * @var string
     */
    private $wordTarget;

    /**
     * @var string
     */
    private $weight;


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
     * Set wordSource
     *
     * @param string $wordSource
     *
     * @return LinkWordTemp
     */
    public function setWordSource($wordSource)
    {
        $this->wordSource = $wordSource;

        return $this;
    }

    /**
     * Get wordSource
     *
     * @return string
     */
    public function getWordSource()
    {
        return $this->wordSource;
    }

    /**
     * Set wordTarget
     *
     * @param string $wordTarget
     *
     * @return LinkWordTemp
     */
    public function setWordTarget($wordTarget)
    {
        $this->wordTarget = $wordTarget;

        return $this;
    }

    /**
     * Get wordTarget
     *
     * @return string
     */
    public function getWordTarget()
    {
        return $this->wordTarget;
    }

    /**
     * Set weight
     *
     * @param string $weight
     *
     * @return LinkWordTemp
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return string
     */
    public function getWeight()
    {
        return $this->weight;
    }

    public static function compare($a, $b)
    {
    	if ($a->getWeight() < $b->getWeight()) return 1;
    	else if($a->getWeight()== $b->getWeight()) return 0;
    	else return -1;
    }
    
    public function __toString()
    {
    	return $this->getWordSource().'=>'.$this->getWordTarget().'('.$this->getWeight().')';
    }
}

