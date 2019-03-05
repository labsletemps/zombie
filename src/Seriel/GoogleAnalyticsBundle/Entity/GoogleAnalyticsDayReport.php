<?php

namespace Seriel\GoogleAnalyticsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\Common\Collections\ArrayCollection;
use Seriel\AppliToolboxBundle\Annotation as SER;

/**
 * GoogleAnalyticsDayReport
 *
 * @ORM\Entity
 * @ORM\Table(name="google_analytics_day_report",options={"engine"="MyISAM"} ,indexes={@ORM\Index(name="day_ga_idx", columns={"day"}),@ORM\Index(name="path_ga_idx", columns={"path"})} ,uniqueConstraints={@UniqueConstraint(name="unique_ga_dayreport_idx", columns={"day", "path"})} )
 * @UniqueEntity(fields={"day", "path"})
 */
class GoogleAnalyticsDayReport extends RootObject implements Listable
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
     * @var \DateTime
     *
     * @ORM\Column(name="day", type="date")
     */
    private $day;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=330)
     */
    private $path;

    /**
     * @var int
     *
     * @ORM\Column(name="readtime", type="integer")
     */
    private $readtime;
    
    /**
     * @var int
     *
     * @ORM\Column(name="readtime_subscriber", type="integer")
     */
    private $readtime_subscriber;
    
    /**
     * @var int
     *
     * @ORM\Column(name="readtime_visitor", type="integer")
     */
    private $readtime_visitor;

    /**
     * @var int
     *
     * @ORM\Column(name="pageview", type="integer")
     */
    private $pageview;
    
    /**
     * @var int
     *
     * @ORM\Column(name="pageview_subscriber", type="integer")
     */
    private $pageview_subscriber;
    
    /**
     * @var int
     *
     * @ORM\Column(name="pageview_visitor", type="integer")
     */
    private $pageview_visitor;

    /**
     * @var int
     *
     * @ORM\Column(name="uniquepageview", type="integer")
     */
    private $uniquepageview;
    
    /**
     * @var int
     *
     * @ORM\Column(name="uniquepageview_subscriber", type="integer")
     */
    private $uniquepageview_subscriber;
    
    /**
     * @var int
     *
     * @ORM\Column(name="uniquepageview_visitor", type="integer")
     */
    private $uniquepageview_visitor;
    
    /**
     * @var int
     *
     * @ORM\Column(name="subscription", type="integer")
     */
    private $subscription;

    /**
     * @var int
     *
     * @ORM\Column(name="entrance", type="integer")
     */
    private $entrance;

    /**
     * @var int
     *
     * @ORM\Column(name="entrance_subscriber", type="integer")
     */
    private $entrance_subscriber;
    
    /**
     * @var int
     *
     * @ORM\Column(name="entrance_visitor", type="integer")
     */
    private $entrance_visitor;
    
    /**
     * @ORM\OneToMany(targetEntity="Seriel\GoogleAnalyticsBundle\Entity\DayReportEntrance", mappedBy="dayreport", cascade={"remove", "persist"})
     */
    private $sourceEntrances;
    
    /**
     * @var int
     *
     * @ORM\Column(name="exitpage", type="integer")
     */
    private $exitpage;

    public function __construct()
    {
    	parent::__construct();
    	$this->day = new \DateTime();
    	$this->readtime = 0;
    	$this->readtime_subscriber = 0;
    	$this->readtime_visitor = 0;
    	$this->pageview= 0;
    	$this->pageview_subscriber= 0;
    	$this->pageview_visitor= 0;
    	$this->uniquepageview= 0;
    	$this->uniquepageview_subscriber= 0;
    	$this->uniquepageview_visitor= 0;
    	$this->subscription= 0;
    	$this->entrance= 0;
    	$this->entrance_subscriber= 0;
    	$this->entrance_visitor= 0;
    	$this->sourceEntrances= new ArrayCollection();
    	$this->exitpage= 0;
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
     * Set day
     *
     * @param \DateTime $day
     *
     * @return GoogleAnalyticsDayReport
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day
     *
     * @SER\ListeProperty("day", label="Jour", sort="date", format="date", dbfield="day")
     * @SER\ReportingColRowProperty("day", label="Jour", sort="date", format="date", option="date")
     * @return \DateTime
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return GoogleAnalyticsDayReport
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     * @SER\ListeProperty("path", label="URI", sort="string", format="none", dbfield="path")
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set readtime
     *
     * @param integer $readtime
     *
     * @return GoogleAnalyticsDayReport
     */
    public function setReadtime($readtime)
    {
        $this->readtime = $readtime;
        return $this;
    }

    /**
     * Add readtime
     *
     * @param integer $readtime
     *
     * @return GoogleAnalyticsDayReport
     */
    public function addReadtime($readtime)
    {
    	$this->readtime += $readtime;
    	
    	return $this;
    }
    
    /**
     * Get readtime
     *
     * @SER\ListeProperty("readtime", label="Readtime", sort="number", format="none", dbfield="readtime")
     * @return int
     */
    public function getReadtime()
    {
        return $this->readtime;
    }

    /**
     * Set pageview
     *
     * @param integer $pageview
     *
     * @return GoogleAnalyticsDayReport
     */
    public function setPageview($pageview)
    {
        $this->pageview = $pageview;

        return $this;
    }

    /**
     * Add pageview
     *
     * @param integer $pageview
     *
     * @return GoogleAnalyticsDayReport
     */
    public function addPageview($pageview)
    {
    	$this->pageview += $pageview;
    	
    	return $this;
    }

    /**
     * Get pageview
     *
     * @SER\ListeProperty("pageview", label="Pageview", sort="number", format="none", dbfield="pageview")
     * @return int
     */
    public function getPageview()
    {
        return $this->pageview;
    }

    /**
     * Set uniquepageview
     *
     * @param integer $uniquepageview
     *
     * @return GoogleAnalyticsDayReport
     */
    public function setUniquepageview($uniquepageview)
    {
    	$this->uniquepageview= $uniquepageview;
    	
    	return $this;
    }
    
    /**
     * Add uniquepageview
     *
     * @param integer $uniquepageview
     *
     * @return GoogleAnalyticsDayReport
     */
    public function addUniquepageview($uniquepageview)
    {
    	$this->uniquepageview += $uniquepageview;
    	
    	return $this;
    }
    
    /**
     * Get uniquepageview
     *
     * @SER\ListeProperty("uniquepageview", label="UniquePageview", sort="number", format="none", dbfield="uniquepageview")
     * @return int
     */
    public function getUniquepageview()
    {
    	return $this->uniquepageview;
    }
    
    
    /**
     * Set subscription
     *
     * @param integer $subscription
     *
     * @return GoogleAnalyticsDayReport
     */
    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * Add subscription
     *
     * @param integer $subscription
     *
     * @return GoogleAnalyticsDayReport
     */
    public function addSubscription($subscription)
    {
    	$this->subscription += $subscription;
    	
    	return $this;
    }
    
    /**
     * Get subscription
     * @SER\ListeProperty("subscription", label="Subscription", sort="number", format="none", dbfield="subscription")
     * @return int
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * Set entrance
     *
     * @param integer $entrance
     *
     * @return GoogleAnalyticsDayReport
     */
    public function setEntrance($entrance)
    {
        $this->entrance = $entrance;

        return $this;
    }

    /**
     * Add entrance
     *
     * @param integer $entrance
     *
     * @return GoogleAnalyticsDayReport
     */
    public function addEntrance($entrance)
    {
    	$this->entrance += $entrance;
    	
    	return $this;
    }
    
    /**
     * Get entrance
     * @SER\ListeProperty("entrance", label="Entrance", sort="number", format="none", dbfield="entrance")
     * @return int
     */
    public function getEntrance()
    {
        return $this->entrance;
    }

    /**
     * Set exitpage
     *
     * @param integer $exitpage
     *
     * @return GoogleAnalyticsDayReport
     */
    public function setExitpage($exitpage)
    {
    	$this->exitpage= $exitpage;
    	
    	return $this;
    }
    
    /**
     * Add exitpage
     *
     * @param integer $exitpage
     *
     * @return GoogleAnalyticsDayReport
     */
    public function addExitpage($exitpage)
    {
    	$this->exitpage += $exitpage;
    	
    	return $this;
    }
    
    /**
     * Get exitpage
     * @SER\ListeProperty("exitpage", label="Exitpage", sort="number", format="none", dbfield="exitpage")
     * @return int
     */
    public function getExitpage()
    {
    	return $this->exitpage;
    }
    
    /**
     * Set readtimeSubscriber
     *
     * @param integer $readtimeSubscriber
     *
     * @return GoogleAnalyticsDayReport
     */
    public function setReadtimeSubscriber($readtimeSubscriber)
    {
        $this->readtime_subscriber = $readtimeSubscriber;

        return $this;
    }
    
    /**
     * Add readtimeSubscriber
     *
     * @param integer $readtimeSubscriber
     *
     * @return GoogleAnalyticsDayReport
     */
    public function addReadtimeSubscriber($readtimeSubscriber)
    {
    	$this->readtime_subscriber += $readtimeSubscriber;
    	
    	return $this;
    }

    /**
     * Get readtimeSubscriber
     *
     * @SER\ListeProperty("readtime_subscriber", label="Readtime subscriber", sort="number", format="none", dbfield="readtime_subscriber")
     * @return integer
     */
    public function getReadtimeSubscriber()
    {
        return $this->readtime_subscriber;
    }

    /**
     * Set readtimeVisitor
     *
     * @param integer $readtimeVisitor
     *
     * @return GoogleAnalyticsDayReport
     */
    public function setReadtimeVisitor($readtimeVisitor)
    {
        $this->readtime_visitor = $readtimeVisitor;

        return $this;
    }

    /**
     * Add readtimeVisitor
     *
     * @param integer $readtimeVisitor
     *
     * @return GoogleAnalyticsDayReport
     */
    public function addReadtimeVisitor($readtimeVisitor)
    {
    	$this->readtime_visitor += $readtimeVisitor;
    	
    	return $this;
    }
    
    /**
     * Calcul readtimeVisitor
     *
     * @return GoogleAnalyticsDayReport
     */
    public function calculReadtimeVisitor()
    {
    	$this->readtime_visitor = $this->readtime - $this->readtime_subscriber;
    	
    	return $this;
    }
    
    /**
     * Get readtimeVisitor
     * @SER\ListeProperty("readtime_visitor", label="Readtime visitor", sort="number", format="none", dbfield="readtime_visitor")
     * @return integer
     */
    public function getReadtimeVisitor()
    {
        return $this->readtime_visitor;
    }

    /**
     * Set pageviewSubscriber
     *
     * @param integer $pageviewSubscriber
     *
     * @return GoogleAnalyticsDayReport
     */
    public function setPageviewSubscriber($pageviewSubscriber)
    {
        $this->pageview_subscriber = $pageviewSubscriber;

        return $this;
    }
    
    /**
     * Add pageviewSubscriber
     *
     * @param integer $pageviewSubscriber
     *
     * @return GoogleAnalyticsDayReport
     */
    public function addPageviewSubscriber($pageviewSubscriber)
    {
    	$this->pageview_subscriber += $pageviewSubscriber;
    	
    	return $this;
    }

    /**
     * Get pageviewSubscriber
     * @SER\ListeProperty("pageview_subscriber", label="Pageview subscriber", sort="number", format="none", dbfield="pageview_subscriber")
     * @return integer
     */
    public function getPageviewSubscriber()
    {
        return $this->pageview_subscriber;
    }

    /**
     * Set pageviewVisitor
     *
     * @param integer $pageviewVisitor
     *
     * @return GoogleAnalyticsDayReport
     */
    public function setPageviewVisitor($pageviewVisitor)
    {
        $this->pageview_visitor = $pageviewVisitor;

        return $this;
    }
    
    /**
     * Add pageviewVisitor
     *
     * @param integer $pageviewVisitor
     *
     * @return GoogleAnalyticsDayReport
     */
    public function addPageviewVisitor($pageviewVisitor)
    {
    	$this->pageview_visitor += $pageviewVisitor;
    	
    	return $this;
    }

    /**
     * Calcul pageviewVisitor
     *
     * @return GoogleAnalyticsDayReport
     */
    public function calculPageviewVisitor()
    {
    	$this->pageview_visitor= $this->pageview - $this->pageview_subscriber;
    	
    	return $this;
    }
    
    /**
     * Get pageviewVisitor
     * @SER\ListeProperty("pageview_visitor", label="Pageview visitor", sort="number", format="none", dbfield="pageview_visitor")
     * @return integer
     */
    public function getPageviewVisitor()
    {
        return $this->pageview_visitor;
    }

    /**
     * Set uniquepageviewSubscriber
     *
     * @param integer $uniquepageviewSubscriber
     *
     * @return GoogleAnalyticsDayReport
     */
    public function setUniquepageviewSubscriber($uniquepageviewSubscriber)
    {
        $this->uniquepageview_subscriber = $uniquepageviewSubscriber;

        return $this;
    }
    
    /**
     * Add uniquepageviewSubscriber
     *
     * @param integer $uniquepageviewSubscriber
     *
     * @return GoogleAnalyticsDayReport
     */
    public function addUniquepageviewSubscriber($uniquepageviewSubscriber)
    {
    	$this->uniquepageview_subscriber += $uniquepageviewSubscriber;
    	
    	return $this;
    }

    /**
     * Get uniquepageviewSubscriber
     *
     * @SER\ListeProperty("uniquepageview_subscriber", label="Uniquepageview subscriber", sort="number", format="none", dbfield="uniquepageview_subscriber")
     * @return integer
     */
    public function getUniquepageviewSubscriber()
    {
        return $this->uniquepageview_subscriber;
    }

    /**
     * Set uniquepageviewVisitor
     *
     * @param integer $uniquepageviewVisitor
     *
     * @return GoogleAnalyticsDayReport
     */
    public function setUniquepageviewVisitor($uniquepageviewVisitor)
    {
        $this->uniquepageview_visitor = $uniquepageviewVisitor;

        return $this;
    }
    
    /**
     * Add uniquepageviewVisitor
     *
     * @param integer $uniquepageviewVisitor
     *
     * @return GoogleAnalyticsDayReport
     */
    public function addUniquepageviewVisitor($uniquepageviewVisitor)
    {
    	$this->uniquepageview_visitor += $uniquepageviewVisitor;
    	
    	return $this;
    }

    /**
     * Calcul uniquepageviewVisitor
     *
     * @return GoogleAnalyticsDayReport
     */
    public function calculUniquepageviewVisitor()
    {
    	$this->uniquepageview_visitor= $this->uniquepageview - $this->uniquepageview_subscriber;
    	
    	return $this;
    }
    
    /**
     * Get uniquepageviewVisitor
     *
     * @SER\ListeProperty("uniquepageview_visitor", label="Uniquepageview visitor", sort="number", format="none", dbfield="uniquepageview_visitor")
     * @return integer
     */
    public function getUniquepageviewVisitor()
    {
        return $this->uniquepageview_visitor;
    }

    /**
     * Set entranceSubscriber
     *
     * @param integer $entranceSubscriber
     *
     * @return GoogleAnalyticsDayReport
     */
    public function setEntranceSubscriber($entranceSubscriber)
    {
        $this->entrance_subscriber = $entranceSubscriber;

        return $this;
    }
    
    /**
     * Add entranceSubscriber
     *
     * @param integer $entranceSubscriber
     *
     * @return GoogleAnalyticsDayReport
     */
    public function addEntranceSubscriber($entranceSubscriber)
    {
    	$this->entrance_subscriber += $entranceSubscriber;
    	
    	return $this;
    }

    /**
     * Get entranceSubscriber
     *
     * @SER\ListeProperty("entrance_subscriber", label="Entrance subscriber", sort="number", format="none", dbfield="entrance_subscriber")
     * @return integer
     */
    public function getEntranceSubscriber()
    {
        return $this->entrance_subscriber;
    }

    /**
     * Set entranceVisitor
     *
     * @param integer $entranceVisitor
     *
     * @return GoogleAnalyticsDayReport
     */
    public function setEntranceVisitor($entranceVisitor)
    {
        $this->entrance_visitor = $entranceVisitor;

        return $this;
    }
    
    /**
     * Add entranceVisitor
     *
     * @param integer $entranceVisitor
     *
     * @return GoogleAnalyticsDayReport
     */
    public function addEntranceVisitor($entranceVisitor)
    {
    	$this->entrance_visitor += $entranceVisitor;
    	
    	return $this;
    }

    
    /**
     * Calcul entranceVisitor
     *
     * @return GoogleAnalyticsDayReport
     */
    public function calculEntranceVisitor()
    {
    	$this->entrance_visitor= $this->entrance - $this->entrance_subscriber;
    	
    	return $this;
    }
    
    /**
     * Get entranceVisitor
     * @SER\ListeProperty("entrance_visitor", label="Entrance visitor", sort="number", format="none", dbfield="entrance_visitor")
     * @return integer
     */
    public function getEntranceVisitor()
    {
        return $this->entrance_visitor;
    }
    

    /**
     * Add sourceEntrance
     *
     * @param \Seriel\GoogleAnalyticsBundle\Entity\DayReportEntrance $sourceEntrance
     *
     * @return GoogleAnalyticsDayReport
     */
    public function addSourceEntrance(\Seriel\GoogleAnalyticsBundle\Entity\DayReportEntrance $sourceEntrance)
    {
    	//check if source entrance exist
    	foreach ($this->sourceEntrances as $dayreportEntrance) {
    		if ($dayreportEntrance->getPath() == $sourceEntrance->getPath()) {
    			$dayreportEntrance->addCount($sourceEntrance->getCount());
    			return $this;
    		}
    	}
        $this->sourceEntrances[] = $sourceEntrance;
        $sourceEntrance->setDayreport($this);

        return $this;
    }

    /**
     * Remove sourceEntrance
     *
     * @param \Seriel\GoogleAnalyticsBundle\Entity\DayReportEntrance $sourceEntrance
     */
    public function removeSourceEntrance(\Seriel\GoogleAnalyticsBundle\Entity\DayReportEntrance $sourceEntrance)
    {
        $this->sourceEntrances->removeElement($sourceEntrance);
    }

    /**
     * Get sourceEntrances
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSourceEntrances()
    {
        return $this->sourceEntrances;
    }
}
