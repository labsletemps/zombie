<?php

namespace Seriel\DonReachBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Annotation as SER;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;
use Seriel\AppliToolboxBundle\Managers\ManagersManager;
use Seriel\DonReachBundle\Managers\DonReachArticleMetricsManager;
use ZombieBundle\Utils\ZombieUtils;
use ZombieBundle\API\Entity\ArticleMetrics;
use Seriel\AppliToolboxBundle\Annotation\SerielListePropertyConverter;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use Seriel\DonReachBundle\Managers\DonReachManager;

/**
 * @ORM\Entity
 * @ORM\Table(name="donreach_article_metrics",options={"engine"="MyISAM"})
 */
class DonReachArticleMetrics extends RootObject implements ArticleMetrics, Listable
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
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $buffer;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $facebook;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $fancy;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $google;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $hackernews;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $hatena;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $linkedin;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $mailru;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $odnoklassniki;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $pinterest;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $pocket;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $reddit;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $scoopit;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $stumbleupon;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $tumblr;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $twitter;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $vk;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $weibo;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $xing;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $yummly;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $buffer_indicator;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $facebook_indicator;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $fancy_indicator;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $google_indicator;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $hackernews_indicator;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $hatena_indicator;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $linkedin_indicator;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $mailru_indicator;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $odnoklassniki_indicator;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $pinterest_indicator;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $pocket_indicator;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $reddit_indicator;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $scoopit_indicator;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $stumbleupon_indicator;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $tumblr_indicator;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $twitter_indicator;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $vk_indicator;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $weibo_indicator;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $xing_indicator;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $yummly_indicator;
	
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
	

	public static function updateFromArray(DonReachArticleMetrics $dram, $data, $opt1 = null, $opt2 = null, $opt3 = null, $opt4 = null, $opt5 = null, $opt6 = null, $opt7 = null, $opt8 = null, $opt9 = null) {
		if (!$data) return null;

		$data = (array)$data;

		$dram->date_calcul = $data['day'];
		
		$dram->buffer = (isset($data['buffer'])) ? max($data['buffer'],$dram->buffer) : $dram->buffer;
		$dram->facebook = (isset($data['facebook'])) ? max($data['facebook'],$dram->facebook): $dram->facebook;
		$dram->fancy = (isset($data['fancy'])) ? max($data['fancy'],$dram->fancy): $dram->fancy;
		$dram->google = (isset($data['google'])) ? max($data['google'],$dram->google): $dram->google;
		$dram->hackernews = (isset($data['hackernews'])) ? max($data['hackernews'],$dram->hackernews): $dram->hackernews;
		$dram->hatena = (isset($data['hatena'])) ? max($data['hatena'],$dram->hatena): $dram->hatena;
		$dram->linkedin = (isset($data['linkedin'])) ? max($data['linkedin'],$dram->linkedin): $dram->linkedin;
		$dram->mailru = (isset($data['mailru'])) ? max($data['mailru'],$dram->mailru): $dram->mailru;
		$dram->odnoklassniki = (isset($data['odnoklassniki'])) ? max($data['odnoklassniki'],$dram->odnoklassniki): $dram->odnoklassniki;
		$dram->pinterest = (isset($data['pinterest'])) ? max($data['pinterest'],$dram->pinterest): $dram->pinterest;
		$dram->pocket = (isset($data['pocket'])) ? max($data['pocket'],$dram->pocket): $dram->pocket;
		$dram->reddit = (isset($data['reddit'])) ? max($data['reddit'],$dram->reddit): $dram->reddit;
		$dram->scoopit = (isset($data['scoopit'])) ? max($data['scoopit'],$dram->scoopit): $dram->scoopit;
		$dram->stumbleupon = (isset($data['stumbleupon'])) ? max($data['stumbleupon'],$dram->stumbleupon): $dram->stumbleupon;
		$dram->tumblr = (isset($data['tumblr'])) ? max($data['tumblr'],$dram->tumblr): $dram->tumblr;
		$dram->twitter = (isset($data['twitter'])) ? max($data['twitter'],$dram->twitter): $dram->twitter;
		$dram->vk = (isset($data['vk'])) ? max($data['vk'],$dram->vk): $dram->vk;
		$dram->weibo = (isset($data['weibo'])) ? max($data['weibo'],$dram->weibo): $dram->weibo;
		$dram->xing = (isset($data['xing'])) ? max($data['xing'],$dram->xing): $dram->xing;
		$dram->yummly = (isset($data['yummly'])) ? max($data['yummly'],$dram->yummly): $dram->yummly;

		$dram->setOption1($opt1);
		$dram->setOption2($opt2);
		$dram->setOption3($opt3);
		$dram->setOption4($opt4);
		$dram->setOption5($opt5);
		$dram->setOption6($opt6);
		$dram->setOption7($opt7);
		$dram->setOption8($opt8);
		$dram->setOption9($opt9);

		return $dram;
	}
	
	public static function createFromArray($article, $data, $opt1 = null, $opt2 = null, $opt3 = null, $opt4 = null, $opt5 = null, $opt6 = null, $opt7 = null, $opt8 = null, $opt9 = null) {
		if (!$data) return null;
		$dram = new DonReachArticleMetrics($article);

		return self::updateFromArray($dram, $data, $opt1, $opt2, $opt3, $opt4, $opt5, $opt6, $opt7, $opt8, $opt9);
	}
	
	
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
     * Set buffer
     *
     * @param integer $buffer
     *
     * @return DonReachArticleMetrics
     */
    public function setBuffer($buffer)
    {
        $this->buffer = $buffer;

        return $this;
    }

    /**
     * Get buffer
     *
     * @return integer
     * 
     * @SER\ListeProperty("buffer", label="buffer measure", sort="number",class_supp="measure", format="none", credential="view_measure", dbfield="buffer")
	 * @SER\ReportingDataProperty("buffer", label="buffer measure", label_short="buffer", credential="view_measure", format="number")
     */
    public function getBuffer()
    {
        return $this->buffer;
    }

    /**
     * Set facebook
     *
     * @param integer $facebook
     *
     * @return DonReachArticleMetrics
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get facebook
     *
     * @return integer
     * 
     * @SER\ListeProperty("facebook", label="facebook measure", sort="number",class_supp="measure", format="none", credential="view_measure", dbfield="facebook")
	 * @SER\ReportingDataProperty("facebook", label="facebook measure", label_short="facebook", credential="view_measure", format="number")
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set fancy
     *
     * @param integer $fancy
     *
     * @return DonReachArticleMetrics
     */
    public function setFancy($fancy)
    {
        $this->fancy = $fancy;

        return $this;
    }

    /**
     * Get fancy
     *
     * @return integer
     * 
     * @SER\ListeProperty("fancy", label="fancy measure", sort="number",class_supp="measure", format="none", credential="view_measure", dbfield="fancy")
	 * @SER\ReportingDataProperty("fancy", label="fancy measure", label_short="fancy", credential="view_measure", format="number")
     */
    public function getFancy()
    {
        return $this->fancy;
    }

    /**
     * Set google
     *
     * @param integer $google
     *
     * @return DonReachArticleMetrics
     */
    public function setGoogle($google)
    {
        $this->google = $google;

        return $this;
    }

    /**
     * Get google
     *
     * @return integer
     * 
     * @SER\ListeProperty("google", label="google measure", sort="number",class_supp="measure", format="none", credential="view_measure", dbfield="google")
	 * @SER\ReportingDataProperty("google", label="google measure", label_short="google", credential="view_measure", format="number")
     */
    public function getGoogle()
    {
        return $this->google;
    }

    /**
     * Set hackernews
     *
     * @param integer $hackernews
     *
     * @return DonReachArticleMetrics
     */
    public function setHackernews($hackernews)
    {
        $this->hackernews = $hackernews;

        return $this;
    }

    /**
     * Get hackernews
     *
     * @return integer
     * 
     * @SER\ListeProperty("hackernews", label="hackernews measure", sort="number",class_supp="measure", format="none", credential="view_measure", dbfield="hackernews")
	 * @SER\ReportingDataProperty("hackernews", label="hackernews measure", label_short="hackernews", credential="view_measure", format="number")
     */
    public function getHackernews()
    {
        return $this->hackernews;
    }

    /**
     * Set hatena
     *
     * @param integer $hatena
     *
     * @return DonReachArticleMetrics
     */
    public function setHatena($hatena)
    {
        $this->hatena = $hatena;

        return $this;
    }

    /**
     * Get hatena
     *
     * @return integer
     * 
     * @SER\ListeProperty("hatena", label="hatena measure", sort="number",class_supp="measure", format="none", credential="view_measure", dbfield="hatena")
	 * @SER\ReportingDataProperty("hatena", label="hatena measure", label_short="hatena", credential="view_measure", format="number")
     */
    public function getHatena()
    {
        return $this->hatena;
    }

    /**
     * Set linkedin
     *
     * @param integer $linkedin
     *
     * @return DonReachArticleMetrics
     */
    public function setLinkedin($linkedin)
    {
        $this->linkedin = $linkedin;

        return $this;
    }

    /**
     * Get linkedin
     *
     * @return integer
     * 
     * @SER\ListeProperty("linkedin", label="linkedin measure", sort="number",class_supp="measure", format="none", credential="view_measure", dbfield="linkedin")
	 * @SER\ReportingDataProperty("linkedin", label="linkedin measure", label_short="linkedin", credential="view_measure", format="number")
     */
    public function getLinkedin()
    {
        return $this->linkedin;
    }

    /**
     * Set mailru
     *
     * @param integer $mailru
     *
     * @return DonReachArticleMetrics
     */
    public function setMailru($mailru)
    {
        $this->mailru = $mailru;

        return $this;
    }

    /**
     * Get mailru
     *
     * @return integer
     * 
     * @SER\ListeProperty("mailru", label="mailru measure", sort="number",class_supp="measure", format="none", credential="view_measure", dbfield="mailru")
	 * @SER\ReportingDataProperty("mailru", label="mailru measure", label_short="mailru", credential="view_measure", format="number")
     */
    public function getMailru()
    {
        return $this->mailru;
    }

    /**
     * Set odnoklassniki
     *
     * @param integer $odnoklassniki
     *
     * @return DonReachArticleMetrics
     */
    public function setOdnoklassniki($odnoklassniki)
    {
        $this->odnoklassniki = $odnoklassniki;

        return $this;
    }

    /**
     * Get odnoklassniki
     *
     * @return integer
     * 
     * @SER\ListeProperty("odnoklassniki", label="odnoklassniki measure", sort="number",class_supp="measure", format="none", credential="view_measure", dbfield="odnoklassniki")
	 * @SER\ReportingDataProperty("odnoklassniki", label="odnoklassniki measure", label_short="odnoklassniki", credential="view_measure", format="number")
     */
    public function getOdnoklassniki()
    {
        return $this->odnoklassniki;
    }

    /**
     * Set pinterest
     *
     * @param integer $pinterest
     *
     * @return DonReachArticleMetrics
     */
    public function setPinterest($pinterest)
    {
        $this->pinterest = $pinterest;

        return $this;
    }

    /**
     * Get pinterest
     *
     * @return integer
     * 
     * @SER\ListeProperty("pinterest", label="pinterest measure", sort="number",class_supp="measure", format="none", credential="view_measure", dbfield="pinterest")
	 * @SER\ReportingDataProperty("pinterest", label="pinterest measure", label_short="pinterest", credential="view_measure", format="number")
     */
    public function getPinterest()
    {
        return $this->pinterest;
    }

    /**
     * Set pocket
     *
     * @param integer $pocket
     *
     * @return DonReachArticleMetrics
     */
    public function setPocket($pocket)
    {
        $this->pocket = $pocket;

        return $this;
    }

    /**
     * Get pocket
     *
     * @return integer
     * 
     * @SER\ListeProperty("pocket", label="pocket measure", sort="number",class_supp="measure", format="none", credential="view_measure", dbfield="pocket")
	 * @SER\ReportingDataProperty("pocket", label="pocket measure", label_short="pocket", credential="view_measure", format="number")
     */
    public function getPocket()
    {
        return $this->pocket;
    }

    /**
     * Set reddit
     *
     * @param integer $reddit
     *
     * @return DonReachArticleMetrics
     */
    public function setReddit($reddit)
    {
        $this->reddit = $reddit;

        return $this;
    }

    /**
     * Get reddit
     *
     * @return integer
     * 
     * @SER\ListeProperty("reddit", label="reddit measure", sort="number",class_supp="measure", format="none", credential="view_measure", dbfield="reddit")
	 * @SER\ReportingDataProperty("reddit", label="reddit measure", label_short="reddit", credential="view_measure", format="number")
     */
    public function getReddit()
    {
        return $this->reddit;
    }

    /**
     * Set scoopit
     *
     * @param integer $scoopit
     *
     * @return DonReachArticleMetrics
     */
    public function setScoopit($scoopit)
    {
        $this->scoopit = $scoopit;

        return $this;
    }

    /**
     * Get scoopit
     *
     * @return integer
     * 
     * @SER\ListeProperty("scoopit", label="scoopit measure", sort="number",class_supp="measure", format="none", credential="view_measure", dbfield="scoopit")
	 * @SER\ReportingDataProperty("scoopit", label="scoopit measure", label_short="scoopit", credential="view_measure", format="number")
     */
    public function getScoopit()
    {
        return $this->scoopit;
    }

    /**
     * Set stumbleupon
     *
     * @param integer $stumbleupon
     *
     * @return DonReachArticleMetrics
     */
    public function setStumbleupon($stumbleupon)
    {
        $this->stumbleupon = $stumbleupon;

        return $this;
    }

    /**
     * Get stumbleupon
     *
     * @return integer
     * 
     * @SER\ListeProperty("stumbleupon", label="stumbleupon measure", sort="number",class_supp="measure", format="none", credential="view_measure", dbfield="stumbleupon")
	 * @SER\ReportingDataProperty("stumbleupon", label="stumbleupon measure", label_short="stumbleupon", credential="view_measure", format="number")
     */
    public function getStumbleupon()
    {
        return $this->stumbleupon;
    }

    /**
     * Set tumblr
     *
     * @param integer $tumblr
     *
     * @return DonReachArticleMetrics
     */
    public function setTumblr($tumblr)
    {
        $this->tumblr = $tumblr;

        return $this;
    }

    /**
     * Get tumblr
     *
     * @return integer
     * 
     * @SER\ListeProperty("tumblr", label="tumblr measure", sort="number",class_supp="measure", format="none", credential="view_measure", dbfield="tumblr")
	 * @SER\ReportingDataProperty("tumblr", label="tumblr measure", label_short="tumblr", credential="view_measure", format="number")
     */
    public function getTumblr()
    {
        return $this->tumblr;
    }

    /**
     * Set twitter
     *
     * @param integer $twitter
     *
     * @return DonReachArticleMetrics
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * Get twitter
     *
     * @return integer
     * 
     * @SER\ListeProperty("twitter", label="twitter measure", sort="number",class_supp="measure", format="none", credential="view_measure", dbfield="twitter")
	 * @SER\ReportingDataProperty("twitter", label="twitter measure", label_short="twitter", credential="view_measure", format="number")
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * Set vk
     *
     * @param integer $vk
     *
     * @return DonReachArticleMetrics
     */
    public function setVk($vk)
    {
        $this->vk = $vk;

        return $this;
    }

    /**
     * Get vk
     *
     * @return integer
     * 
     * @SER\ListeProperty("vk", label="vk measure", sort="number",class_supp="measure", credential="view_measure", format="none", dbfield="vk")
	 * @SER\ReportingDataProperty("vk", label="vk measure", label_short="vk", credential="view_measure", format="number")
     */
    public function getVk()
    {
        return $this->vk;
    }

    /**
     * Set weibo
     *
     * @param integer $weibo
     *
     * @return DonReachArticleMetrics
     */
    public function setWeibo($weibo)
    {
        $this->weibo = $weibo;

        return $this;
    }

    /**
     * Get weibo
     *
     * @return integer
     * 
     * @SER\ListeProperty("weibo", label="weibo measure", sort="number",class_supp="measure", format="none", credential="view_measure", dbfield="weibo")
	 * @SER\ReportingDataProperty("weibo", label="weibo measure", label_short="weibo", credential="view_measure", format="number")
     */
    public function getWeibo()
    {
        return $this->weibo;
    }

    /**
     * Set xing
     *
     * @param integer $xing
     *
     * @return DonReachArticleMetrics
     */
    public function setXing($xing)
    {
        $this->xing = $xing;

        return $this;
    }

    /**
     * Get xing
     *
     * @return integer
     * 
     * @SER\ListeProperty("xing", label="xing measure", sort="number", format="none", credential="view_measure", dbfield="xing")
	 * @SER\ReportingDataProperty("xing", label="xing measure", label_short="xing", credential="view_measure", format="number")
     */
    public function getXing()
    {
        return $this->xing;
    }

    /**
     * Set yummly
     *
     * @param integer $yummly
     *
     * @return DonReachArticleMetrics
     */
    public function setYummly($yummly)
    {
        $this->yummly = $yummly;

        return $this;
    }

    /**
     * Get yummly
     *
     * @return integer
     * 
     * @SER\ListeProperty("yummly", label="yummly measure", sort="number", format="none", credential="view_measure", dbfield="yummly")
	 * @SER\ReportingDataProperty("yummly", label="yummly measure", label_short="yummly", credential="view_measure", format="number")
     */
    public function getYummly()
    {
        return $this->yummly;
    }

    /**
     * Set article
     *
     * @param \ZombieBundle\Entity\News\Article $article
     *
     * @return DonReachArticleMetrics
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

    /**
     * Set dateParution
     *
     * @param \DateTime $dateParution
     *
     * @return DonReachArticleMetrics
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
     * @return DonReachArticleMetrics
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
     * Set option1
     *
     * @param string $option1
     *
     * @return DonReachArticleMetrics
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
     * @return DonReachArticleMetrics
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
     * @return DonReachArticleMetrics
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
     * @return DonReachArticleMetrics
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
     * @return DonReachArticleMetrics
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
     * @return DonReachArticleMetrics
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
     * @return DonReachArticleMetrics
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
     * @return DonReachArticleMetrics
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
     * @return DonReachArticleMetrics
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
     * Set bufferIndicator
     *
     * @param integer $bufferIndicator
     *
     * @return DonReachArticleMetrics
     */
    public function setBufferIndicator($bufferIndicator)
    {
        $this->buffer_indicator = $bufferIndicator;

        return $this;
    }

    /**
     * Get bufferIndicator
     *
     * @return integer
     * 
     * @SER\ListeProperty("buffer_indicator",labellogo="/images/buffer_indicator.png", label="getNameBufferIndicator()", sort="number",class_supp="indicator", format="none", credential="view_indicator", dbfield="buffer_indicator")
	 * @SER\ReportingDataProperty("buffer_indicator",labellogo="/images/buffer_indicator.png", label="getNameBufferIndicator()", label_short="getNameBufferIndicator()", format="number", credential="view_indicator", moyenne=true)
     * @SER\ReportingColRowProperty("buffer_indicator",labellogo="/images/buffer_indicator.png", label="getNameBufferIndicator()", option="percent" , credential="view_indicator")
     */
    public function getBufferIndicator()
    {
        return $this->buffer_indicator;
    }

    
    public static function getNameBufferIndicator()
    {
    	$DRMgr= ManagersManager::getManager()->getContainer()->get('seriel_donreach.manager');
    	if (false) $DRMgr= new DonReachManager();
    	return $DRMgr->getLabelIndicator('buffer_indicator');
    }
    
    /**
     * Set facebookIndicator
     *
     * @param integer $facebookIndicator
     *
     * @return DonReachArticleMetrics
     */
    public function setFacebookIndicator($facebookIndicator)
    {
        $this->facebook_indicator = $facebookIndicator;

        return $this;
    }

    /**
     * Get facebookIndicator
     *
     * @return integer
     * 
     * @SER\ListeProperty("facebook_indicator",labellogo="/images/facebook_indicator.png", label="getNameFacebookIndicator()", sort="number",class_supp="indicator", format="none", credential="view_indicator", dbfield="facebook_indicator")
	 * @SER\ReportingDataProperty("facebook_indicator",labellogo="/images/facebook_indicator.png", label="getNameFacebookIndicator()", label_short="getNameFacebookIndicator()", format="number", credential="view_indicator", moyenne=true)
     * @SER\ReportingColRowProperty("facebook_indicator",labellogo="/images/facebook_indicator.png", label="getNameFacebookIndicator()", option="percent" , credential="view_indicator")
     */
    public function getFacebookIndicator()
    {
        return $this->facebook_indicator;
    }
    
    public static function getNameFacebookIndicator()
    {
    	$DRMgr= ManagersManager::getManager()->getContainer()->get('seriel_donreach.manager');
    	if (false) $DRMgr= new DonReachManager();
    	return $DRMgr->getLabelIndicator('facebook_indicator');
    }

    /**
     * Set fancyIndicator
     *
     * @param integer $fancyIndicator
     *
     * @return DonReachArticleMetrics
     */
    public function setFancyIndicator($fancyIndicator)
    {
        $this->fancy_indicator = $fancyIndicator;

        return $this;
    }

    /**
     * Get fancyIndicator
     *
     * @return integer
     * 
     * @SER\ListeProperty("fancy_indicator",labellogo="/images/fancy_indicator.png", label="getNameFancyIndicator()", sort="number",class_supp="indicator", format="none", credential="view_indicator", dbfield="fancy_indicator")
	 * @SER\ReportingDataProperty("fancy_indicator",labellogo="/images/fancy_indicator.png", label="getNameFancyIndicator()", label_short="getNameFancyIndicator()", format="number", credential="view_indicator", moyenne=true)
     * @SER\ReportingColRowProperty("fancy_indicator",labellogo="/images/fancy_indicator.png", label="getNameFancyIndicator()", option="percent" , credential="view_indicator")
     */
    public function getFancyIndicator()
    {
        return $this->fancy_indicator;
    }
    
    public static function getNameFancyIndicator()
    {
    	$DRMgr= ManagersManager::getManager()->getContainer()->get('seriel_donreach.manager');
    	if (false) $DRMgr= new DonReachManager();
    	return $DRMgr->getLabelIndicator('fancy_indicator');
    }

    /**
     * Set googleIndicator
     *
     * @param integer $googleIndicator
     *
     * @return DonReachArticleMetrics
     */
    public function setGoogleIndicator($googleIndicator)
    {
        $this->google_indicator = $googleIndicator;

        return $this;
    }

    /**
     * Get googleIndicator
     *
     * @return integer
     * 
     * @SER\ListeProperty("google_indicator",labellogo="/images/google_indicator.png", label="getNameGoogleIndicator()", sort="number",class_supp="indicator", format="none", credential="view_indicator", dbfield="google_indicator")
	 * @SER\ReportingDataProperty("google_indicator",labellogo="/images/google_indicator.png", label="getNameGoogleIndicator()", label_short="getNameGoogleIndicator()", format="number", credential="view_indicator", moyenne=true)
     * @SER\ReportingColRowProperty("google_indicator",labellogo="/images/google_indicator.png", label="getNameGoogleIndicator()", option="percent" , credential="view_indicator")
     */
    public function getGoogleIndicator()
    {
        return $this->google_indicator;
    }

    public static function getNameGoogleIndicator()
    {
    	$DRMgr= ManagersManager::getManager()->getContainer()->get('seriel_donreach.manager');
    	if (false) $DRMgr= new DonReachManager();
    	return $DRMgr->getLabelIndicator('google_indicator');
    }
    
    /**
     * Set hackernewsIndicator
     *
     * @param integer $hackernewsIndicator
     *
     * @return DonReachArticleMetrics
     */
    public function setHackernewsIndicator($hackernewsIndicator)
    {
        $this->hackernews_indicator = $hackernewsIndicator;

        return $this;
    }

    /**
     * Get hackernewsIndicator
     *
     * @return integer
     * 
     * @SER\ListeProperty("hackernews_indicator",labellogo="/images/hackernews_indicator.png", label="getNameHackernewsIndicator()", sort="number",class_supp="indicator", format="none", credential="view_indicator", dbfield="hackernews_indicator")
	 * @SER\ReportingDataProperty("hackernews_indicator",labellogo="/images/hackernews_indicator.png", label="getNameHackernewsIndicator()", label_short="getNameHackernewsIndicator()", format="number", credential="view_indicator", moyenne=true)
     * @SER\ReportingColRowProperty("hackernews_indicator",labellogo="/images/hackernews_indicator.png", label="getNameHackernewsIndicator()", option="percent" , credential="view_indicator")
     */
    public function getHackernewsIndicator()
    {
        return $this->hackernews_indicator;
    }

    public static function getNameHackernewsIndicator()
    {
    	$DRMgr= ManagersManager::getManager()->getContainer()->get('seriel_donreach.manager');
    	if (false) $DRMgr= new DonReachManager();
    	return $DRMgr->getLabelIndicator('hackernews_indicator');
    }
    
    /**
     * Set hatenaIndicator
     *
     * @param integer $hatenaIndicator
     *
     * @return DonReachArticleMetrics
     */
    public function setHatenaIndicator($hatenaIndicator)
    {
        $this->hatena_indicator = $hatenaIndicator;

        return $this;
    }

    /**
     * Get hatenaIndicator
     *
     * @return integer
     * 
     * @SER\ListeProperty("hatena_indicator",labellogo="/images/hatena_indicator.png", label="getNameHatenaIndicator()", sort="number",class_supp="indicator", format="none", credential="view_indicator", dbfield="hatena_indicator")
	 * @SER\ReportingDataProperty("hatena_indicator",labellogo="/images/hatena_indicator.png", label="getNameHatenaIndicator()", label_short="getNameHatenaIndicator()", format="number", credential="view_indicator", moyenne=true)
     * @SER\ReportingColRowProperty("hatena_indicator",labellogo="/images/hatena_indicator.png", label="getNameHatenaIndicator()", option="percent" , credential="view_indicator")
     */
    public function getHatenaIndicator()
    {
        return $this->hatena_indicator;
    }

    public static function getNameHatenaIndicator()
    {
    	$DRMgr= ManagersManager::getManager()->getContainer()->get('seriel_donreach.manager');
    	if (false) $DRMgr= new DonReachManager();
    	return $DRMgr->getLabelIndicator('hatena_indicator');
    }

    /**
     * Set linkedinIndicator
     *
     * @param integer $linkedinIndicator
     *
     * @return DonReachArticleMetrics
     */
    public function setLinkedinIndicator($linkedinIndicator)
    {
        $this->linkedin_indicator = $linkedinIndicator;

        return $this;
    }

    /**
     * Get linkedinIndicator
     *
     * @return integer
     * 
     * @SER\ListeProperty("linkedin_indicator",labellogo="/images/linkedin_indicator.png", label="getNameLinkedinIndicator()", sort="number",class_supp="indicator", format="none", credential="view_indicator", dbfield="linkedin_indicator")
	 * @SER\ReportingDataProperty("linkedin_indicator",labellogo="/images/linkedin_indicator.png", label="getNameLinkedinIndicator()", label_short="getNameLinkedinIndicator()", format="number", credential="view_indicator", moyenne=true)
     * @SER\ReportingColRowProperty("linkedin_indicator",labellogo="/images/linkedin_indicator.png", label="getNameLinkedinIndicator()", option="percent" , credential="view_indicator")
     */
    public function getLinkedinIndicator()
    {
        return $this->linkedin_indicator;
    }

    public static function getNameLinkedinIndicator()
    {
    	$DRMgr= ManagersManager::getManager()->getContainer()->get('seriel_donreach.manager');
    	if (false) $DRMgr= new DonReachManager();
    	return $DRMgr->getLabelIndicator('linkedin_indicator');
    }
    
    /**
     * Set mailruIndicator
     *
     * @param integer $mailruIndicator
     *
     * @return DonReachArticleMetrics
     */
    public function setMailruIndicator($mailruIndicator)
    {
        $this->mailru_indicator = $mailruIndicator;

        return $this;
    }

    /**
     * Get mailruIndicator
     *
     * @return integer
     * 
     * @SER\ListeProperty("mailru_indicator",labellogo="/images/mailru_indicator.png", label="getNameMailruIndicator()", sort="number",class_supp="indicator", format="none", credential="view_indicator", dbfield="mailru_indicator")
	 * @SER\ReportingDataProperty("mailru_indicator",labellogo="/images/mailru_indicator.png", label="getNameMailruIndicator()", label_short="getNameMailruIndicator()", format="number", credential="view_indicator", moyenne=true)
     * @SER\ReportingColRowProperty("mailru_indicator",labellogo="/images/mailru_indicator.png", label="getNameMailruIndicator()", option="percent" , credential="view_indicator")
     */
    public function getMailruIndicator()
    {
        return $this->mailru_indicator;
    }
    
    public static function getNameMailruIndicator()
    {
    	$DRMgr= ManagersManager::getManager()->getContainer()->get('seriel_donreach.manager');
    	if (false) $DRMgr= new DonReachManager();
    	return $DRMgr->getLabelIndicator('mailru_indicator');
    }

    /**
     * Set odnoklassnikiIndicator
     *
     * @param integer $odnoklassnikiIndicator
     *
     * @return DonReachArticleMetrics
     */
    public function setOdnoklassnikiIndicator($odnoklassnikiIndicator)
    {
        $this->odnoklassniki_indicator = $odnoklassnikiIndicator;

        return $this;
    }

    /**
     * Get odnoklassnikiIndicator
     *
     * @return integer
     * 
     * @SER\ListeProperty("odnoklassniki_indicator",labellogo="/images/odnoklassniki_indicator.png", label="getNameOdnoklassnikiIndicator()",class_supp="indicator", sort="number", format="none", credential="view_indicator", dbfield="odnoklassniki_indicator")
	 * @SER\ReportingDataProperty("odnoklassniki_indicator",labellogo="/images/odnoklassniki_indicator.png", label="getNameOdnoklassnikiIndicator()", label_short="getNameOdnoklassnikiIndicator()", format="number", credential="view_indicator", moyenne=true)
     * @SER\ReportingColRowProperty("odnoklassniki_indicator",labellogo="/images/odnoklassniki_indicator.png", label="getNameOdnoklassnikiIndicator()", option="percent" , credential="view_indicator")
     */
    public function getOdnoklassnikiIndicator()
    {
        return $this->odnoklassniki_indicator;
    }

    public static function getNameOdnoklassnikiIndicator()
    {
    	$DRMgr= ManagersManager::getManager()->getContainer()->get('seriel_donreach.manager');
    	if (false) $DRMgr= new DonReachManager();
    	return $DRMgr->getLabelIndicator('odnoklassniki_indicator');
    }
    
    /**
     * Set pinterestIndicator
     *
     * @param integer $pinterestIndicator
     *
     * @return DonReachArticleMetrics
     */
    public function setPinterestIndicator($pinterestIndicator)
    {
        $this->pinterest_indicator = $pinterestIndicator;

        return $this;
    }

    /**
     * Get pinterestIndicator
     *
     * @return integer
     * 
     * @SER\ListeProperty("pinterest_indicator",labellogo="/images/pinterest_indicator.png", label="getNamePinterestIndicator()", sort="number",class_supp="indicator", format="none", credential="view_indicator", dbfield="pinterest_indicator")
	 * @SER\ReportingDataProperty("pinterest_indicator",labellogo="/images/pinterest_indicator.png", label="getNamePinterestIndicator()", label_short="getNamePinterestIndicator()", format="number", credential="view_indicator", moyenne=true)    
     * @SER\ReportingColRowProperty("pinterest_indicator",labellogo="/images/pinterest_indicator.png", label="getNamePinterestIndicator()", option="percent" , credential="view_indicator")
     */
    public function getPinterestIndicator()
    {
        return $this->pinterest_indicator;
    }

    public static function getNamePinterestIndicator()
    {
    	$DRMgr= ManagersManager::getManager()->getContainer()->get('seriel_donreach.manager');
    	if (false) $DRMgr= new DonReachManager();
    	return $DRMgr->getLabelIndicator('pinterest_indicator');
    }
    
    /**
     * Set pocketIndicator
     *
     * @param integer $pocketIndicator
     *
     * @return DonReachArticleMetrics
     */
    public function setPocketIndicator($pocketIndicator)
    {
        $this->pocket_indicator = $pocketIndicator;

        return $this;
    }

    /**
     * Get pocketIndicator
     *
     * @return integer
     * 
     * @SER\ListeProperty("pocket_indicator",labellogo="/images/pocket_indicator.png", label="getNamePocketIndicator()", sort="number",class_supp="indicator", format="none", credential="view_indicator", dbfield="pocket_indicator")
	 * @SER\ReportingDataProperty("pocket_indicator",labellogo="/images/pocket_indicator.png", label="getNamePocketIndicator()", label_short="getNamePocketIndicator()", format="number", credential="view_indicator", moyenne=true) 
     * @SER\ReportingColRowProperty("pocket_indicator",labellogo="/images/pocket_indicator.png", label="getNamePocketIndicator()", option="percent" , credential="view_indicator")
     */
    public function getPocketIndicator()
    {
        return $this->pocket_indicator;
    }

    public static function getNamePocketIndicator()
    {
    	$DRMgr= ManagersManager::getManager()->getContainer()->get('seriel_donreach.manager');
    	if (false) $DRMgr= new DonReachManager();
    	return $DRMgr->getLabelIndicator('pocket_indicator');
    }
    
    /**
     * Set redditIndicator
     *
     * @param integer $redditIndicator
     *
     * @return DonReachArticleMetrics
     */
    public function setRedditIndicator($redditIndicator)
    {
        $this->reddit_indicator = $redditIndicator;

        return $this;
    }

    /**
     * Get redditIndicator
     *
     * @return integer
     * 
     * @SER\ListeProperty("reddit_indicator",labellogo="/images/reddit_indicator.png", label="getNameRedditIndicator()", sort="number",class_supp="indicator", format="none", credential="view_indicator", dbfield="reddit_indicator")
	 * @SER\ReportingDataProperty("reddit_indicator",labellogo="/images/reddit_indicator.png", label="getNameRedditIndicator()", label_short="getNameRedditIndicator()", format="number", credential="view_indicator", moyenne=true) 
     * @SER\ReportingColRowProperty("reddit_indicator",labellogo="/images/reddit_indicator.png", label="getNameRedditIndicator()", option="percent" , credential="view_indicator")
     */
    public function getRedditIndicator()
    {
        return $this->reddit_indicator;
    }
    
    public static function getNameRedditIndicator()
    {
    	$DRMgr= ManagersManager::getManager()->getContainer()->get('seriel_donreach.manager');
    	if (false) $DRMgr= new DonReachManager();
    	return $DRMgr->getLabelIndicator('reddit_indicator');
    }
    

    /**
     * Set scoopitIndicator
     *
     * @param integer $scoopitIndicator
     *
     * @return DonReachArticleMetrics
     */
    public function setScoopitIndicator($scoopitIndicator)
    {
        $this->scoopit_indicator = $scoopitIndicator;

        return $this;
    }

    /**
     * Get scoopitIndicator
     *
     * @return integer
     * 
     * @SER\ListeProperty("scoopit_indicator",labellogo="/images/scoopit_indicator.png", label="getNameScoopitIndicator()", sort="number",class_supp="indicator", format="none", credential="view_indicator", dbfield="scoopit_indicator")
	 * @SER\ReportingDataProperty("scoopit_indicator",labellogo="/images/scoopit_indicator.png", label="getNameScoopitIndicator()", label_short="getNameScoopitIndicator()", format="number", credential="view_indicator", moyenne=true) 
     * @SER\ReportingColRowProperty("scoopit_indicator",labellogo="/images/scoopit_indicator.png", label="getNameScoopitIndicator()", option="percent" , credential="view_indicator")
     */
    public function getScoopitIndicator()
    {
        return $this->scoopit_indicator;
    }

    public static function getNameScoopitIndicator()
    {
    	$DRMgr= ManagersManager::getManager()->getContainer()->get('seriel_donreach.manager');
    	if (false) $DRMgr= new DonReachManager();
    	return $DRMgr->getLabelIndicator('scoopit_indicator');
    }
    
    
    /**
     * Set stumbleuponIndicator
     *
     * @param integer $stumbleuponIndicator
     *
     * @return DonReachArticleMetrics
     */
    public function setStumbleuponIndicator($stumbleuponIndicator)
    {
        $this->stumbleupon_indicator = $stumbleuponIndicator;

        return $this;
    }

    /**
     * Get stumbleuponIndicator
     *
     * @return integer
     * 
     * @SER\ListeProperty("stumbleupon_indicator",labellogo="/images/stumbleupon_indicator.png", label="getNameStumbleuponIndicator()", sort="number",class_supp="indicator", format="none", credential="view_indicator", dbfield="stumbleupon_indicator")
	 * @SER\ReportingDataProperty("stumbleupon_indicator",labellogo="/images/stumbleupon_indicator.png", label="getNameStumbleuponIndicator()", label_short="getNameStumbleuponIndicator()", format="number", credential="view_indicator", moyenne=true) 
     * @SER\ReportingColRowProperty("stumbleupon_indicator",labellogo="/images/stumbleupon_indicator.png", label="getNameStumbleuponIndicator()", option="percent" , credential="view_indicator")
     */
    public function getStumbleuponIndicator()
    {
        return $this->stumbleupon_indicator;
    }

    public static function getNameStumbleuponIndicator()
    {
    	$DRMgr= ManagersManager::getManager()->getContainer()->get('seriel_donreach.manager');
    	if (false) $DRMgr= new DonReachManager();
    	return $DRMgr->getLabelIndicator('stumbleupon_indicator');
    }
    
    /**
     * Set tumblrIndicator
     *
     * @param integer $tumblrIndicator
     *
     * @return DonReachArticleMetrics
     */
    public function setTumblrIndicator($tumblrIndicator)
    {
        $this->tumblr_indicator = $tumblrIndicator;

        return $this;
    }

    /**
     * Get tumblrIndicator
     *
     * @return integer
     * 
     * @SER\ListeProperty("tumblr_indicator",labellogo="/images/tumblr_indicator.png", label="getNameTumblrIndicator()", sort="number",class_supp="indicator", format="none", credential="view_indicator", dbfield="tumblr_indicator")
	 * @SER\ReportingDataProperty("tumblr_indicator",labellogo="/images/tumblr_indicator.png", label="getNameTumblrIndicator()", label_short="getNameTumblrIndicator()", format="number", credential="view_indicator", moyenne=true) 
     * @SER\ReportingColRowProperty("tumblr_indicator",labellogo="/images/tumblr_indicator.png", label="getNameTumblrIndicator()", option="percent" , credential="view_indicator")
     */
    public function getTumblrIndicator()
    {
        return $this->tumblr_indicator;
    }

    public static function getNameTumblrIndicator()
    {
    	$DRMgr= ManagersManager::getManager()->getContainer()->get('seriel_donreach.manager');
    	if (false) $DRMgr= new DonReachManager();
    	return $DRMgr->getLabelIndicator('tumblr_indicator');
    }
    
    /**
     * Set twitterIndicator
     *
     * @param integer $twitterIndicator
     *
     * @return DonReachArticleMetrics
     */
    public function setTwitterIndicator($twitterIndicator)
    {
        $this->twitter_indicator = $twitterIndicator;

        return $this;
    }

    /**
     * Get twitterIndicator
     *
     * @return integer
     * 
     * @SER\ListeProperty("twitter_indicator",labellogo="/images/twitter_indicator.png", label="getNameTwitterIndicator()", sort="number",class_supp="indicator", format="none", credential="view_indicator", dbfield="twitter_indicator")
	 * @SER\ReportingDataProperty("twitter_indicator",labellogo="/images/twitter_indicator.png", label="getNameTwitterIndicator()", label_short="getNameTwitterIndicator()", format="number", credential="view_indicator", moyenne=true) 
     * @SER\ReportingColRowProperty("twitter_indicator",labellogo="/images/twitter_indicator.png", label="getNameTwitterIndicator()", option="percent" , credential="view_indicator")
     */
    public function getTwitterIndicator()
    {
        return $this->twitter_indicator;
    }

    public static function getNameTwitterIndicator()
    {
    	$DRMgr= ManagersManager::getManager()->getContainer()->get('seriel_donreach.manager');
    	if (false) $DRMgr= new DonReachManager();
    	return $DRMgr->getLabelIndicator('twitter_indicator');
    }
    
    /**
     * Set vkIndicator
     *
     * @param integer $vkIndicator
     *
     * @return DonReachArticleMetrics
     */
    public function setVkIndicator($vkIndicator)
    {
        $this->vk_indicator = $vkIndicator;

        return $this;
    }

    /**
     * Get vkIndicator
     *
     * @return integer
     * 
     * @SER\ListeProperty("vk_indicator",labellogo="/images/vk_indicator.png", label="getNameVkIndicator()", sort="number",class_supp="indicator", format="none", credential="view_indicator", dbfield="vk_indicator")
	 * @SER\ReportingDataProperty("vk_indicator",labellogo="/images/vk_indicator.png", label="getNameVkIndicator()", label_short="getNameVkIndicator()", format="number", credential="view_indicator", moyenne=true) 
     * @SER\ReportingColRowProperty("vk_indicator",labellogo="/images/vk_indicator.png", label="getNameVkIndicator()", option="percent" , credential="view_indicator")
     */
    public function getVkIndicator()
    {
        return $this->vk_indicator;
    }
    
    public static function getNameVkIndicator()
    {
    	$DRMgr= ManagersManager::getManager()->getContainer()->get('seriel_donreach.manager');
    	if (false) $DRMgr= new DonReachManager();
    	return $DRMgr->getLabelIndicator('vk_indicator');
    }

    /**
     * Set weiboIndicator
     *
     * @param integer $weiboIndicator
     *
     * @return DonReachArticleMetrics
     */
    public function setWeiboIndicator($weiboIndicator)
    {
        $this->weibo_indicator = $weiboIndicator;

        return $this;
    }

    /**
     * Get weiboIndicator
     *
     * @return integer
     * 
     * @SER\ListeProperty("weibo_indicator",labellogo="/images/weibo_indicator.png" , label="getNameWeiboIndicator()", sort="number",class_supp="indicator", format="none", credential="view_indicator", dbfield="weibo_indicator")
	 * @SER\ReportingDataProperty("weibo_indicator",labellogo="/images/weibo_indicator.png", label="getNameWeiboIndicator()", label_short="getNameWeiboIndicator()", format="number", credential="view_indicator", moyenne=true)
     * @SER\ReportingColRowProperty("weibo_indicator",labellogo="/images/weibo_indicator.png", label="getNameWeiboIndicator()", option="percent" , credential="view_indicator")
     */
    public function getWeiboIndicator()
    {
        return $this->weibo_indicator;
    }

    public static function getNameWeiboIndicator()
    {
    	$DRMgr= ManagersManager::getManager()->getContainer()->get('seriel_donreach.manager');
    	if (false) $DRMgr= new DonReachManager();
    	return $DRMgr->getLabelIndicator('weibo_indicator');
    }
    
    
    /**
     * Set xingIndicator
     *
     * @param integer $xingIndicator
     *
     * @return DonReachArticleMetrics
     */
    public function setXingIndicator($xingIndicator)
    {
        $this->xing_indicator = $xingIndicator;

        return $this;
    }

    /**
     * Get xingIndicator
     *
     * @return integer
     * 
     * @SER\ListeProperty("xing_indicator",labellogo="/images/xing_indicator.png", label="getNameXingIndicator()", sort="number",class_supp="indicator", format="none", credential="view_indicator", dbfield="xing_indicator")
	 * @SER\ReportingDataProperty("xing_indicator",labellogo="/images/xing_indicator.png", label="getNameXingIndicator()", label_short="getNameXingIndicator()", format="number", credential="view_indicator", moyenne=true)
     * @SER\ReportingColRowProperty("xing_indicator",labellogo="/images/xing_indicator.png", label="getNameXingIndicator()", option="percent" , credential="view_indicator")
     */
    public function getXingIndicator()
    {
        return $this->xing_indicator;
    }

    public static function getNameXingIndicator()
    {
    	$DRMgr= ManagersManager::getManager()->getContainer()->get('seriel_donreach.manager');
    	if (false) $DRMgr= new DonReachManager();
    	return $DRMgr->getLabelIndicator('xing_indicator');
    }
    
    /**
     * Set yummlyIndicator
     *
     * @param integer $yummlyIndicator
     *
     * @return DonReachArticleMetrics
     */
    public function setYummlyIndicator($yummlyIndicator)
    {
        $this->yummly_indicator = $yummlyIndicator;

        return $this;
    }

    /**
     * Get yummlyIndicator
     *
     * @return integer
     * 
     * @SER\ListeProperty("yummly_indicator",labellogo="/images/yummly_indicator.png", label="getNameYummlyIndicator()", sort="number",class_supp="indicator", format="none", credential="view_indicator", dbfield="yummly_indicator")
	 * @SER\ReportingDataProperty("yummly_indicator",labellogo="/images/yummly_indicator.png", label="getNameYummlyIndicator()", label_short="getNameYummlyIndicator()", format="number", credential="view_indicator", moyenne=true)
     * @SER\ReportingColRowProperty("yummly_indicator",labellogo="/images/yummly_indicator.png", label="getNameYummlyIndicator()", option="percent" , credential="view_indicator")
     */
    public function getYummlyIndicator()
    {
        return $this->yummly_indicator;
    }
    
    public static function getNameYummlyIndicator()
    {
    	$DRMgr= ManagersManager::getManager()->getContainer()->get('seriel_donreach.manager');
    	if (false) $DRMgr= new DonReachManager();
    	return $DRMgr->getLabelIndicator('yummly_indicator');
    }

    public function calculate() {
    	$artMetricsMgr = ManagersManager::getManager()->getContainer()->get('seriel_donreach.article_metrics_manager');
    	if (false) $artMetricsMgr = new DonReachArticleMetricsManager();
    	 
    	// calculate value on 100.
    	 
    	$moy_buffer = $artMetricsMgr->getGeneralMoy("buffer");
		$moy_facebook = $artMetricsMgr->getGeneralMoy("facebook");
		$moy_fancy = $artMetricsMgr->getGeneralMoy("fancy");
		$moy_google = $artMetricsMgr->getGeneralMoy("google");
		$moy_hackernews = $artMetricsMgr->getGeneralMoy("hackernews");
		$moy_hatena = $artMetricsMgr->getGeneralMoy("hatena");
		$moy_linkedin = $artMetricsMgr->getGeneralMoy("linkedin");
		$moy_mailru = $artMetricsMgr->getGeneralMoy("mailru");
		$moy_odnoklassniki = $artMetricsMgr->getGeneralMoy("odnoklassniki");
		$moy_pinterest = $artMetricsMgr->getGeneralMoy("pinterest");
		$moy_pocket = $artMetricsMgr->getGeneralMoy("pocket");
		$moy_reddit = $artMetricsMgr->getGeneralMoy("reddit");
		$moy_scoopit = $artMetricsMgr->getGeneralMoy("scoopit");
		$moy_stumbleupon = $artMetricsMgr->getGeneralMoy("stumbleupon");
		$moy_tumblr = $artMetricsMgr->getGeneralMoy("tumblr");
		$moy_twitter = $artMetricsMgr->getGeneralMoy("twitter");
		$moy_vk = $artMetricsMgr->getGeneralMoy("vk");
		$moy_weibo = $artMetricsMgr->getGeneralMoy("weibo");
		$moy_xing = $artMetricsMgr->getGeneralMoy("xing");
		$moy_yummly = $artMetricsMgr->getGeneralMoy("yummly");
		
    	$this->buffer_indicator = ZombieUtils::getMarkOn100($this->buffer, $moy_buffer);
		$this->facebook_indicator = ZombieUtils::getMarkOn100($this->facebook, $moy_facebook);
		$this->fancy_indicator = ZombieUtils::getMarkOn100($this->fancy, $moy_fancy);
		$this->google_indicator = ZombieUtils::getMarkOn100($this->google, $moy_google);
		$this->hackernews_indicator = ZombieUtils::getMarkOn100($this->hackernews, $moy_hackernews);
		$this->hatena_indicator = ZombieUtils::getMarkOn100($this->hatena, $moy_hatena);
		$this->linkedin_indicator = ZombieUtils::getMarkOn100($this->linkedin, $moy_linkedin);
		$this->mailru_indicator = ZombieUtils::getMarkOn100($this->mailru, $moy_mailru);
		$this->odnoklassniki_indicator = ZombieUtils::getMarkOn100($this->odnoklassniki, $moy_odnoklassniki);
		$this->pinterest_indicator = ZombieUtils::getMarkOn100($this->pinterest, $moy_pinterest);
		$this->pocket_indicator = ZombieUtils::getMarkOn100($this->pocket, $moy_pocket);
		$this->reddit_indicator = ZombieUtils::getMarkOn100($this->reddit, $moy_reddit);
		$this->scoopit_indicator = ZombieUtils::getMarkOn100($this->scoopit, $moy_scoopit);
		$this->stumbleupon_indicator = ZombieUtils::getMarkOn100($this->stumbleupon, $moy_stumbleupon);
		$this->tumblr_indicator = ZombieUtils::getMarkOn100($this->tumblr, $moy_tumblr);
		$this->twitter_indicator = ZombieUtils::getMarkOn100($this->twitter, $moy_twitter);
		$this->vk_indicator = ZombieUtils::getMarkOn100($this->vk, $moy_vk);
		$this->weibo_indicator = ZombieUtils::getMarkOn100($this->weibo, $moy_weibo);
		$this->xing_indicator = ZombieUtils::getMarkOn100($this->xing, $moy_xing);
		$this->yummly_indicator = ZombieUtils::getMarkOn100($this->yummly, $moy_yummly);
    }
    
    public function getValues() {
    	$res = array();
    	
    	$res['buffer'] = array('measure' => $this->buffer, 'indicator' => $this->buffer_indicator);
		$res['facebook'] = array('measure' => $this->facebook, 'indicator' => $this->facebook_indicator);
		$res['fancy'] = array('measure' => $this->fancy, 'indicator' => $this->fancy_indicator);
		$res['google'] = array('measure' => $this->google, 'indicator' => $this->google_indicator);
		$res['hackernews'] = array('measure' => $this->hackernews, 'indicator' => $this->hackernews_indicator);
		$res['hatena'] = array('measure' => $this->hatena, 'indicator' => $this->hatena_indicator);
		$res['linkedin'] = array('measure' => $this->linkedin, 'indicator' => $this->linkedin_indicator);
		$res['mailru'] = array('measure' => $this->mailru, 'indicator' => $this->mailru_indicator);
		$res['odnoklassniki'] = array('measure' => $this->odnoklassniki, 'indicator' => $this->odnoklassniki_indicator);
		$res['pinterest'] = array('measure' => $this->pinterest, 'indicator' => $this->pinterest_indicator);
		$res['pocket'] = array('measure' => $this->pocket, 'indicator' => $this->pocket_indicator);
		$res['reddit'] = array('measure' => $this->reddit, 'indicator' => $this->reddit_indicator);
		$res['scoopit'] = array('measure' => $this->scoopit, 'indicator' => $this->scoopit_indicator);
		$res['stumbleupon'] = array('measure' => $this->stumbleupon, 'indicator' => $this->stumbleupon_indicator);
		$res['tumblr'] = array('measure' => $this->tumblr, 'indicator' => $this->tumblr_indicator);
		$res['twitter'] = array('measure' => $this->twitter, 'indicator' => $this->twitter_indicator);
		$res['vk'] = array('measure' => $this->vk, 'indicator' => $this->vk_indicator);
		$res['weibo'] = array('measure' => $this->weibo, 'indicator' => $this->weibo_indicator);
		$res['xing'] = array('measure' => $this->xing, 'indicator' => $this->xing_indicator);
		$res['yummly'] = array('measure' => $this->yummly, 'indicator' => $this->yummly_indicator);
		
		return $res;
    }
    
    /************** ArticleMetrics Methods ***************/
    
    public function getAllMeasures() {
    	$measures = array();
    	
    	$measures['buffer'] = $this->getMeasure('buffer');
    	$measures['facebook'] = $this->getMeasure('facebook');
    	$measures['fancy'] = $this->getMeasure('fancy');
    	$measures['google'] = $this->getMeasure('google');
    	$measures['hackernews'] = $this->getMeasure('hackernews');
    	$measures['hatena'] = $this->getMeasure('hatena');
    	$measures['linkedin'] = $this->getMeasure('linkedin');
    	$measures['mailru'] = $this->getMeasure('mailru');
    	$measures['odnoklassniki'] = $this->getMeasure('odnoklassniki');
    	$measures['pinterest'] = $this->getMeasure('pinterest');
    	$measures['pocket'] = $this->getMeasure('pocket');
    	$measures['reddit'] = $this->getMeasure('reddit');
    	$measures['scoopit'] = $this->getMeasure('scoopit');
    	$measures['stumbleupon'] = $this->getMeasure('stumbleupon');
    	$measures['tumblr'] = $this->getMeasure('tumblr');
    	$measures['twitter'] = $this->getMeasure('twitter');
    	$measures['vk'] = $this->getMeasure('vk');
    	$measures['weibo'] = $this->getMeasure('weibo');
    	$measures['xing'] = $this->getMeasure('xing');
    	$measures['yummly'] = $this->getMeasure('yummly');
    	
    	return $measures;
    }
    public function getAllIndicators() {
    	$indicators = array();
    	
    	$indicators['buffer'] = $this->getIndicator('buffer');
    	$indicators['facebook'] = $this->getIndicator('facebook');
    	$indicators['fancy'] = $this->getIndicator('fancy');
    	$indicators['google'] = $this->getIndicator('google');
    	$indicators['hackernews'] = $this->getIndicator('hackernews');
    	$indicators['hatena'] = $this->getIndicator('hatena');
    	$indicators['linkedin'] = $this->getIndicator('linkedin');
    	$indicators['mailru'] = $this->getIndicator('mailru');
    	$indicators['odnoklassniki'] = $this->getIndicator('odnoklassniki');
    	$indicators['pinterest'] = $this->getIndicator('pinterest');
    	$indicators['pocket'] = $this->getIndicator('pocket');
    	$indicators['reddit'] = $this->getIndicator('reddit');
    	$indicators['scoopit'] = $this->getIndicator('scoopit');
    	$indicators['stumbleupon'] = $this->getIndicator('stumbleupon');
    	$indicators['tumblr'] = $this->getIndicator('tumblr');
    	$indicators['twitter'] = $this->getIndicator('twitter');
    	$indicators['vk'] = $this->getIndicator('vk');
    	$indicators['weibo'] = $this->getIndicator('weibo');
    	$indicators['xing'] = $this->getIndicator('xing');
    	$indicators['yummly'] = $this->getIndicator('yummly');
    	
    	return $indicators;
    }
    public static function getAllIdIndicators() {
    	$authChecker = SymfonyUtils::getAuthorizationChecker();
    	$IDindicators = array();
    	$reader = ManagersManager::getManager()->getContainer()->get('annotation_reader');
    	$converter = new SerielListePropertyConverter($reader);
    	$fields = $converter->convert('Seriel\DonReachBundle\Entity\DonReachArticleMetrics');
    	if ($fields) {
    		foreach ($fields as $field) {
    			if ($field->getClassSupp() == 'indicator' ) {
    				$label = $field->getLabel();
    				if (substr($label, -2) == '()') {
    					eval('$label = self::'.$label.';');
    				}
    				//security
    				$cred = $field->getCredential();
    				// if user not have acces, not add in list 
    				if ( ((isset($cred) ) && ($authChecker->isGranted('ANY_RIGHT_ON[Seriel\DonReachBundle\Entity\DonReachArticleMetrics >> '.$cred.']'))) OR  ( $authChecker->isGranted($cred))  OR  ( ! isset($cred))) {
    					$IDindicators[$field->getDbfield()] = $label;
    				}
    				
    			}
    		}
    	}
    	return $IDindicators;
    }
  
    public static function getAllLogoIndicators() {
    	$IDindicators = array();
    	$reader = ManagersManager::getManager()->getContainer()->get('annotation_reader');
    	$converter = new SerielListePropertyConverter($reader);
    	$fields = $converter->convert('Seriel\DonReachBundle\Entity\DonReachArticleMetrics');
    	if ($fields) {
    		foreach ($fields as $field) {
    			if ($field->getClassSupp() == 'indicator' ) {
    				$labellogo = $field->getLabellogo();
    				if (substr($labellogo, -2) == '()') {
    					eval('$labellogo = self::'.$labellogo.';');
    				}
    				$IDindicators[$field->getDbfield()] = $labellogo;
    			}
    		}
    	}
    	return $IDindicators;
    }
    
    public function getMeasure($measure) {
    	$measure= trim(strtolower($measure));
    	
    	if (!$measure) return null;
    	
    	if ($measure == 'buffer') return $this->buffer;
    	if ($measure == 'facebook') return $this->facebook;
    	if ($measure == 'fancy') return $this->fancy;
    	if ($measure == 'google') return $this->google;
    	if ($measure == 'hackernews') return $this->hackernews;
    	if ($measure == 'hatena') return $this->hatena;
    	if ($measure == 'linkedin') return $this->linkedin;
    	if ($measure == 'mailru') return $this->mailru;
    	if ($measure == 'odnoklassniki') return $this->odnoklassniki;
    	if ($measure == 'pinterest') return $this->pinterest;
    	if ($measure == 'pocket') return $this->pocket;
    	if ($measure == 'reddit') return $this->reddit;
    	if ($measure == 'scoopit') return $this->scoopit;
    	if ($measure == 'stumbleupon') return $this->stumbleupon;
    	if ($measure == 'tumblr') return $this->tumblr;
    	if ($measure == 'twitter') return $this->twitter;
    	if ($measure == 'vk') return $this->vk;
    	if ($measure == 'weibo') return $this->weibo;
    	if ($measure == 'xing') return $this->xing;
    	if ($measure == 'yummly') return $this->yummly;
    	
    	return null;
    }
    public function getIndicator($indicator) {
    	$indicator = trim(strtolower($indicator));
    	
    	if (!$indicator) return null;
    	
    	if ($indicator == 'buffer') return $this->buffer_indicator;
    	if ($indicator == 'facebook') return $this->facebook_indicator;
    	if ($indicator == 'fancy') return $this->fancy_indicator;
    	if ($indicator == 'google') return $this->google_indicator;
    	if ($indicator == 'hackernews') return $this->hackernews_indicator;
    	if ($indicator == 'hatena') return $this->hatena_indicator;
    	if ($indicator == 'linkedin') return $this->linkedin_indicator;
    	if ($indicator == 'mailru') return $this->mailru_indicator;
    	if ($indicator == 'odnoklassniki') return $this->odnoklassniki_indicator;
    	if ($indicator == 'pinterest') return $this->pinterest_indicator;
    	if ($indicator == 'pocket') return $this->pocket_indicator;
    	if ($indicator == 'reddit') return $this->reddit_indicator;
    	if ($indicator == 'scoopit') return $this->scoopit_indicator;
    	if ($indicator == 'stumbleupon') return $this->stumbleupon_indicator;
    	if ($indicator == 'tumblr') return $this->tumblr_indicator;
    	if ($indicator == 'twitter') return $this->twitter_indicator;
    	if ($indicator == 'vk') return $this->vk_indicator;
    	if ($indicator == 'weibo') return $this->weibo_indicator;
    	if ($indicator == 'xing') return $this->xing_indicator;
    	if ($indicator == 'yummly') return $this->yummly_indicator;
    	
    	return null;
    }
}
