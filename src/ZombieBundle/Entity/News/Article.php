<?php

namespace ZombieBundle\Entity\News;

use Doctrine\ORM\Mapping as ORM;
use ZombieBundle\Entity\RootObject;
use Seriel\AppliToolboxBundle\Annotation as SER;
use ZombieBundle\Utils\ZombieUtils;
use Seriel\AppliToolboxBundle\Managers\ManagersManager;


/**
 * @ORM\Entity
 * @ORM\Table(name="article",options={"engine"="MyISAM"},indexes={@ORM\Index(name="date_parution_article_idx", columns={"date_parution"})})
 */
class Article extends RootObject
{
	const SOURCE_LE_TEMPS = '01';
	const SOURCE_UNKNOWN = '99';

	protected $_cache_metrics_objects;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", length=300, nullable=false)
	 */
	protected $guid;

	/**
	 * @ORM\Column(type="string", length=30, nullable=false)
	 */
	protected $short_guid;

	/**
	 * @ORM\Column(type="string", length=2, nullable=false)
	 */
	protected $source;

	/**
	 * @ORM\Column(type="string", length=30, nullable=false)
	 */
	protected $uid;

	/**
	 * @ORM\Column(type="datetime", nullable=false)
	 */
	protected $date_parution;

	/**
	 * @ORM\Column(type="string", length=200, nullable=false)
	 */
	protected $titre;

	/**
	 * @ORM\Column(type="string", length=200, nullable=true)
	 */
	protected $url_title;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $chapeau;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $chapeau_striped;

	/**
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	protected $section;

	/**
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	protected $url_section;

	/**
	 * @ORM\Column(type="text", nullable=false)
	 */
	protected $content;

	/**
	 * @ORM\Column(type="text", nullable=false)
	 */
	protected $content_striped;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $nb_words;

	/**
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	protected $mot_cle;

	/**
	 * @ORM\Column(type="string", length=300, nullable=true)
	 */
	protected $tags;

	/**
	 * @ORM\Column(type="string", length=200, nullable=true)
	 */
	protected $auteur;

	/**
	 * @ORM\Column(type="string", length=200, nullable=true)
	 */
	protected $auteur_externe;

	/**
	 * @ORM\Column(type="string", length=500, nullable=true)
	 */
	protected $image_url;

	/**
	 * @ORM\Column(type="boolean", unique=false, nullable=true, options={"default":false})
	 */
	protected $deleted;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	protected $dateDelete;

	/**
	 * @ORM\ManyToOne(targetEntity="ZombieBundle\Entity\Individu\Individu")
	 * @ORM\JoinColumn(name="individu_delete_id", referencedColumnName="id")
	 */
	protected $individuDelete;

	/**
	 * @ORM\Column(type="string", length=500, nullable=true)
	 */
	protected $article_uri_1;

	/**
	 * @ORM\Column(type="string", length=500, nullable=true)
	 */
	protected $article_uri_2;

	/**
	 * @ORM\Column(type="string", length=500, nullable=true)
	 */
	protected $article_uri_3;

	/**
	 * @ORM\Column(type="string", length=500, nullable=true)
	 */
	protected $article_uri_4;

	/**
	 * @ORM\Column(type="string", length=500, nullable=true)
	 */
	protected $article_uri_5;

	/**
	 * @ORM\Column(type="string", length=500, nullable=true)
	 */
	protected $article_uri_6;

	/**
	 * @ORM\Column(type="string", length=500, nullable=true)
	 */
	protected $article_uri_7;

	/**
	 * @ORM\Column(type="string", length=500, nullable=true)
	 */
	protected $article_uri_8;

	/**
	 * @ORM\Column(type="string", length=500, nullable=true)
	 */
	protected $article_uri_9;

	/**
	 * @ORM\Column(type="string", length=500, nullable=true)
	 */
	protected $article_uri_10;
	
	protected $note_semantique;


	public function __construct()
	{
		parent::__construct();
		$this->_cache_metrics_objects = array();
	}

	public function getId() {
		return $this->id;
	}


    /**
     * Set guid
     *
     * @param string $guid
     *
     * @return Article
     */
    public function setGuid($guid)
    {
        $this->guid = $guid;

        if ($guid) {
        	$shortened = self::shortenGuid($guid);
        	$source = '00';
        	$uid = $guid;

        	if (is_array($shortened)) {
        		$source = $shortened['source'];
        		$uid = $shortened['uid'];
        	} else {
        		$uid = $shortened;
        	}

        	$this->setShortGuid($source.'_'.$uid);
        	$this->setSource($source);
        	$this->setUid($uid);
        } else {
        	$this->setSource('00');
        	$this->setShortGuid('');
        	$this->setUid('');
        }

        return $this;
    }

    /**
     * Get guid
     *
     * @return string
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * Set shortGuid
     *
     * @param string $shortGuid
     *
     * @return Article
     */
    public function setShortGuid($shortGuid)
    {
        $this->short_guid = $shortGuid;

        return $this;
    }

    /**
     * Get shortGuid
     *
     * @return string
     */
    public function getShortGuid()
    {
        return $this->short_guid;
    }

    /**
     * Set source
     *
     * @param string $source
     *
     * @return Article
     */
    public function setSource($source)
    {
    	$this->source = $source;

    	return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
    	return $this->source;
    }

    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return Article
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        if ($titre) {
        	$this->setUrlTitle($this->transformTitleToUrl($titre));
        } else {
        	$this->setUrlTitle("");
        }

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     *
     * @SER\ListeProperty("titre", label="Titre", sort="string", format="none", dbfield="titre")
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set chapeau
     *
     * @param string $chapeau
     *
     * @return Article
     */
    public function setChapeau($chapeau)
    {
        $this->chapeau = $chapeau;

        if ($chapeau) {
        	$this->setChapeauStriped(self::stripContent($chapeau));
        } else {
        	$this->setChapeauStriped('');
        }

        return $this;
    }

    /**
     * Get chapeau
     *
     * @return string
     *
     * @SER\ListeProperty("chapeau", label="Chapeau", sort="string", format="none", dbfield="chapeau")
     */
    public function getChapeau()
    {
        return $this->chapeau;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Article
     */
    public function setContent($content)
    {
        $this->content = $content;

        if ($content) {
        	$this->setContentStriped(self::stripContent($content));
        } else {
        	$this->setContentStriped('');
        }

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
     * Set contentStriped
     *
     * @param string $contentStriped
     *
     * @return Article
     */
    public function setContentStriped($contentStriped)
    {
    	$this->content_striped = $contentStriped;
    	$this->setNbWords(str_word_count($contentStriped));

    	return $this;
    }

    /**
     * Get contentStriped
     *
     * @return string
     *
     * @SER\ListeProperty("content_striped", label="Contenu", sort="string", format="none" , dbfield="content_striped")
     */
    public function getContentStriped()
    {
    	return $this->content_striped;
    }

    /**
     * Set motCle
     *
     * @param string $motCle
     *
     * @return Article
     */
    public function setMotCle($motCle)
    {
        $this->mot_cle = $motCle;

        return $this;
    }

    /**
     * Get motCle
     *
     * @return string
     *
     * @SER\ListeProperty("mot_cle", label="Mot clé", sort="string", format="none", dbfield="mot_cle")
     * @SER\ReportingColRowProperty("mot_cle", label="Mot clé")
     */
    public function getMotCle()
    {
        return $this->mot_cle;
    }

    /**
     * Set tags
     *
     * @param string $tags
     *
     * @return Article
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set date_parution
     *
     * @param \DateTime $date_parution
     *
     * @return Article
     */
    public function setDateParution($date_parution)
    {
        $this->date_parution = $date_parution;

        return $this;
    }

    /**
     * Get date_parution
     *
     * @return \DateTime
     *
     * @SER\ListeProperty("date_parution", label="Date parution", sort="date_heure", format="date_heure", dbfield="date_parution")
     * @SER\ReportingColRowProperty("date_parution", label="Date parution", sort="date", format="date_heure", option="date_heure")
     */
    public function getDateParution()
    {
        return $this->date_parution;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return Article
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set dateDelete
     *
     * @param \DateTime $dateDelete
     *
     * @return Article
     */
    public function setDateDelete($dateDelete)
    {
        $this->dateDelete = $dateDelete;

        return $this;
    }

    /**
     * Get dateDelete
     *
     * @return \DateTime
     */
    public function getDateDelete()
    {
        return $this->dateDelete;
    }

    /**
     * Set individuDelete
     *
     * @param \ZombieBundle\Entity\Individu\Individu $individuDelete
     *
     * @return Article
     */
    public function setIndividuDelete(\ZombieBundle\Entity\Individu\Individu $individuDelete = null)
    {
        $this->individuDelete = $individuDelete;

        return $this;
    }

    /**
     * Get individuDelete
     *
     * @return \ZombieBundle\Entity\Individu
     */
    public function getIndividuDelete()
    {
        return $this->individuDelete;
    }

    public static function shortenGuid($guid) {
    	if (!$guid) return $guid;

    	$lastSlashPos = strrpos($guid, "/");

    	if ($lastSlashPos === false) return $guid;

    	$prefix = substr($guid, 0, $lastSlashPos+1);
    	$realGuid = substr($guid, $lastSlashPos+1);

    	// TODO : tester le prefix.
    	if ($prefix == "https://www.example.com/node/") {
    		return array('source' => self::SOURCE_LE_TEMPS, 'uid' => $realGuid);
    	} else {
    		return array('source' => self::SOURCE_UNKNOWN, 'uid' => $realGuid);
    	}
    }

    public static function addSpaceBeforeTag($content, $tag, $ln = false) {
    	if (!$tag) return $content;

    	$lower_tag = strtolower($tag);
    	$upper_tag = strtoupper($tag);

    	$added = $ln ? "\r\n" : " ";

    	$content = str_replace("<$lower_tag ", "$added<$lower_tag ", $content);
    	$content = str_replace("<$lower_tag>", "$added<$lower_tag>", $content);
    	$content = str_replace("<$lower_tag/>", "$added<$lower_tag/>", $content);

    	$content = str_replace("<$upper_tag ", "$added<$upper_tag ", $content);
    	$content = str_replace("<$upper_tag>", "$added<$upper_tag>", $content);
    	$content = str_replace("<$upper_tag/>", "$added<$upper_tag/>", $content);

    	return $content;
    }
    public static function addSpaceBetweenTags($content, $tag1, $tag2 = null, $ln = false) {
    	if (!$tag1) return $content;

    	if (!$tag2) $tag2 = $tag1;

    	$lower_tag1 = strtolower($tag1);
    	$lower_tag2 = strtolower($tag2);

    	$upper_tag1 = strtoupper($tag1);
    	$upper_tag2 = strtoupper($tag2);

    	$added = $ln ? "\r\n" : " ";

    	$content = str_replace("</$lower_tag1><$lower_tag2>", "</$lower_tag1>$added<$lower_tag2>", $content);
    	$content = str_replace("</$lower_tag1><$lower_tag2 ", "</$lower_tag1>$added<$lower_tag2 ", $content);
    	$content = str_replace("</$lower_tag1><$lower_tag2/>", "</$lower_tag1>$added<$lower_tag2/>", $content);
    	$content = str_replace("<$lower_tag1/><$lower_tag2>", "<$lower_tag1/>$added<$lower_tag2>", $content);
    	$content = str_replace("<$lower_tag1/><$lower_tag2 ", "<$lower_tag1/>$added<$lower_tag2 ", $content);
    	$content = str_replace("<$lower_tag1/><$lower_tag2/>", "<$lower_tag1/>$added<$lower_tag2/>", $content);

    	$content = str_replace("</$lower_tag1><$upper_tag2>", "</$lower_tag1>$added<$upper_tag2>", $content);
    	$content = str_replace("</$lower_tag1><$upper_tag2 ", "</$lower_tag1>$added<$upper_tag2 ", $content);
    	$content = str_replace("</$lower_tag1><$upper_tag2/>", "</$lower_tag1>$added<$upper_tag2/>", $content);
    	$content = str_replace("<$lower_tag1/><$upper_tag2>", "<$lower_tag1/>$added<$upper_tag2>", $content);
    	$content = str_replace("<$lower_tag1/><$upper_tag2 ", "<$lower_tag1/>$added<$upper_tag2 ", $content);
    	$content = str_replace("<$lower_tag1/><$upper_tag2/>", "<$lower_tag1/>$added<$upper_tag2/>", $content);

    	$content = str_replace("</$upper_tag1><$lower_tag2>", "</$upper_tag1>$added<$lower_tag2>", $content);
    	$content = str_replace("</$upper_tag1><$lower_tag2 ", "</$upper_tag1>$added<$lower_tag2 ", $content);
    	$content = str_replace("</$upper_tag1><$lower_tag2/>", "</$upper_tag1>$added<$lower_tag2/>", $content);
    	$content = str_replace("<$upper_tag1/><$lower_tag2>", "<$upper_tag1/>$added<$lower_tag2>", $content);
    	$content = str_replace("<$upper_tag1/><$lower_tag2 ", "<$upper_tag1/>$added<$lower_tag2 ", $content);
    	$content = str_replace("<$upper_tag1/><$lower_tag2/>", "<$upper_tag1/>$added<$lower_tag2/>", $content);

    	$content = str_replace("</$upper_tag1><$upper_tag2>", "</$upper_tag1>$added<$upper_tag2>", $content);
    	$content = str_replace("</$upper_tag1><$upper_tag2 ", "</$upper_tag1>$added<$upper_tag2 ", $content);
    	$content = str_replace("</$upper_tag1><$upper_tag2/>", "</$upper_tag1>$added<$upper_tag2/>", $content);
    	$content = str_replace("<$upper_tag1/><$upper_tag2>", "<$upper_tag1/>$added<$upper_tag2>", $content);
    	$content = str_replace("<$upper_tag1/><$upper_tag2 ", "<$upper_tag1/>$added<$upper_tag2 ", $content);
    	$content = str_replace("<$upper_tag1/><$upper_tag2/>", "<$upper_tag1/>$added<$upper_tag2/>", $content);

    	return $content;
    }

    public static function stripContent($content) {
    	// Clean Text.

    	// remove Return to the line.
    	$content = str_replace("\r\n", " ", $content);
    	$content = str_replace("\n", " ", $content);

    	$content = str_replace('&nbsp;', ' ', $content);

    	// tag li
    	$content = self::addSpaceBeforeTag($content, 'li', true);

    	// tag br
    	$content = self::addSpaceBeforeTag($content, 'br', true);

    	// tag br
    	$content = self::addSpaceBeforeTag($content, 'hr', true);

    	// tag p
    	$content = self::addSpaceBetweenTags($content, 'p', null, true);


    	// tag p + h1
    	$content = self::addSpaceBetweenTags($content, 'h1', 'p', true);
    	$content = self::addSpaceBetweenTags($content, 'p', 'h1', true);

    	// tag p + h2
    	$content = self::addSpaceBetweenTags($content, 'h2', 'p', true);
    	$content = self::addSpaceBetweenTags($content, 'p', 'h2', true);

    	// tag p + h3
    	$content = self::addSpaceBetweenTags($content, 'h3', 'p', true);
    	$content = self::addSpaceBetweenTags($content, 'p', 'h3', true);

    	// tag p + h4
    	$content = self::addSpaceBetweenTags($content, 'h4', 'p', true);
    	$content = self::addSpaceBetweenTags($content, 'p', 'h4', true);

    	// tag p + h5
    	$content = self::addSpaceBetweenTags($content, 'h5', 'p', true);
    	$content = self::addSpaceBetweenTags($content, 'p', 'h5', true);

    	// tag p + h6
    	$content = self::addSpaceBetweenTags($content, 'h6', 'p', true);
    	$content = self::addSpaceBetweenTags($content, 'p', 'h6', true);

    	// tag h1 + h2
    	$content = self::addSpaceBetweenTags($content, 'h1', 'h2', true);

    	// tag h2 + h3
    	$content = self::addSpaceBetweenTags($content, 'h2', 'h3', true);

    	// tag h3 + h4
    	$content = self::addSpaceBetweenTags($content, 'h3', 'h4', true);

    	// tag h4 + h5
    	$content = self::addSpaceBetweenTags($content, 'h4', 'h5', true);

    	// tag h5 + h6
    	$content = self::addSpaceBetweenTags($content, 'h5', 'h6', true);

    	$striped = strip_tags($content);

    	return $striped;
    }

    /**
     * Set urlTitle
     *
     * @param string $urlTitle
     *
     * @return Article
     */
    public function setUrlTitle($urlTitle)
    {
        $this->url_title = $urlTitle;

        return $this;
    }

    /**
     * Get urlTitle
     *
     * @return string
     */
    public function getUrlTitle()
    {
        return $this->url_title;
    }

    protected function transformTitleToUrl($title) {
    	return ZombieUtils::drupal_pathauto_cleanstring($title);
    }

    public function getUrls() {
    	if ($this->source == self::SOURCE_LE_TEMPS) {
    		$base = "https://www.example.com/";
    		$uris = $this->getUris();

    		$res = array();
    		if ($uris) {
    			foreach ($uris as $uri) {
    				$res[] = $base.$uri;
    			}
    		}

    		return $res;
    	}

    	return false;
    }

    public function getUrl() {
    	$urls = $this->getUrls();

    	if ($urls && count($urls) > 1) return $urls[1];
    	if ($urls && count($urls) > 0) return $urls[0];

    	return null;
    }

    public function getUris() {
    	$res = array();

    	if ($this->article_uri_1) $res[] = $this->article_uri_1;
    	if ($this->article_uri_2) $res[] = $this->article_uri_2;
    	if ($this->article_uri_3) $res[] = $this->article_uri_3;
    	if ($this->article_uri_4) $res[] = $this->article_uri_4;
    	if ($this->article_uri_5) $res[] = $this->article_uri_5;
    	if ($this->article_uri_6) $res[] = $this->article_uri_6;
    	if ($this->article_uri_7) $res[] = $this->article_uri_7;
    	if ($this->article_uri_8) $res[] = $this->article_uri_8;
    	if ($this->article_uri_9) $res[] = $this->article_uri_9;
    	if ($this->article_uri_10) $res[] = $this->article_uri_10;

    	return $res;
    }

    public function setUris($uris) {
    	$already_set = array();

    	// in first time, add uri by guid 
    	$this->article_uri_1 = '/node/'.$this->getUid();

    	$already_set[$this->article_uri_1] = $this->article_uri_1;

    	$current = 2;
    	if ($uris) {
    		foreach ($uris as $uri) {
    			if ($current > 10) break;

    			if ($uri && (!isset($already_set[$uri]))) {
    				eval('$this->article_uri_'.$current.' = $uri;');
    				$already_set[$uri] = $uri;
    				$current++;
    			}
    		}
    	}

    	for (; $current <= 10; $current++) {
    		eval('$this->article_uri_'.$current.' = null;');
    	}
    }

    /**
     * Set articleUri1
     *
     * @param string $articleUri1
     *
     * @return Article
     */
    public function setArticleUri1($articleUri1)
    {
        $this->article_uri_1 = $articleUri1;

        return $this;
    }

    /**
     * Get articleUri1
     *
     * @return string
     */
    public function getArticleUri1()
    {
        return $this->article_uri_1;
    }

    /**
     * Set articleUri2
     *
     * @param string $articleUri2
     *
     * @return Article
     */
    public function setArticleUri2($articleUri2)
    {
        $this->article_uri_2 = $articleUri2;

        return $this;
    }

    /**
     * Get articleUri2
     *
     * @return string
     */
    public function getArticleUri2()
    {
        return $this->article_uri_2;
    }

    /**
     * Set articleUri3
     *
     * @param string $articleUri3
     *
     * @return Article
     */
    public function setArticleUri3($articleUri3)
    {
        $this->article_uri_3 = $articleUri3;

        return $this;
    }

    /**
     * Get articleUri3
     *
     * @return string
     */
    public function getArticleUri3()
    {
        return $this->article_uri_3;
    }

    /**
     * Set articleUri4
     *
     * @param string $articleUri4
     *
     * @return Article
     */
    public function setArticleUri4($articleUri4)
    {
        $this->article_uri_4 = $articleUri4;

        return $this;
    }

    /**
     * Get articleUri4
     *
     * @return string
     */
    public function getArticleUri4()
    {
        return $this->article_uri_4;
    }

    /**
     * Set articleUri5
     *
     * @param string $articleUri5
     *
     * @return Article
     */
    public function setArticleUri5($articleUri5)
    {
        $this->article_uri_5 = $articleUri5;

        return $this;
    }

    /**
     * Get articleUri5
     *
     * @return string
     */
    public function getArticleUri5()
    {
        return $this->article_uri_5;
    }

    /**
     * Set articleUri6
     *
     * @param string $articleUri6
     *
     * @return Article
     */
    public function setArticleUri6($articleUri6)
    {
        $this->article_uri_6 = $articleUri6;

        return $this;
    }

    /**
     * Get articleUri6
     *
     * @return string
     */
    public function getArticleUri6()
    {
        return $this->article_uri_6;
    }

    /**
     * Set articleUri7
     *
     * @param string $articleUri7
     *
     * @return Article
     */
    public function setArticleUri7($articleUri7)
    {
        $this->article_uri_7 = $articleUri7;

        return $this;
    }

    /**
     * Get articleUri7
     *
     * @return string
     */
    public function getArticleUri7()
    {
        return $this->article_uri_7;
    }

    /**
     * Set articleUri8
     *
     * @param string $articleUri8
     *
     * @return Article
     */
    public function setArticleUri8($articleUri8)
    {
        $this->article_uri_8 = $articleUri8;

        return $this;
    }

    /**
     * Get articleUri8
     *
     * @return string
     */
    public function getArticleUri8()
    {
        return $this->article_uri_8;
    }

    /**
     * Set articleUri9
     *
     * @param string $articleUri9
     *
     * @return Article
     */
    public function setArticleUri9($articleUri9)
    {
        $this->article_uri_9 = $articleUri9;

        return $this;
    }

    /**
     * Get articleUri9
     *
     * @return string
     */
    public function getArticleUri9()
    {
        return $this->article_uri_9;
    }

    /**
     * Set articleUri10
     *
     * @param string $articleUri10
     *
     * @return Article
     */
    public function setArticleUri10($articleUri10)
    {
        $this->article_uri_10 = $articleUri10;

        return $this;
    }

    /**
     * Get articleUri10
     *
     * @return string
     */
    public function getArticleUri10()
    {
        return $this->article_uri_10;
    }

    /**
     * Set uid
     *
     * @param string $uid
     *
     * @return Article
     */
    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * Get uid
     *
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set section
     *
     * @param string $section
     *
     * @return Article
     */
    public function setSection($section)
    {
        $this->section = $section;

        if ($section) {
        	$this->setUrlSection($this->transformSectionToUrl($section));
        } else {
        	$this->setUrlSection("");
        }

        return $this;
    }

    /**
     * Get section
     *
     * @return string
     *
     * @SER\ListeProperty("section", label="Section", sort="string", format="none", dbfield="section")
     * @SER\ReportingColRowProperty("section", label="Section")
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set auteur
     *
     * @param string $auteur
     *
     * @return Article
     */
    public function setAuteur($auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return string
     * @SER\ReportingColRowProperty("auteur", label="Auteur")
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set urlSection
     *
     * @param string $urlSection
     *
     * @return Article
     */
    public function setUrlSection($urlSection)
    {
        $this->url_section = $urlSection;

        return $this;
    }

    /**
     * Get urlSection
     *
     * @return string
     */
    public function getUrlSection()
    {
        return $this->url_section;
    }

    protected function transformSectionToUrl($section) {
    	return ZombieUtils::drupal_section_to_url($section);
    }

    /**
     * Set nbWords
     *
     * @param integer $nbWords
     *
     * @return Article
     */
    public function setNbWords($nbWords)
    {
        $this->nb_words = $nbWords;

        return $this;
    }

    /**
     * Get nbWords
     *
     * @return integer
     *
     * @SER\ListeProperty("nb_words", label="nb. mots", sort="number", format="none", dbfield="nb_words")
	 * @SER\ReportingDataProperty("nb_words", label="Nombre de mots", label_short="nb. mot", format="number")
    */
    public function getNbWords()
    {
        return $this->nb_words;
    }
    /**
     * Get nbWords
     *
     * @return integer
     *
	 * @SER\ReportingDataProperty("nb_words_avg", label="Moyenne de mots", label_short="moy. mot", format="number", moyenne=true)   
     */
    public function getNbWordsForAvg()
    {
    	return $this->nb_words;
    }
    /**
     *
     * @SER\ListeProperty("metrics_objects", label="Metrics", type="object", multiple="getMetricsModules()")
     * @SER\ReportingDataProperty("metrics_objects", label="Metrics", type="object", multiple="getMetricsModules()")
     * @SER\ReportingColRowProperty("metrics_objects", label="Metrics", type="object", multiple="getMetricsModules()")
     */
    public function getMetricsObject($module = null) {
    	try {
    		if (isset($this->_cache_metrics_objects[$module])) return $this->_cache_metrics_objects[$module];

    		$modules = ManagersManager::getManager()->getZombieModules();
    		if ($modules && isset($modules[$module])) {
    			$module_params = $modules[$module];
    			if ($module_params && isset($module_params['service'])) {
    				// get service.
    				$service = $module_params['service'];
    				if ($service) {
    					$mgr = ManagersManager::getManager()->getContainer()->get($service);
    					if ($mgr && method_exists($mgr, 'getMetricsObjectForArticle')) {
    						$metricsObject = $mgr->getMetricsObjectForArticle($this);
    						$this->_cache_metrics_objects[$module] = $metricsObject;
    						return $metricsObject;
    					}
    				}
    			}
    		}
    	} catch (Exception $ex) {
    		$logger = ManagersManager::getManager()->getContainer()->get('logger');
    		$logger->error($ex);
    	}
    	return null;
    }

    public static function getMetricsModules() {
    	try {
    		$modules = ManagersManager::getManager()->getZombieModules();

    		$datas = array();
    		if ($modules) {
    			foreach ($modules as $name => $infos) {
    				if ($infos && isset($infos['metrics_object_class'])) {
    					$datas[] = array($name, $infos['metrics_object_class']);
    				}
    			}
    		}

    		return $datas;
    	} catch (Exception $ex) {
    		$logger = ManagersManager::getManager()->getContainer()->get('logger');
    		$logger->error($ex);
    	}

    	return array();
    }

    /**
     * Set auteurExterne
     *
     * @param string $auteurExterne
     *
     * @return Article
     */
    public function setAuteurExterne($auteurExterne)
    {
        $this->auteur_externe = $auteurExterne;

        return $this;
    }

    /**
     * Get auteurExterne
     *
     * @return string
     */
    public function getAuteurExterne()
    {
        return $this->auteur_externe;
    }

    /**
     * Set imageUrl
     *
     * @param string $imageUrl
     *
     * @return Article
     */
    public function setImageUrl($imageUrl)
    {
        $this->image_url = $imageUrl;

        return $this;
    }

    /**
     * Get imageUrl
     *
     * @return string
     */
    public function getImageUrl()
    {
        return $this->image_url;
    }

    /**
     * Set chapeauStriped
     *
     * @param string $chapeauStriped
     *
     * @return Article
     */
    public function setChapeauStriped($chapeauStriped)
    {
        $this->chapeau_striped = $chapeauStriped;

        return $this;
    }

    /**
     * Get chapeauStriped
     *
     * @return string
     */
    public function getChapeauStriped()
    {
        return $this->chapeau_striped;
    }

    /**
     * Get readTime per second
	 * @SER\ReportingDataProperty("read_time", label="Temps de lecture (s)", label_short="Lecture", format="number", moyenne=true)  
     * @return integer
     */
    public function getReadTime()
    {
    	//5 word/second by default change in parameter.yml
    	if ($GLOBALS['kernel']->getContainer()->hasParameter('read_wordpersecond')) {
    		$wordpersecond= $GLOBALS['kernel']->getContainer()->getParameter('read_wordpersecond');
    	}
    	else {
    		$wordpersecond = 5;
    	}
    	$readtime = $this->getNbWords() * $wordpersecond;
    	if (isset($readtime) and $readtime != 0 )
    	{
    		return $readtime;
    	}
    	else {
    		return 1;
    	}
    }
    
    /**
     * 
     * @return number
     * 
     * @SER\ListeProperty("note_semantique", label="Note sémantique", sort="number", format="none", dbfield="sh.num1")
	 * @SER\ReportingDataProperty("note_semantique", label="Note sémantique", label_short="Semantique", format="number", moyenne=true)  
     */
    public function getNoteSemantique() {
    	return $this->note_semantique;
    }
    public function setNoteSemantique($note_semantique) {
    	$this->note_semantique = round($note_semantique);
    }
}
