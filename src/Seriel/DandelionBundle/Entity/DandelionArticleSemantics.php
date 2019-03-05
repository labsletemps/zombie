<?php

namespace Seriel\DandelionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;
use Seriel\AppliToolboxBundle\Managers\ManagersManager;
use Seriel\DandelionBundle\Managers\DandelionEntitiesManager;

/**
 * @ORM\Entity
 * @ORM\Table(name="dandelion_article_semantics",options={"engine"="MyISAM"})
 */
class DandelionArticleSemantics extends RootObject implements Listable
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\OneToOne(targetEntity="ZombieBundle\Entity\News\Article")
	 * @ORM\JoinColumn(name="article_id", referencedColumnName="id")
	 **/
	protected $article;

	/**
	 * @ORM\Column(type="datetime", nullable=false)
	 */
	protected $date_parution;

	/**
	 * @ORM\Column(type="datetime", nullable=false)
	 */
	protected $date_calcul;

	/**
	 * @ORM\Column(type="string", length=200, nullable=false)
	 */
	protected $titre;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $titre_semantics;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $chapeau;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $chapeau_semantics;

	/**
	 * @ORM\Column(type="text", nullable=false)
	 */
	protected $content;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $content_semantics;

	/**
	 * @ORM\OneToMany(targetEntity="DandelionArticleEntityLink", mappedBy="article_semantics", orphanRemoval=true, cascade={"persist", "remove"})
	 */
	protected $entities;

	/**
	 * @ORM\Column(type="string", length=300, unique=false, nullable=true)
	 */
	protected $option_1;

	/**
	 * @ORM\Column(type="string", length=300, unique=false, nullable=true)
	 */
	protected $option_2;

	/**
	 * @ORM\Column(type="string", length=300, unique=false, nullable=true)
	 */
	protected $option_3;

	/**
	 * @ORM\Column(type="string", length=300, unique=false, nullable=true)
	 */
	protected $option_4;

	/**
	 * @ORM\Column(type="string", length=300, unique=false, nullable=true)
	 */
	protected $option_5;

	/**
	 * @ORM\Column(type="string", length=300, unique=false, nullable=true)
	 */
	protected $option_6;

	/**
	 * @ORM\Column(type="string", length=300, unique=false, nullable=true)
	 */
	protected $option_7;

	/**
	 * @ORM\Column(type="string", length=300, unique=false, nullable=true)
	 */
	protected $option_8;

	/**
	 * @ORM\Column(type="string", length=300, unique=false, nullable=true)
	 */
	protected $option_9;


	public function __construct(\ZombieBundle\Entity\News\Article $article)
	{
		parent::__construct();

		$this->article = $article;
		$this->date_parution = $article->getDateParution();
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
     * Set dateParution
     *
     * @param \DateTime $dateParution
     *
     * @return DandelionArticleSemantics
     */
    public function setDateParution($dateParution)
    {
        $this->date_parution = $dateParution;

        return $this;
    }

    /**
     * Get dateParution
     *
     * @return \DateTime
     */
    public function getDateParution()
    {
        return $this->date_parution;
    }

    /**
     * Set dateCalcul
     *
     * @param \DateTime $dateCalcul
     *
     * @return DandelionArticleSemantics
     */
    public function setDateCalcul($dateCalcul)
    {
        $this->date_calcul = $dateCalcul;

        return $this;
    }

    /**
     * Get dateCalcul
     *
     * @return \DateTime
     */
    public function getDateCalcul()
    {
        return $this->date_calcul;
    }

    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return DandelionArticleSemantics
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set titreSemantics
     *
     * @param string $titreSemantics
     *
     * @return DandelionArticleSemantics
     */
    public function setTitreSemantics($titreSemantics)
    {
        $this->titre_semantics = $titreSemantics;

        return $this;
    }

    /**
     * Get titreSemantics
     *
     * @return string
     */
    public function getTitreSemantics()
    {
        return $this->titre_semantics;
    }

    /**
     * Set chapeau
     *
     * @param string $chapeau
     *
     * @return DandelionArticleSemantics
     */
    public function setChapeau($chapeau)
    {
        $this->chapeau = $chapeau;

        return $this;
    }

    /**
     * Get chapeau
     *
     * @return string
     */
    public function getChapeau()
    {
        return $this->chapeau;
    }

    /**
     * Set chapeauSemantics
     *
     * @param string $chapeauSemantics
     *
     * @return DandelionArticleSemantics
     */
    public function setChapeauSemantics($chapeauSemantics)
    {
        $this->chapeau_semantics = $chapeauSemantics;

        return $this;
    }

    /**
     * Get chapeauSemantics
     *
     * @return string
     */
    public function getChapeauSemantics()
    {
        return $this->chapeau_semantics;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return DandelionArticleSemantics
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set contentSemantics
     *
     * @param string $contentSemantics
     *
     * @return DandelionArticleSemantics
     */
    public function setContentSemantics($contentSemantics)
    {
        $this->content_semantics = $contentSemantics;

        return $this;
    }

    /**
     * Get contentSemantics
     *
     * @return string
     */
    public function getContentSemantics()
    {
        return $this->content_semantics;
    }

    /**
     * Set option1
     *
     * @param string $option1
     *
     * @return DandelionArticleSemantics
     */
    public function setOption1($option1)
    {
        $this->option_1 = $option1;

        return $this;
    }

    /**
     * Get option1
     *
     * @return string
     */
    public function getOption1()
    {
        return $this->option_1;
    }

    /**
     * Set option2
     *
     * @param string $option2
     *
     * @return DandelionArticleSemantics
     */
    public function setOption2($option2)
    {
        $this->option_2 = $option2;

        return $this;
    }

    /**
     * Get option2
     *
     * @return string
     */
    public function getOption2()
    {
        return $this->option_2;
    }

    /**
     * Set option3
     *
     * @param string $option3
     *
     * @return DandelionArticleSemantics
     */
    public function setOption3($option3)
    {
        $this->option_3 = $option3;

        return $this;
    }

    /**
     * Get option3
     *
     * @return string
     */
    public function getOption3()
    {
        return $this->option_3;
    }

    /**
     * Set option4
     *
     * @param string $option4
     *
     * @return DandelionArticleSemantics
     */
    public function setOption4($option4)
    {
        $this->option_4 = $option4;

        return $this;
    }

    /**
     * Get option4
     *
     * @return string
     */
    public function getOption4()
    {
        return $this->option_4;
    }

    /**
     * Set option5
     *
     * @param string $option5
     *
     * @return DandelionArticleSemantics
     */
    public function setOption5($option5)
    {
        $this->option_5 = $option5;

        return $this;
    }

    /**
     * Get option5
     *
     * @return string
     */
    public function getOption5()
    {
        return $this->option_5;
    }

    /**
     * Set option6
     *
     * @param string $option6
     *
     * @return DandelionArticleSemantics
     */
    public function setOption6($option6)
    {
        $this->option_6 = $option6;

        return $this;
    }

    /**
     * Get option6
     *
     * @return string
     */
    public function getOption6()
    {
        return $this->option_6;
    }

    /**
     * Set option7
     *
     * @param string $option7
     *
     * @return DandelionArticleSemantics
     */
    public function setOption7($option7)
    {
        $this->option_7 = $option7;

        return $this;
    }

    /**
     * Get option7
     *
     * @return string
     */
    public function getOption7()
    {
        return $this->option_7;
    }

    /**
     * Set option8
     *
     * @param string $option8
     *
     * @return DandelionArticleSemantics
     */
    public function setOption8($option8)
    {
        $this->option_8 = $option8;

        return $this;
    }

    /**
     * Get option8
     *
     * @return string
     */
    public function getOption8()
    {
        return $this->option_8;
    }

    /**
     * Set option9
     *
     * @param string $option9
     *
     * @return DandelionArticleSemantics
     */
    public function setOption9($option9)
    {
        $this->option_9 = $option9;

        return $this;
    }

    /**
     * Get option9
     *
     * @return string
     */
    public function getOption9()
    {
        return $this->option_9;
    }

    /**
     * Set article
     *
     * @param \ZombieBundle\Entity\News\Article $article
     *
     * @return DandelionArticleSemantics
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

    public function dealWithEntitiesFromAnnotations($annotations) {
    	if (!$annotations) return;

    	$entitiesMgr = ManagersManager::getManager()->getContainer()->get('seriel_dandelion.dandelion_entities_manager');
    	if (false) $entitiesMgr = new DandelionEntitiesManager();

    	$entityTypeMap = array();

    	$entitesDone = array();
		$DandelionSubjectDone = array();
    	foreach ($annotations as $dand_entity) {
    		
    		if (! isset($dand_entity['id'])) continue;
    		
    		$id = intval(trim($dand_entity['id']));

    		if (isset($entitesDone[$id])) continue;

    		$title = trim($dand_entity['title']);
    		$uri = trim($dand_entity['uri']);
    		$label = trim($dand_entity['label']);

    		$entity = $entitiesMgr->getDandelionEntityForDandelionId($id);
    		if (!$entity) {
    			$entity = new DandelionEntity();
    			$entity->setDandelionId($id);
    		}

    		$entity->setTitle($title);
    		$entity->setUri($uri);
    		$entity->setLabel($label);

			// Add DandelionEntityType
			$arrayDbpediaId = isset($dand_entity['types']) ? $dand_entity['types'] : null;
			$entity->setTypes($arrayDbpediaId,$entityTypeMap);
			$types = $entity->getTypes()->toArray();
			if ($types) {
				foreach ($types as $type) {
					if (false) $type = new DandelionEntityType();
					$entityTypeMap[$type->getDbpediaId()] = $type;
				}
			}

			// Add DandelionSubject
			//$arrayCategorie = isset($dand_entity['categories']) ? $dand_entity['categories'] : array();
			//$weight = floatval(trim($dand_entity['confidence']));
			//$DandelionSubjectDone = $this->setNewSubject($arrayCategorie, $this->article ,$weight,$DandelionSubjectDone);
    		$entitiesMgr->save($entity);
    		$entitesDone[$id] = $entity;
    	}
		$entitiesMgr->flush();    
	return $entitesDone;
    }

    protected function setNewSubject($arraySubject, $article ,$weight, $DandelionSubjectDone) {
		$subjectMgr = ManagersManager::getManager()->getContainer()->get('seriel_dandelion.dandelion_subject_manager');
		foreach ($arraySubject as $name) {
			$name=trim($name);
			if ($name != null and $name != '') {
				// check if subject exist in database
				$subject = $subjectMgr->getDandelionSubjectByName($name);
				if ($subject == null) {
					//search if subject is in list pre-save
					$subject = isset($DandelionSubjectDone[$name]) ? $DandelionSubjectDone[$name] : null;
					// new subject
					if ($subject == null) {
						$subject = new \Seriel\DandelionBundle\Entity\DandelionSubject();
						$subject->setName($name);
						$articleSubject = new \Seriel\DandelionBundle\Entity\DandelionArticleSubject;
						$articleSubject->setArticle($article);
						$articleSubject->setWeight($weight);
						$articleSubject->setSubject($subject);
						$subject->addArticleSubject($articleSubject);
						$DandelionSubjectDone [$name] = $subject;
					}
					// subject pre-save
					else {
						$articleSubject = $subject->getArticleSubject($article);
						if ($articleSubject == null) {
							$articleSubject = new \Seriel\DandelionBundle\Entity\DandelionArticleSubject;
							$articleSubject->setArticle($article);
							$articleSubject->setWeight($weight);
							$articleSubject->setSubject($subject);
							$subject->addArticleSubject($articleSubject);
						}
						else {
							$articleSubject->setWeight($weight);
						}
					}
				}
				else {
					//subject in BD
					// TO-DO : remove all articleSubject of article in BD
					$articleSubject = $subject->getArticleSubject($article);
					if ($articleSubject == null) {
						$articleSubject = new \Seriel\DandelionBundle\Entity\DandelionArticleSubject;
						$articleSubject->setArticle($article);
						$articleSubject->setWeight($weight);
						$articleSubject->setSubject($subject);
						$subject->addArticleSubject($articleSubject);
					}
					else {
						$articleSubject->setWeight($weight);
					}
				}
				$subjectMgr->save($subject);
			}
		}
		return $DandelionSubjectDone;
	}

    public function calculate() {
    	$json_titre = $this->getTitreSemantics();
    	$titre_datas = json_decode($json_titre, true);

    	if (isset($titre_datas['annotations'])) {
    		$entitiesHash = $this->dealWithEntitiesFromAnnotations($titre_datas['annotations']);

    		// Ok, let's now link the datas
    		$this->linkEntities(DandelionArticleEntityLink::TEXT_TYPE_TITLE, $titre_datas['annotations'], $entitiesHash);
    	}

    	$json_chapeau = $this->getChapeauSemantics();
    	$chapeau_datas = json_decode($json_chapeau, true);

    	if (isset($chapeau_datas['annotations'])) {
    		$entitiesHash = $this->dealWithEntitiesFromAnnotations($chapeau_datas['annotations']);

    		// Ok, let's now link the datas
    		$this->linkEntities(DandelionArticleEntityLink::TEXT_TYPE_CHAPEAU, $chapeau_datas['annotations'], $entitiesHash);
    	}

    	$json_content = $this->getContentSemantics();
    	$content_datas = json_decode($json_content, true);

    	if (isset($content_datas['annotations'])) {
    		$entitiesHash = $this->dealWithEntitiesFromAnnotations($content_datas['annotations']);

    		// Ok, let's now link the datas
    		$this->linkEntities(DandelionArticleEntityLink::TEXT_TYPE_CONTENT, $content_datas['annotations'], $entitiesHash);
    	}

    }

    protected function getAllEntitiesForType($type) {
    	if ($type === null) return array();

    	$res = array();
    	if ($this->entities) {
    		foreach ($this->entities as $entityLink) {
    			if (false) $entityLink = new DandelionArticleEntityLink();
    			if ($entityLink->getType() == $type) $res[] = $entityLink;
    		}
    	}
    	return $res;
    }

    public function getEntitiesTitle() {
    	return $this->getAllEntitiesForType(DandelionArticleEntityLink::TEXT_TYPE_TITLE);
    }

    public function getEntitiesChapeau() {
    	return $this->getAllEntitiesForType(DandelionArticleEntityLink::TEXT_TYPE_CHAPEAU);
    }

    public function getEntitiesContent() {
    	return $this->getAllEntitiesForType(DandelionArticleEntityLink::TEXT_TYPE_CONTENT);
    }

    public function linkEntities($type, $datas, $entitiesHash = null) {
    	$origs = $this->getAllEntitiesForType($type);

    	$origsMap = array();
    	if ($origs) {
    		foreach ($origs as $orig) {
    			if (false) $orig = new DandelionArticleEntityLink();
    			$start = $orig->getStart();
    			$entity_id = $orig->getEntity()->getDandelionId();

    			$uid = $start.'_'.$entity_id;

    			$origsMap[$uid] = $orig;
    		}
    	}

    	$newsMap = array();
    	if ($datas) {
    		foreach ($datas as $data) {
    			if (! isset($data['id'])) continue;
    			$entity_id = intval(trim($data['id']));
    			$start = intval(trim($data['start']));

    			$uid = $start.'_'.$entity_id;

    			$newsMap[$uid] = $data;
    		}
    	}

    	if ($newsMap) {
    		$entitiesMgr = ManagersManager::getManager()->getContainer()->get('seriel_dandelion.dandelion_entities_manager');
    		if (false) $entitiesMgr = new DandelionEntitiesManager();
    		foreach ($newsMap as $uid => $data) {
    			if (! isset($data['id'])) continue;
    			$entity_id = intval(trim($data['id']));
    			$start = intval(trim($data['start']));
    			$end = intval(trim($data['end']));
    			$spot = $data['spot'];
    			$confidence = floatval(trim($data['confidence']));

    			$entityLink = null;
    			if (isset($origsMap[$uid])) {
    				$entityLink = $origsMap[$uid];
    			} else {
    				$entity = ($entitiesHash && isset($entitiesHash[$entity_id])) ? $entitiesHash[$entity_id] : $entitiesMgr->getDandelionEntityForDandelionId($entity_id);

    				if (!$entity) {
    					// This souldn't happen :/
    					error_log('DandelinArticleSemantics : entity not found '.$entity_id);
    					continue;
    				}

    				$entityLink = new DandelionArticleEntityLink();
    				$entityLink->setArticleSemantics($this);
    				$entityLink->setStart($start);
    				$entityLink->setEntity($entity);
    				$entityLink->setType($type);

    				$this->addEntity($entityLink);
    			}
    			$entityLink->setEnd($end);
    			$entityLink->setConfidence($confidence);
    			$entityLink->setSpot($spot);
    		}

    		// Delete possible disappearances.
    		foreach ($origsMap as $uid => $link) {
    			if (!isset($newsMap[$uid])) {
    				$this->removeEntity($link);
    			}
    		}
    	}
    }

    /**
     * Add entity
     *
     * @param \Seriel\DandelionBundle\Entity\DandelionArticleEntityLink $entity
     *
     * @return DandelionArticleSemantics
     */
    protected function addEntity(\Seriel\DandelionBundle\Entity\DandelionArticleEntityLink $entity)
    {
        $this->entities[] = $entity;

        return $this;
    }

    /**
     * Remove entity
     *
     * @param \Seriel\DandelionBundle\Entity\DandelionArticleEntityLink $entity
     */
    protected function removeEntity(\Seriel\DandelionBundle\Entity\DandelionArticleEntityLink $entity)
    {
        $this->entities->removeElement($entity);
    }

    /**
     * Get entities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEntities()
    {
        return $this->entities;
    }
}
