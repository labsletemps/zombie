<?php

namespace Seriel\DandelionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;
/**
 * DandelionSubject
 *
 * @ORM\Entity
 * @ORM\Table(name="dandelion_subject",options={"engine"="MyISAM"})
 */
class DandelionSubject extends RootObject implements Listable
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="DandelionArticleSubject", mappedBy="subject" , cascade={"persist", "remove"})
     */
    private $articleSubjects;

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
     * Set name
     *
     * @param string $name
     *
     * @return DandelionSubject
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
     * Add articleSubject
     *
     * @param \Seriel\DandelionBundle\Entity\DandelionArticleSubject $articleSubject
     *
     * @return DandelionSubject
     */
    public function addArticleSubject(\Seriel\DandelionBundle\Entity\DandelionArticleSubject $articleSubject)
    {
        $this->articleSubjects[] = $articleSubject;

        return $this;
    }

    /**
     * Remove articleSubject
     *
     * @param \Seriel\DandelionBundle\Entity\DandelionArticleSubject $articleSubject
     */
    public function removeArticleSubject(\Seriel\DandelionBundle\Entity\DandelionArticleSubject $articleSubject)
    {
        $this->articleSubjects->removeElement($articleSubject);
    }

    /**
     * Get articleSubjects
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArticleSubjects()
    {
        return $this->articleSubjects;
    }

    /**
     * Get articleSubject
     *
     * @return \Seriel\DandelionBundle\Entity\DandelionArticleSubject
     */
    public function getArticleSubject($article)
    {
        foreach ($this->articleSubjects as $articleSubject) {
            if ($articleSubject->getArticle()->getId() == $article->getId()) {
                return $articleSubject;
            }
        }
        return null;
    }
}
