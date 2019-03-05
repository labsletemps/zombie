<?php

namespace Seriel\GoogleAnalyticsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Annotation as SER;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;
use Seriel\AppliToolboxBundle\Managers\ManagersManager;
use ZombieBundle\API\Entity\ArticleMetrics;
use ZombieBundle\Utils\ZombieUtils;
use Seriel\AppliToolboxBundle\Annotation\SerielListePropertyConverter;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use Seriel\GoogleAnalyticsBundle\Managers\GoogleAnalyticsManager;

/**
 * GoogleAnalyticsArticleMetrics
 *
 * @ORM\Entity
 * @ORM\Table(name="google_analytics_article_metrics",options={"engine"="MyISAM"})
 */
class GoogleAnalyticsArticleMetrics extends RootObject implements ArticleMetrics, Listable
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
     * @ORM\OneToOne(targetEntity="ZombieBundle\Entity\News\Article")
     * @ORM\JoinColumn(name="article_id", referencedColumnName="id")
     **/
    private $article;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $date_calcul;
    
    /**
     * @var int
     *
     * @ORM\Column(name="readtime_measure", type="integer")
     */
    private $readtimeMeasure;
    
    /**
     * @var int
     *
     * @ORM\Column(name="readtime_subscriber_measure", type="integer")
     */
    private $readtimeSubscriberMeasure;
    
    /**
     * @var int
     *
     * @ORM\Column(name="readtime_visitor_measure", type="integer")
     */
    private $readtimeVisitorMeasure;

    /**
     * @var int
     *
     * @ORM\Column(name="pageview_measure", type="integer")
     */
    private $pageviewMeasure;

    /**
     * @var int
     *
     * @ORM\Column(name="pageview_subscriber_measure", type="integer")
     */
    private $pageviewSubscriberMeasure;
    
    /**
     * @var int
     *
     * @ORM\Column(name="pageview_visitor_measure", type="integer")
     */
    private $pageviewVisitorMeasure;

    /**
     * @var int
     *
     * @ORM\Column(name="uniquepageview_measure", type="integer")
     */
    private $uniquepageviewMeasure;
    
    /**
     * @var int
     *
     * @ORM\Column(name="uniquepageview_subscriber_measure", type="integer")
     */
    private $uniquepageviewSubscriberMeasure;
    
    /**
     * @var int
     *
     * @ORM\Column(name="uniquepageview_visitor_measure", type="integer")
     */
    private $uniquepageviewVisitorMeasure;
    
    /**
     * @var int
     *
     * @ORM\Column(name="subscription_measure", type="integer")
     */
    private $subscriptionMeasure;

    /**
     * @var int
     *
     * @ORM\Column(name="entrance_measure", type="integer")
     */
    private $entranceMeasure;
    
    /**
     * @var int
     *
     * @ORM\Column(name="entrance_subscriber_measure", type="integer")
     */
    private $entranceSubscriberMeasure;
    
    /**
     * @var int
     *
     * @ORM\Column(name="entrance_visitor_measure", type="integer")
     */
    private $entranceVisitorMeasure;

    /**
     * @var int
     *
     * @ORM\Column(name="completionread_indicator", type="float")
     */
    private $completionreadIndicator;
    
    /**
     * @var int
     *
     * @ORM\Column(name="completionread_subscriber_indicator", type="float")
     */
    private $completionreadSubscriberIndicator;
    
    /**
     * @var int
     *
     * @ORM\Column(name="completionread_visitor_indicator", type="float")
     */
    private $completionreadVisitorIndicator;

    /**
     * @var int
     *
     * @ORM\Column(name="entrance_indicator", type="float")
     */
    private $entranceIndicator;

    /**
     * @var int
     *
     * @ORM\Column(name="subscription_indicator", type="float")
     */
    private $subscriptionIndicator;

    /**
     * @var int
     *
     * @ORM\Column(name="pageview_indicator", type="float")
     */
    private $pageviewIndicator;
    
    /**
     * @var int
     *
     * @ORM\Column(name="attention_indicator", type="float")
     */
    private $attentionIndicator;

    /**
     * @var int
     *
     * @ORM\Column(name="audience_indicator", type="float")
     */
    private $audienceIndicator;
    
    /**
     * @var int
     *
     * @ORM\Column(name="monetisation_indicator", type="float")
     */
    private $monetisationIndicator;
    
    /**
     * @var int
     *
     * @ORM\Column(name="abonne_like_indicator", type="float")
     */
    private $abonneLikeIndicator;
    
    /**
     * @var int
     *
     * @ORM\Column(name="visiteur_like_indicator", type="float")
     */
    private $visiteurLikeIndicator;
    
    /**
     * @var int
     *
     * @ORM\Column(name="exitpage_measure", type="integer")
     */
    private $exitpageMeasure;
    
    /**
     * @var int
     *
     * @ORM\Column(name="bounce_indicator", type="float")
     */
    private $bounceIndicator;
    
    public function __construct()
    {
    	parent::__construct();
    	//initialize
    	$this->setReadtimeMeasure(0);
    	$this->setReadtimeSubscriberMeasure(0);
    	$this->setReadtimeVisitorMeasure(0);
    	$this->setPageviewMeasure(0);
    	$this->setPageviewSubscriberMeasure(0);
    	$this->setPageviewVisitorMeasure(0);
    	$this->setUniquepageviewMeasure(0);
    	$this->setUniquepageviewSubscriberMeasure(0);
    	$this->setUniquepageviewVisitorMeasure(0);
    	$this->setSubscriptionMeasure(0);
    	$this->setEntranceMeasure(0);
    	$this->setEntranceSubscriberMeasure(0);
    	$this->setEntranceVisitorMeasure(0);
    	$this->setExitpageMeasure(0);
    	$this->setMonetisationIndicator(0);
    	$this->setAttentionIndicator(0); 
    	$this->setAbonneLikeIndicator(0); 
    	$this->setVisiteurLikeIndicator(0);
    	$this->setEntranceIndicator(0);
    	$this->setSubscriptionIndicator(0);
    	$this->setPageviewIndicator(0);
    	$this->setCompletionreadIndicator(0);
    	$this->setCompletionreadSubscriberIndicator(0);
    	$this->setCompletionreadVisitorIndicator(0);
    	$this->setAudienceIndicator(0);
    	$this->setBounceIndicator(0);
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
     * Set article
     *
     * @param \ZombieBundle\Entity\News\Article $article
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setArticle(\ZombieBundle\Entity\News\Article $article)
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
     * Set dateCalcul
     *
     * @param \DateTime $dateCalcul
     *
     * @return GoogleAnalyticsArticleMetrics
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
     * Set readtimeMeasure
     *
     * @param integer $readtimeMeasure
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setReadtimeMeasure($readtimeMeasure)
    {
    	if ($readtimeMeasure== null) $readtimeMeasure= 0;
        $this->readtimeMeasure = $readtimeMeasure;

        return $this;
    }

    /**
     * Get readtimeMeasure
     *
     * @SER\ListeProperty("readtimeMeasure", label="read time measure", sort="number", format="none", credential="view_measure", dbfield="readtimeMeasure")
	 * @SER\ReportingDataProperty("readtimeMeasure", label="read time measure", label_short="readtime", credential="view_measure", format="number")
     * @return int
     */
    public function getReadtimeMeasure()
    {
        return $this->readtimeMeasure;
    }

    /**
     * Set pageviewMeasure
     *
     * @param integer $pageviewMeasure
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setPageviewMeasure($pageviewMeasure)
    {
    	if ($pageviewMeasure == null) $pageviewMeasure = 0;
        $this->pageviewMeasure = $pageviewMeasure;

        return $this;
    }

    /**
     * Get pageviewMeasure
     *
     * @SER\ListeProperty("pageviewMeasure", label="page view measure", sort="number", format="none", credential="view_measure", dbfield="pageviewMeasure")
	 * @SER\ReportingDataProperty("pageviewMeasure", label="page view measure", label_short="pageview", credential="view_measure", format="number")
     * @return int
     */
    public function getPageviewMeasure()
    {
        return $this->pageviewMeasure;
    }

    /**
     * Set uniquepageviewMeasure
     *
     * @param integer $uniquepageviewMeasure
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setUniquepageviewMeasure($uniquepageviewMeasure)
    {
    	if ($uniquepageviewMeasure== null) $uniquepageviewMeasure= 0;
    	$this->uniquepageviewMeasure= $uniquepageviewMeasure;
    	
    	return $this;
    }
    
    /**
     * Get uniquepageviewMeasure
     *
     * @SER\ListeProperty("uniquepageviewMeasure", label="unique page view measure", sort="number", format="none", credential="view_measure", dbfield="uniquepageviewMeasure")
	 * @SER\ReportingDataProperty("uniquepageviewMeasure", label="unique page view measure", label_short="uniquepageview", credential="view_measure", format="number")
     * @return int
     */
    public function getUniquepageviewMeasure()
    {
    	return $this->uniquepageviewMeasure;
    }
    
    
    /**
     * Set subscriptionMeasure
     *
     * @param integer $subscriptionMeasure
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setSubscriptionMeasure($subscriptionMeasure)
    {
    	
    	if ($subscriptionMeasure== null) $subscriptionMeasure= 0;
        $this->subscriptionMeasure = $subscriptionMeasure;

        return $this;
    }

    /**
     * Get subscriptionMeasure
     *
     * @SER\ListeProperty("subscriptionMeasure", label="subscription measure", sort="number", format="none", credential="view_measure", dbfield="subscriptionMeasure")
	 * @SER\ReportingDataProperty("subscriptionMeasure", label="subscription measure", label_short="subscription", credential="view_measure", format="number")
     * @return int
     */
    public function getSubscriptionMeasure()
    {
        return $this->subscriptionMeasure;
    }

    /**
     * Set entranceMeasure
     *
     * @param integer $entranceMeasure
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setEntranceMeasure($entranceMeasure)
    {
    	if ($entranceMeasure== null) $entranceMeasure= 0;
        $this->entranceMeasure = $entranceMeasure;

        return $this;
    }

    /**
     * Get entranceMeasure
     *
     * @SER\ListeProperty("entranceMeasure", label="entrance measure", sort="number", format="none", credential="view_measure", dbfield="entranceMeasure")
	 * @SER\ReportingDataProperty("entranceMeasure", label="entrance measure", label_short="entrance", credential="view_measure", format="number")
     * @return int
     */
    public function getEntranceMeasure()
    {
        return $this->entranceMeasure;
    }
    
    /**
     * Set exitpageMeasure
     *
     * @param integer $exitpageMeasure
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setExitpageMeasure($exitpageMeasure)
    {
    	if ($exitpageMeasure== null) $exitpageMeasure= 0;
    	$this->exitpageMeasure= $exitpageMeasure;
    	
    	return $this;
    }
    
    /**
     * Get exitpageMeasure
     *
     * @SER\ListeProperty("exitpageMeasure", label="exitpage measure", sort="number", format="none", credential="view_measure", dbfield="exitpageMeasure")
     * @SER\ReportingDataProperty("exitpageMeasure", label="exitpage measure", label_short="exitpage", credential="view_measure", format="number")
     * @return int
     */
    public function getExitpageMeasure()
    {
    	return $this->exitpageMeasure;
    }
    

    /**
     * Set completionreadIndicator
     *
     * @param integer $completionreadIndicator
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setCompletionreadIndicator($completionreadIndicator)
    {
        $this->completionreadIndicator = $completionreadIndicator;

        return $this;
    }

    /**
     * Get completionreadIndicator
     *
     * @SER\ListeProperty("completionreadIndicator",labellogo="/images/completionreadIndicator.png", label="getNameCompletionreadIndicator()", sort="number",class_supp="indicator", format="number", credential="view_indicator", dbfield="completionreadIndicator")
	 * @SER\ReportingDataProperty("completionreadIndicator",labellogo="/images/completionreadIndicator.png", label="getNameCompletionreadIndicator()", label_short="getNameCompletionreadIndicator()", format="number", credential="view_indicator", moyenne=true) 
     * @SER\ReportingColRowProperty("completionreadIndicator",labellogo="/images/completionreadIndicator.png", label="getNameCompletionreadIndicator()", credential="view_indicator", option="percent")
     * @return int
     */
    public function getCompletionreadIndicator()
    {
        return $this->completionreadIndicator;
    }
    
    public static function getNameCompletionreadIndicator()
    {
    	$GAMgr= ManagersManager::getManager()->getContainer()->get('seriel_google_analytics.manager');
    	if (false) $GAMgr= new GoogleAnalyticsManager();
    	return $GAMgr->getLabelIndicator('completionreadIndicator');
    }
    
    
    /**
     * Set completionreadSubscriberIndicator
     *
     * @param integer $completionreadSubscriberIndicator
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setCompletionreadSubscriberIndicator($completionreadSubscriberIndicator)
    {
    	$this->completionreadSubscriberIndicator= $completionreadSubscriberIndicator;
    	
    	return $this;
    }
    
    /**
     * Get completionreadSubscriberIndicator
     *
     * @SER\ListeProperty("completionreadSubscriberIndicator",labellogo="/images/completionreadSubscriberIndicator.png", label="getNameCompletionreadSubscriberIndicator()", sort="number",class_supp="indicator", format="number", credential="view_indicator", dbfield="completionreadSubscriberIndicator")
	 * @SER\ReportingDataProperty("completionreadSubscriberIndicator",labellogo="/images/completionreadSubscriberIndicator.png", label="getNameCompletionreadSubscriberIndicator()", label_short="getNameCompletionreadSubscriberIndicator()", format="number", credential="view_indicator", moyenne=true)
     * @SER\ReportingColRowProperty("completionreadSubscriberIndicator",labellogo="/images/completionreadSubscriberIndicator.png", label="getNameCompletionreadSubscriberIndicator()", credential="view_indicator", option="percent")
     * @return int
     */
    public function getCompletionreadSubscriberIndicator()
    {
    	return $this->completionreadSubscriberIndicator;
    }
    
    public static function getNameCompletionreadSubscriberIndicator()
    {
    	$GAMgr= ManagersManager::getManager()->getContainer()->get('seriel_google_analytics.manager');
    	if (false) $GAMgr= new GoogleAnalyticsManager();
    	return $GAMgr->getLabelIndicator('completionreadSubscriberIndicator');
    }
    
    /**
     * Set completionreadVisitorIndicator
     *
     * @param integer $completionreadVisitorIndicator
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setCompletionreadVisitorIndicator($completionreadVisitorIndicator)
    {
    	$this->completionreadVisitorIndicator= $completionreadVisitorIndicator;
    	
    	return $this;
    }
    
    /**
     * Get completionreadVisitorIndicator
     *
     * @SER\ListeProperty("completionreadVisitorIndicator",labellogo="/images/completionreadVisitorIndicator.png", label="getNameCompletionreadVisitorIndicator()", sort="number",class_supp="indicator", format="number", credential="view_indicator", dbfield="completionreadVisitorIndicator")
	 * @SER\ReportingDataProperty("completionreadVisitorIndicator",labellogo="/images/completionreadVisitorIndicator.png", label="getNameCompletionreadVisitorIndicator()", label_short="getNameCompletionreadVisitorIndicator()", format="number", credential="view_indicator", moyenne=true)
     * @SER\ReportingColRowProperty("completionreadVisitorIndicator",labellogo="/images/completionreadVisitorIndicator.png", label="getNameCompletionreadVisitorIndicator()", credential="view_indicator", option="percent")
     * @return int
     */
    public function getCompletionreadVisitorIndicator()
    {
    	return $this->completionreadVisitorIndicator;
    }
   
    public static function getNameCompletionreadVisitorIndicator()
    {
    	$GAMgr= ManagersManager::getManager()->getContainer()->get('seriel_google_analytics.manager');
    	if (false) $GAMgr= new GoogleAnalyticsManager();
    	return $GAMgr->getLabelIndicator('completionreadVisitorIndicator');
    }
    

    /**
     * Set entranceIndicator
     *
     * @param integer $entranceIndicator
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setEntranceIndicator($entranceIndicator)
    {
        $this->entranceIndicator = $entranceIndicator;

        return $this;
    }

    /**
     * Get entranceIndicator
     *
     * @SER\ListeProperty("entranceIndicator",labellogo="/images/entranceIndicator.png", label="getNameEntranceIndicator()", sort="number",class_supp="indicator", format="number", credential="view_indicator", dbfield="entranceIndicator")
	 * @SER\ReportingDataProperty("entranceIndicator",labellogo="/images/entranceIndicator.png", label="getNameEntranceIndicator()", label_short="getNameEntranceIndicator()", format="number", credential="view_indicator", moyenne=true)
     * @SER\ReportingColRowProperty("entranceIndicator",labellogo="/images/entranceIndicator.png", label="getNameEntranceIndicator()", credential="view_indicator", option="percent")
     * @return int
     */
    public function getEntranceIndicator()
    {
        return $this->entranceIndicator;
    }

    public static function getNameEntranceIndicator()
    {
    	$GAMgr= ManagersManager::getManager()->getContainer()->get('seriel_google_analytics.manager');
    	if (false) $GAMgr= new GoogleAnalyticsManager();
    	return $GAMgr->getLabelIndicator('entranceIndicator');
    }
    
    /**
     * Set bounceIndicator
     *
     * @param integer $bounceIndicator
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setBounceIndicator($bounceIndicator)
    {
    	$this->bounceIndicator= $bounceIndicator;
    	
    	return $this;
    }
    
    /**
     * Get entranceIndicator
     *
     * @SER\ListeProperty("bounceIndicator",labellogo="/images/bounceIndicator.png", label="getNameBounceIndicator()", sort="number",class_supp="indicator", format="number", credential="view_indicator_temps", dbfield="bounceIndicator")
     * @SER\ReportingDataProperty("bounceIndicator",labellogo="/images/bounceIndicator.png", label="getNameBounceIndicator()", label_short="getNameBounceIndicator()", format="number", credential="view_indicator_temps", moyenne=true)
     * @SER\ReportingColRowProperty("bounceIndicator",labellogo="/images/bounceIndicator.png", label="getNameBounceIndicator()", credential="view_indicator_temps", option="percent")
     * @return int
     */
    public function getBounceIndicator()
    {
    	return $this->bounceIndicator;
    }
    
    public static function getNameBounceIndicator()
    {
    	$GAMgr= ManagersManager::getManager()->getContainer()->get('seriel_google_analytics.manager');
    	if (false) $GAMgr= new GoogleAnalyticsManager();
    	return $GAMgr->getLabelIndicator('bounceIndicator');
    }
    
    
    //bounceIndicator
    
    /**
     * Set subscriptionIndicator
     *
     * @param integer $subscriptionIndicator
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setSubscriptionIndicator($subscriptionIndicator)
    {
        $this->subscriptionIndicator = $subscriptionIndicator;

        return $this;
    }

    /**
     * Get subscriptionIndicator
     *
     * @SER\ListeProperty("subscriptionIndicator",labellogo="/images/subscriptionIndicator.png", label="getNameSubscriptionIndicator()", sort="number",class_supp="indicator", format="number", credential="view_indicator", dbfield="subscriptionIndicator")
	 * @SER\ReportingDataProperty("subscriptionIndicator",labellogo="/images/subscriptionIndicator.png", label="getNameSubscriptionIndicator()", label_short="getNameSubscriptionIndicator()", format="number", credential="view_indicator", moyenne=true)
     * @SER\ReportingColRowProperty("subscriptionIndicator",labellogo="/images/subscriptionIndicator.png", label="getNameSubscriptionIndicator()", credential="view_indicator", option="percent")
     * @return int
     */
    public function getSubscriptionIndicator()
    {
        return $this->subscriptionIndicator;
    }
    
    public static function getNameSubscriptionIndicator()
    {
    	$GAMgr= ManagersManager::getManager()->getContainer()->get('seriel_google_analytics.manager');
    	if (false) $GAMgr= new GoogleAnalyticsManager();
    	return $GAMgr->getLabelIndicator('subscriptionIndicator');
    }

    /**
     * Set pageviewIndicator
     *
     * @param integer $pageviewIndicator
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setPageviewIndicator($pageviewIndicator)
    {
        $this->pageviewIndicator = $pageviewIndicator;

        return $this;
    }

    /**
     * Get pageviewIndicator
     *
     * @SER\ListeProperty("pageviewIndicator",labellogo="/images/pageviewIndicator.png", label="getNamePageviewIndicator()", sort="number", format="number",class_supp="indicator", credential="view_indicator", dbfield="pageviewIndicator")
	 * @SER\ReportingDataProperty("pageviewIndicator",labellogo="/images/pageviewIndicator.png", label="getNamePageviewIndicator()", label_short="getNamePageviewIndicator()", format="number", credential="view_indicator", moyenne=true)
     * @SER\ReportingColRowProperty("pageviewIndicator",labellogo="/images/pageviewIndicator.png", label="getNamePageviewIndicator()", credential="view_indicator", option="percent")
     * @return int
     */
    public function getPageviewIndicator()
    {
        return $this->pageviewIndicator;
    }

    public static function getNamePageviewIndicator()
    {
    	$GAMgr= ManagersManager::getManager()->getContainer()->get('seriel_google_analytics.manager');
    	if (false) $GAMgr= new GoogleAnalyticsManager();
    	return $GAMgr->getLabelIndicator('pageviewIndicator');
    }
    
    /**
     * Set readtimeSubscriberMeasure
     *
     * @param integer $readtimeSubscriberMeasure
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setReadtimeSubscriberMeasure($readtimeSubscriberMeasure)
    {
    	if ($readtimeSubscriberMeasure== null) $readtimeSubscriberMeasure= 0;
        $this->readtimeSubscriberMeasure = $readtimeSubscriberMeasure;

        return $this;
    }

    /**
     * Get readtimeSubscriberMeasure
     * @SER\ListeProperty("readtimeSubscriberMeasure", label="read time subscriber measure", sort="none", format="none", credential="view_measure", dbfield="readtimeSubscriberMeasure")
	 * @SER\ReportingDataProperty("readtimeSubscriberMeasure", label="read time subscriber measure", label_short="readtime s.", credential="view_measure", format="number")
     * @return integer
     */
    public function getReadtimeSubscriberMeasure()
    {
        return $this->readtimeSubscriberMeasure;
    }

    /**
     * Set readtimeVisitorMeasure
     *
     * @param integer $readtimeVisitorMeasure
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setReadtimeVisitorMeasure($readtimeVisitorMeasure)
    {
    	if ($readtimeVisitorMeasure== null) $readtimeVisitorMeasure= 0;
        $this->readtimeVisitorMeasure = $readtimeVisitorMeasure;

        return $this;
    }

    /**
     * Get readtimeVisitorMeasure
     * @SER\ListeProperty("readtimeVisitorMeasure", label="read time visitor measure", sort="number", format="none", credential="view_measure", dbfield="readtimeVisitorMeasure")
	 * @SER\ReportingDataProperty("readtimeVisitorMeasure", label="read time visitor measure", label_short="readtime v.", credential="view_measure", format="number")
     * @return integer
     */
    public function getReadtimeVisitorMeasure()
    {
        return $this->readtimeVisitorMeasure;
    }

    /**
     * Set pageviewSubscriberMeasure
     *
     * @param integer $pageviewSubscriberMeasure
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setPageviewSubscriberMeasure($pageviewSubscriberMeasure)
    {
    	if ($pageviewSubscriberMeasure== null) $pageviewSubscriberMeasure= 0;
        $this->pageviewSubscriberMeasure = $pageviewSubscriberMeasure;

        return $this;
    }

    /**
     * Get pageviewSubscriberMeasure
     * @SER\ListeProperty("pageviewSubscriberMeasure", label="page view subscriber measure", sort="number", format="none", credential="view_measure", dbfield="pageviewSubscriberMeasure")
	 * @SER\ReportingDataProperty("pageviewSubscriberMeasure", label="page view subscriber measure", label_short="pageview s.", credential="view_measure", format="number")
     * @return integer
     */
    public function getPageviewSubscriberMeasure()
    {
        return $this->pageviewSubscriberMeasure;
    }

    /**
     * Set pageviewVisitorMeasure
     *
     * @param integer $pageviewVisitorMeasure
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setPageviewVisitorMeasure($pageviewVisitorMeasure)
    {
    	if ($pageviewVisitorMeasure== null) $pageviewVisitorMeasure= 0;
        $this->pageviewVisitorMeasure = $pageviewVisitorMeasure;

        return $this;
    }

    /**
     * Get pageviewVisitorMeasure
     * @SER\ListeProperty("pageviewVisitorMeasure", label="page view visitor measure", sort="number", format="none", credential="view_measure", dbfield="pageviewVisitorMeasure")
	 * @SER\ReportingDataProperty("pageviewVisitorMeasure", label="page view visitor measure", label_short="pageview v.", credential="view_measure", format="number")
     * @return integer
     */
    public function getPageviewVisitorMeasure()
    {
        return $this->pageviewVisitorMeasure;
    }

    /**
     * Set uniquepageviewSubscriberMeasure
     *
     * @param integer $uniquepageviewSubscriberMeasure
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setUniquepageviewSubscriberMeasure($uniquepageviewSubscriberMeasure)
    {
    	if ($uniquepageviewSubscriberMeasure== null) $uniquepageviewSubscriberMeasure= 0;
        $this->uniquepageviewSubscriberMeasure = $uniquepageviewSubscriberMeasure;

        return $this;
    }

    /**
     * Get uniquepageviewSubscriberMeasure
     * @SER\ListeProperty("uniquepageviewSubscriberMeasure", label="unique page view subscriber measure", sort="number", format="none", credential="view_measure", dbfield="uniquepageviewSubscriberMeasure")
	 * @SER\ReportingDataProperty("uniquepageviewSubscriberMeasure", label="unique page view subscriber measure", label_short="uniquepageview s.", credential="view_measure", format="number")
     * @return integer
     */
    public function getUniquepageviewSubscriberMeasure()
    {
        return $this->uniquepageviewSubscriberMeasure;
    }

    /**
     * Set uniquepageviewVisitorMeasure
     *
     * @param integer $uniquepageviewVisitorMeasure
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setUniquepageviewVisitorMeasure($uniquepageviewVisitorMeasure)
    {
    	if ($uniquepageviewVisitorMeasure== null) $uniquepageviewVisitorMeasure= 0;
        $this->uniquepageviewVisitorMeasure = $uniquepageviewVisitorMeasure;

        return $this;
    }

    /**
     * Get uniquepageviewVisitorMeasure
     * @SER\ListeProperty("uniquepageviewVisitorMeasure", label="unique page view visitor measure", sort="number", format="none", credential="view_measure", dbfield="uniquepageviewVisitorMeasure")
	 * @SER\ReportingDataProperty("uniquepageviewVisitorMeasure", label="unique page view visitor measure", label_short="uniquepageview v.", credential="view_measure", format="number")   
     * @return integer
     */
    public function getUniquepageviewVisitorMeasure()
    {
        return $this->uniquepageviewVisitorMeasure;
    }

    /**
     * Set entranceSubscriberMeasure
     *
     * @param integer $entranceSubscriberMeasure
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setEntranceSubscriberMeasure($entranceSubscriberMeasure)
    {
    	if ($entranceSubscriberMeasure== null) $entranceSubscriberMeasure= 0;
        $this->entranceSubscriberMeasure = $entranceSubscriberMeasure;

        return $this;
    }

    /**
     * Get entranceSubscriberMeasure
     * @SER\ListeProperty("entranceSubscriberMeasure", label="entrance subscriber measure", sort="number", format="none", credential="view_measure", dbfield="entranceSubscriberMeasure")
	 * @SER\ReportingDataProperty("entranceSubscriberMeasure", label="entrance subscriber measure", label_short="entrance s.", credential="view_measure", format="number") 
     * @return integer
     */
    public function getEntranceSubscriberMeasure()
    {
        return $this->entranceSubscriberMeasure;
    }

    /**
     * Set entranceVisitorMeasure
     *
     * @param integer $entranceVisitorMeasure
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setEntranceVisitorMeasure($entranceVisitorMeasure)
    {
    	if ($entranceVisitorMeasure== null) $entranceVisitorMeasure= 0;
        $this->entranceVisitorMeasure = $entranceVisitorMeasure;

        return $this;
    }

    /**
     * Get entranceVisitorMeasure
     * @SER\ListeProperty("entranceVisitorMeasure", label="entrance visitor measure", sort="number", format="none", credential="view_measure", dbfield="entranceVisitorMeasure")
	 * @SER\ReportingDataProperty("entranceVisitorMeasure", label="entrance visitor measure", label_short="entrance v.", credential="view_measure", format="number")
     * @return integer
     */
    public function getEntranceVisitorMeasure()
    {
        return $this->entranceVisitorMeasure;
    }
    
    
    /**
     * Set attentionIndicator
     *
     * @param integer $attentionIndicator
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setAttentionIndicator($attentionIndicator)
    {
    	$this->attentionIndicator = $attentionIndicator;
    	
    	return $this;
    }
    
    /**
     * Get attentionIndicator
     * @SER\ListeProperty("attentionIndicator",labellogo="/images/attentionIndicator.png", label="getNameAttentionIndicator()", sort="number", format="number",class_supp="indicator", credential="view_indicator_temps", dbfield="attentionIndicator")
	 * @SER\ReportingDataProperty("attentionIndicator",labellogo="/images/attentionIndicator.png", label="getNameAttentionIndicator()", label_short="getNameAttentionIndicator()", format="number", credential="view_indicator_temps", moyenne=true)
     * @SER\ReportingColRowProperty("attentionIndicator",labellogo="/images/attentionIndicator.png", label="getNameAttentionIndicator()", option="percent" , credential="view_indicator_temps")
     * @return integer
     */
    public function getAttentionIndicator()
    {
    	return $this->attentionIndicator;
    }
    
    public static function getNameAttentionIndicator()
    {
    	$GAMgr= ManagersManager::getManager()->getContainer()->get('seriel_google_analytics.manager');
    	if (false) $GAMgr= new GoogleAnalyticsManager();
    	return $GAMgr->getLabelIndicator('attentionIndicator');
    }
    
    /**
     * Set audienceIndicator
     *
     * @param integer $audienceIndicator
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setAudienceIndicator($audienceIndicator)
    {
    	$this->audienceIndicator = $audienceIndicator;
    	
    	return $this;
    }
    
    /**
     * Get audienceIndicator
     * @SER\ListeProperty("audienceIndicator",labellogo="/images/audienceIndicator.png", label="getNameAudienceIndicator()", sort="number", format="number",class_supp="indicator", credential="view_indicator_temps", dbfield="audienceIndicator")
	 * @SER\ReportingDataProperty("audienceIndicator",labellogo="/images/audienceIndicator.png", label="getNameAudienceIndicator()", label_short="getNameAudienceIndicator()", format="number", credential="view_indicator_temps", moyenne=true)
     * @SER\ReportingColRowProperty("audienceIndicator",labellogo="/images/audienceIndicator.png", label="getNameAudienceIndicator()", option="percent" , credential="view_indicator_temps")
     * @return integer
     */
    public function getAudienceIndicator()
    {
    	return $this->audienceIndicator;
    }
    
    public static function getNameAudienceIndicator()
    {
    	$GAMgr= ManagersManager::getManager()->getContainer()->get('seriel_google_analytics.manager');
    	if (false) $GAMgr= new GoogleAnalyticsManager();
    	return $GAMgr->getLabelIndicator('audienceIndicator');
    }
    
    /**
     * Set monetisationIndicator
     *
     * @param integer $monetisationIndicator
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setMonetisationIndicator($monetisationIndicator)
    {
    	$this->monetisationIndicator = $monetisationIndicator;
    	
    	return $this;
    }
    
    /**
     * Get monetisationIndicator
     * @SER\ListeProperty("monetisationIndicator",labellogo="/images/monetisationIndicator.png", label="getNameMonetisationIndicator()", sort="number", format="number",class_supp="indicator", credential="view_indicator_temps", dbfield="monetisationIndicator")
	 * @SER\ReportingDataProperty("monetisationIndicator",labellogo="/images/monetisationIndicator.png", label="getNameMonetisationIndicator()", label_short="getNameMonetisationIndicator()", format="number", credential="view_indicator_temps", moyenne=true)
     * @SER\ReportingColRowProperty("monetisationIndicator",labellogo="/images/monetisationIndicator.png", label="getNameMonetisationIndicator()", option="percent" , credential="view_indicator_temps")
     * @return integer
     */
    public function getMonetisationIndicator()
    {
    	return $this->monetisationIndicator;
    }
    
    public static function getNameMonetisationIndicator()
    {
    	$GAMgr= ManagersManager::getManager()->getContainer()->get('seriel_google_analytics.manager');
    	if (false) $GAMgr= new GoogleAnalyticsManager();
    	return $GAMgr->getLabelIndicator('monetisationIndicator');
    }
    
    /**
     * Set abonneLikeIndicator
     *
     * @param integer $abonneLikeIndicator
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setAbonneLikeIndicator($abonneLikeIndicator)
    {
    	$this->abonneLikeIndicator= $abonneLikeIndicator;
    	
    	return $this;
    }
    
    /**
     * Get abonneLikeIndicator
     * @SER\ListeProperty("abonneLikeIndicator",labellogo="/images/abonneLikeIndicator.png", label="getNameAbonneLikeIndicator()", sort="number", format="number",class_supp="indicator", credential="view_indicator_temps", dbfield="abonneLikeIndicator")
	 * @SER\ReportingDataProperty("abonneLikeIndicator",labellogo="/images/abonneLikeIndicator.png", label="getNameAbonneLikeIndicator()", label_short="getNameAbonneLikeIndicator()", format="number", credential="view_indicator_temps", moyenne=true)
     * @SER\ReportingColRowProperty("abonneLikeIndicator",labellogo="/images/abonneLikeIndicator.png", label="getNameAbonneLikeIndicator()", option="percent" , credential="view_indicator_temps")
     * @return integer
     */
    public function getAbonneLikeIndicator()
    {
    	return $this->abonneLikeIndicator;
    }
    
    public static function getNameAbonneLikeIndicator()
    {
    	$GAMgr= ManagersManager::getManager()->getContainer()->get('seriel_google_analytics.manager');
    	if (false) $GAMgr= new GoogleAnalyticsManager();
    	return $GAMgr->getLabelIndicator('abonneLikeIndicator');
    }
    
    /**
     * Set visiteurLikeIndicator
     *
     * @param integer $visiteurLikeIndicator
     *
     * @return GoogleAnalyticsArticleMetrics
     */
    public function setVisiteurLikeIndicator($visiteurLikeIndicator)
    {
    	$this->visiteurLikeIndicator= $visiteurLikeIndicator;
    	
    	return $this;
    }
    
    /**
     * Get visiteurLikeIndicator
     * @SER\ListeProperty("visiteurLikeIndicator",labellogo="/images/visiteurLikeIndicator.png", label="getNameVisiteurLikeIndicator()", sort="number", format="number",class_supp="indicator", credential="view_indicator", dbfield="visiteurLikeIndicator")
	 * @SER\ReportingDataProperty("visiteurLikeIndicator",labellogo="/images/visiteurLikeIndicator.png", label="getNameVisiteurLikeIndicator()", label_short="getNameVisiteurLikeIndicator()", format="number", credential="view_indicator", moyenne=true)
     * @SER\ReportingColRowProperty("visiteurLikeIndicator",labellogo="/images/visiteurLikeIndicator.png", label="getNameVisiteurLikeIndicator()", credential="view_indicator", option="percent")
     * @return integer
     */
    public function getVisiteurLikeIndicator()
    {
    	return $this->visiteurLikeIndicator;
    }
    
    public static function getNameVisiteurLikeIndicator()
    {
    	$GAMgr= ManagersManager::getManager()->getContainer()->get('seriel_google_analytics.manager');
    	if (false) $GAMgr= new GoogleAnalyticsManager();
    	return $GAMgr->getLabelIndicator('visiteurLikeIndicator');
    }

    
    // intermediate calcul
    public function preCalcul() {
    	
    	//$entranceIndicator
    	if ($this->getPageviewMeasure() != 0) {
    		$entranceIndicator = ($this->getEntranceMeasure()/ sqrt($this->getPageviewMeasure())) * 100;
    		$this->setEntranceIndicator($entranceIndicator);
    	}
    	else {
    		$this->setEntranceIndicator(0);
    	}
    	
    	//bounceIndicator
    	if ($this->getPageviewMeasure() != 0) {
    		$bounceIndicator= ($this->getExitpageMeasure()/ sqrt($this->getPageviewMeasure())) * 100;
    		$this->setBounceIndicator($bounceIndicator);
    	}
    	else {
    		$this->setBounceIndicator(0);
    	}
    	
    	//subscriptionIndicator
    	if ($this->getPageviewMeasure() != 0) {
    		$subscriptionIndicator = ($this->getSubscriptionMeasure() / sqrt($this->getPageviewMeasure())) * 100;
    		$this->setSubscriptionIndicator($subscriptionIndicator);
    	}
    	else {
    		$this->setSubscriptionIndicator(0);
    	}
    	//pageview indicator return % view unique for article
    	if ($this->getPageviewMeasure() != 0) {
    		$PageviewIndicator = ($this->getUniquepageviewMeasure()/ $this->getPageviewMeasure()) * 100;
    		$this->setPageviewIndicator($PageviewIndicator);
    	}
    	else {
    		$this->setPageviewIndicator(0);
    	}
    	

    	//completionreadIndicator
    	if ($this->getArticle()->getReadTime()!= 0 and $this->getPageviewMeasure() != 0) {
    		$CompletionreadIndicator = (($this->getReadtimeMeasure()/$this->getPageviewMeasure() )/ $this->getArticle()->getReadTime()) * 100;
    		$this->setCompletionreadIndicator($CompletionreadIndicator);
    	}
    	else {
    		$this->setCompletionreadIndicator(0);
    	}
    	
    	//attentionIndicator
    	if ($this->getArticle()->getReadTime()!= 0 and $this->getPageviewMeasure() != 0) {
    		$AttentionIndicator = (($this->getReadtimeMeasure()/$this->getPageviewMeasure() )/ sqrt($this->getArticle()->getReadTime())) * 100;
    		$this->setAttentionIndicator($AttentionIndicator);
    	}
    	else {
    		$this->setAttentionIndicator(0);
    	}
    	
    	//completionreadSubscriberIndicator
    	if ($this->getArticle()->getReadTime()!= 0 and $this->getPageviewSubscriberMeasure() != 0) {
    		$CompletionreadSubscriberIndicator = (($this->getReadtimeSubscriberMeasure()/$this->getPageviewSubscriberMeasure())/ $this->getArticle()->getReadTime()) * 100;
    		$this->setCompletionreadSubscriberIndicator($CompletionreadSubscriberIndicator);
    	}
    	else {
    		$this->setCompletionreadSubscriberIndicator(0);
    	}
    	
    	
    	//completionreadVisitorIndicator
    	if ($this->getArticle()->getReadTime()!= 0 and $this->getPageviewVisitorMeasure() != 0) {
    		$CompletionreadVisitorIndicator = (($this->getReadtimeVisitorMeasure()/$this->getPageviewVisitorMeasure() )/ $this->getArticle()->getReadTime()) * 100;
    		$this->setCompletionreadVisitorIndicator($CompletionreadVisitorIndicator);
    	}
    	else {
    		$this->setCompletionreadVisitorIndicator(0);
    	}
    	
    	//AbonneLikeIndicator
    	if ($this->getArticle()->getReadTime()!= 0 and $this->getPageviewSubscriberMeasure() != 0) {
    		$AbonneLikeIndicator = (($this->getReadtimeSubscriberMeasure()/$this->getPageviewSubscriberMeasure() )/ sqrt($this->getArticle()->getReadTime())) * 100;
    		$this->setAbonneLikeIndicator($AbonneLikeIndicator);
    	}
    	else {
    		$this->setAbonneLikeIndicator(0);
    	}
    	
    	//VisitorLikeIndicator
    	if ($this->getArticle()->getReadTime()!= 0 and $this->getPageviewVisitorMeasure() != 0) {
    		$VisitorLikeIndicator = (($this->getReadtimeVisitorMeasure()/$this->getPageviewVisitorMeasure() )/ sqrt($this->getArticle()->getReadTime())) * 100;
    		$this->setVisiteurLikeIndicator($VisitorLikeIndicator);
    	}
    	else {
    		$this->setVisiteurLikeIndicator(0);
    	}
    	
    }
    //Calcul All indicator with measure and other parameters
    public function calculIndicator() {
    	
        $GAMetricsMgr = ManagersManager::getManager()->getContainer()->get('seriel_google_analytics.article_metrics_manager');
    	if (false) $GAMetricsMgr= new GoogleAnalyticsArticleMetricsManager();
    	
    	
    	$AvgCompletionRead= $GAMetricsMgr->getAvgCompletionRead();
    	$AvgSubscription= $GAMetricsMgr->getAvgSubscription();
    	$AvgUniquePageView = $GAMetricsMgr->getAvgUniquePageView();
    	$AvgUniquePageSubscriberView = $GAMetricsMgr->getAvgUniquePageSubscriberView();
    	$AvgUniquePageVisitorView = $GAMetricsMgr->getAvgUniquePageVisitorView();
    	$AvgEntranceIndicator= $GAMetricsMgr->getAvgEntranceIndicator();
    	$AvgPageViewIndicator = $GAMetricsMgr->getAvgPageViewIndicator();
    	$AvgCompletionReadSubscriber= $GAMetricsMgr->getAvgCompletionReadSubscriber();
    	$AvgCompletionReadVisitor= $GAMetricsMgr->getAvgCompletionReadVisitor();
    	$AvgAttention = $GAMetricsMgr->getAvgAttentionIndicator();
    	$AvgAbonneLike = $GAMetricsMgr->getAvgAbonneIndicator();
    	$AvgVisitorLike = $GAMetricsMgr->getAvgVisitorIndicator();
    	$AvgBounceIndicator =  $GAMetricsMgr->getAvgBounceIndicator();
    	$this->monetisationIndicator = $this->subscriptionIndicator;
    	
    	
    	//$entranceIndicator
    	$this->entranceIndicator= $AvgEntranceIndicator? ZombieUtils::getMarkOn100($this->entranceIndicator, $AvgEntranceIndicator) : 0;

    	//$bounceIndicator
    	$this->bounceIndicator= $AvgBounceIndicator? ZombieUtils::getMarkOn100($this->bounceIndicator, $AvgBounceIndicator) : 0;
    	
    	//subscriptionIndicator
    	$this->subscriptionIndicator = $AvgSubscription? ZombieUtils::getMarkOn100($this->subscriptionIndicator, $AvgSubscription) : 0;

    	//pageview indicator return % view unique for article
    	$this->pageviewIndicator = $AvgPageViewIndicator? ZombieUtils::getMarkOn100($this->pageviewIndicator, $AvgPageViewIndicator) : 0;
    	
    	//completionreadIndicator
    	$this->completionreadIndicator = $AvgCompletionRead? ZombieUtils::getMarkOn100($this->completionreadIndicator, $AvgCompletionRead) : 0;
 
    	//completionreadSubscriberIndicator
    	$this->completionreadSubscriberIndicator = $AvgCompletionReadSubscriber? ZombieUtils::getMarkOn100($this->completionreadSubscriberIndicator, $AvgCompletionReadSubscriber) : 0;
    	
    	//completionreadVisitorIndicator
    	$this->completionreadVisitorIndicator = $AvgCompletionReadVisitor? ZombieUtils::getMarkOn100($this->completionreadVisitorIndicator, $AvgCompletionReadVisitor) : 0;
    	

    	//monetisationIndicator    	
    	$monetisationIndicator= $AvgSubscription? ZombieUtils::getMarkOn100($this->monetisationIndicator, $AvgSubscription) : 0;
    	$this->setMonetisationIndicator($monetisationIndicator);
    	
    	//attentionIndicator   	
    	$attentionIndicator= $AvgAttention? ZombieUtils::getMarkOn100($this->attentionIndicator, $AvgAttention) : 0;
    	$this->setAttentionIndicator($attentionIndicator);
    	
    	//audienceIndicator
    	$audienceIndicator = $this->getUniquepageviewMeasure();
    	$audienceIndicator = $AvgUniquePageView? ZombieUtils::getMarkOn100($audienceIndicator, $AvgUniquePageView) : 0;
    	$this->setAudienceIndicator($audienceIndicator);

    	//abonneLikeIndicator
    	$AbonneaudienceIndicator = $this->getUniquepageviewSubscriberMeasure();
    	$AbonneaudienceIndicator = $AvgUniquePageSubscriberView? ZombieUtils::getMarkOn100($AbonneaudienceIndicator, $AvgUniquePageSubscriberView) : 0;
    	$abonneAttentionIndicator= $AvgAbonneLike? ZombieUtils::getMarkOn100($this->abonneLikeIndicator , $AvgAbonneLike) : 0;
    	$abonneLikeIndicator = ($AbonneaudienceIndicator + $abonneAttentionIndicator) / 2;
    	$this->setAbonneLikeIndicator($abonneLikeIndicator);
    	
    	//visiteurLikeIndicator
    	$VisitoraudienceIndicator = $this->getUniquepageviewVisitorMeasure();
    	$VisitoraudienceIndicator= $AvgUniquePageVisitorView? ZombieUtils::getMarkOn100($VisitoraudienceIndicator, $AvgUniquePageVisitorView) : 0;
    	$visitorAttentionIndicator= $AvgVisitorLike? ZombieUtils::getMarkOn100($this->visiteurLikeIndicator , $AvgVisitorLike) : 0;
    	$visitorLikeIndicator= ($VisitoraudienceIndicator + $visitorAttentionIndicator) / 2;
    	$this->setVisiteurLikeIndicator($visitorLikeIndicator);
    	return $this;
    }
    
    /************** ArticleMetrics Methods ***************/
    
    public function getAllMeasures() {
    	$measures = array();
    	
    	$measures['read_time'] = $this->getMeasure('read_time');
    	$measures['read_time_subscriber'] = $this->getMeasure('read_time_subscriber');
    	$measures['read_time_visitor'] = $this->getMeasure('read_time_visitor');
    	$measures['page_view'] = $this->getMeasure('page_view');
    	$measures['page_view_subscriber'] = $this->getMeasure('page_view_subscriber');
    	$measures['page_view_visitor'] = $this->getMeasure('page_view_visitor');
    	$measures['unique_page_view'] = $this->getMeasure('unique_page_view');
    	$measures['unique_page_view_subscriber'] = $this->getMeasure('unique_page_view_subscriber');
    	$measures['unique_page_view_visitor'] = $this->getMeasure('unique_page_view_visitor');
    	$measures['subscription'] = $this->getMeasure('subscription');
    	$measures['entrance'] = $this->getMeasure('entrance');
    	$measures['entrance_subscriber'] = $this->getMeasure('entrance_subscriber');
    	$measures['entrance_visitor'] = $this->getMeasure('entrance_visitor');
    	$measures['exitpage'] = $this->getMeasure('exitpage');
    	
    	return $measures;
    }
    public function getAllIndicators() {
    	$indicators = array();
    	
    	$indicators['completion_read'] = $this->getIndicator('completion_read');
    	$indicators['completion_read_subscriber'] = $this->getIndicator('completion_read_subscriber');
    	$indicators['completion_read_visitor'] = $this->getIndicator('completion_read_visitor');
    	$indicators['entrance'] = $this->getIndicator('entrance');
    	$indicators['subscription'] = $this->getIndicator('subscription');
    	$indicators['page_view'] = $this->getIndicator('page_view');
    	$indicators['monetisation'] = $this->getIndicator('monetisation');
    	$indicators['audience'] = $this->getIndicator('audience');
    	$indicators['attention'] = $this->getIndicator('attention');
    	$indicators['abonne_like'] = $this->getIndicator('abonne_like');
    	$indicators['visiteur_like'] = $this->getIndicator('visiteur_like');
    	$indicators['bounce'] = $this->getIndicator('bounce');
    	
    	return $indicators;
    }
    public static function getAllIdIndicators() {
    	$authChecker = SymfonyUtils::getAuthorizationChecker();
    	$IDindicators = array();
    	$reader = ManagersManager::getManager()->getContainer()->get('annotation_reader');
    	$converter = new SerielListePropertyConverter($reader);
    	$fields = $converter->convert('Seriel\GoogleAnalyticsBundle\Entity\GoogleAnalyticsArticleMetrics');
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
    				if ( ((isset($cred) ) && ($authChecker->isGranted('ANY_RIGHT_ON[Seriel\GoogleAnalyticsBundle\Entity\GoogleAnalyticsArticleMetrics >> '.$cred.']'))) OR  ( $authChecker->isGranted($cred))  OR  ( ! isset($cred))) {
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
    	$fields = $converter->convert('Seriel\GoogleAnalyticsBundle\Entity\GoogleAnalyticsArticleMetrics');
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
    	
    	if ($measure == 'read_time') return $this->readtimeMeasure;
    	if ($measure == 'read_time_subscriber') return $this->readtimeSubscriberMeasure;
    	if ($measure == 'read_time_visitor') return $this->readtimeVisitorMeasure;
    	if ($measure == 'page_view') return $this->pageviewMeasure;
    	if ($measure == 'page_view_subscriber') return $this->pageviewSubscriberMeasure;
    	if ($measure == 'page_view_visitor') return $this->pageviewVisitorMeasure;
    	if ($measure == 'unique_page_view') return $this->uniquepageviewMeasure;
    	if ($measure == 'unique_page_view_subscriber') return $this->uniquepageviewSubscriberMeasure;
    	if ($measure == 'unique_page_view_visitor') return $this->uniquepageviewVisitorMeasure;
    	if ($measure == 'subscription') return $this->subscriptionMeasure;
    	if ($measure == 'entrance') return $this->entranceMeasure;
    	if ($measure == 'entrance_subscriber') return $this->entranceSubscriberMeasure;
    	if ($measure == 'entrance_visitor') return $this->entranceVisitorMeasure;
    	if ($measure == 'exitpage') return $this->exitpageMeasure;
    	
    	return null;
    }
    public function getIndicator($indicator) {
    	$indicator = trim(strtolower($indicator));
    	
    	if (!$indicator) return null;
    	
    	if ($indicator == 'completion_read') return $this->completionreadIndicator;
    	if ($indicator == 'completion_read_subscriber') return $this->completionreadSubscriberIndicator;
    	if ($indicator == 'completion_read_visitor') return $this->completionreadVisitorIndicator;
    	if ($indicator == 'entrance') return $this->entranceIndicator;
    	if ($indicator == 'subscription') return $this->subscriptionIndicator;
    	if ($indicator == 'page_view') return $this->pageviewIndicator;
    	if ($indicator == 'monetisation') return $this->monetisationIndicator;
    	if ($indicator == 'audience') return $this->audienceIndicator;
    	if ($indicator == 'attention') return $this->attentionIndicator;
    	if ($indicator == 'abonne_like') return $this->abonneLikeIndicator;
    	if ($indicator == 'visiteur_like') return $this->visiteurLikeIndicator;
    	if ($indicator == 'bounce') return $this->bounceIndicator;
    	
    	
    	
    	return null;
    }

}
