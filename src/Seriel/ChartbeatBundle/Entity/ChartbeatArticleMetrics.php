<?php

namespace Seriel\ChartbeatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Annotation as SER;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;
use Seriel\AppliToolboxBundle\Managers\ManagersManager;
use Seriel\ChartbeatBundle\Managers\ChartbeatManager;
use Seriel\ChartbeatBundle\Managers\ChartbeatArticleMetricsManager;
use ZombieBundle\Utils\ZombieUtils;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use ZombieBundle\API\Entity\ArticleMetrics;
use Seriel\AppliToolboxBundle\Annotation\SerielListePropertyConverter;

/**
 * @ORM\Entity
 * @ORM\Table(name="chartbeat_article_metrics",options={"engine"="MyISAM"},indexes={@ORM\Index(name="metric_page_views_cbam_idx", columns={"metric_page_views"}),@ORM\Index(name="metric_reading_time_cbam_idx", columns={"metric_reading_time"}),@ORM\Index(name="metric_read_percent_cbam_idx", columns={"metric_read_percent"})})
 */
class ChartbeatArticleMetrics extends RootObject implements ArticleMetrics, Listable
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
	protected $date_evergreen;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $nb_days_since_parution;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $nb_days_since_evergreen;
	
	/**
	 * @ORM\Column(type="datetime", nullable=false)
	 */
	protected $date_calcul;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $page_views_total;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $page_time_total;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $page_time_avg;
	
	/**
	 * @ORM\Column(type="decimal", precision=6, scale=4,  nullable=true)
	 */
	protected $page_time_avg_on_words;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $page_avg_scroll_since_parution;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $page_avg_scroll_since_evergreen;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $page_views_per_day_since_parution;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $page_views_per_day_between_parution_and_evergreen;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $page_views_per_day_since_evergreen;
	
	/**
	 * @ORM\Column(type="decimal", precision=6, scale=4, nullable=true)
	 */
	protected $page_views_ratio_before_and_after_evergreen;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $page_time_per_day_since_parution;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $page_average_time_since_parution;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $page_time_per_day_since_evergreen;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $page_average_time_since_evergreen;
	
	/**
	 * @ORM\Column(type="decimal", precision=6, scale=4, nullable=true)
	 */
	protected $metric_page_views;
	
	/**
	 * @ORM\Column(type="decimal", precision=6, scale=4, nullable=true)
	 */
	protected $metric_reading_time;
	
	/**
	 * @ORM\Column(type="decimal", precision=6, scale=4, nullable=true)
	 */
	protected $metric_read_percent;
	
	/**
	 * @ORM\Column(type="decimal", precision=6, scale=4, nullable=true)
	 */
	protected $metric_evergreen;
	
	/**
	 * @ORM\Column(type="decimal", precision=6, scale=4, nullable=true)
	 */
	protected $indicator_attention;
	
	/**
	 * @ORM\Column(type="decimal", precision=6, scale=4, nullable=true)
	 */
	protected $indicator_durabilite;
	
	
	public function __construct(\ZombieBundle\Entity\News\Article $article)
	{
		parent::__construct();
		
		$this->article = $article;
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
     * @return ChartbeatArticleMetrics
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
     * Set dateEvergreen
     *
     * @param \DateTime $dateEvergreen
     *
     * @return ChartbeatArticleMetrics
     */
    public function setDateEvergreen($dateEvergreen)
    {
        $this->date_evergreen = $dateEvergreen;

        return $this;
    }

    /**
     * Get dateEvergreen
     *
     * @return \DateTime
     */
    public function getDateEvergreen()
    {
        return $this->date_evergreen;
    }

    /**
     * Set nbDaysSinceParution
     *
     * @param integer $nbDaysSinceParution
     *
     * @return ChartbeatArticleMetrics
     */
    public function setNbDaysSinceParution($nbDaysSinceParution)
    {
        $this->nb_days_since_parution = $nbDaysSinceParution;

        return $this;
    }

    /**
     * Get nbDaysSinceParution
     *
     * @return integer
     */
    public function getNbDaysSinceParution()
    {
        return $this->nb_days_since_parution;
    }

    /**
     * Set nbDaysSinceEvergreen
     *
     * @param integer $nbDaysSinceEvergreen
     *
     * @return ChartbeatArticleMetrics
     */
    public function setNbDaysSinceEvergreen($nbDaysSinceEvergreen)
    {
        $this->nb_days_since_evergreen = $nbDaysSinceEvergreen;

        return $this;
    }

    /**
     * Get nbDaysSinceEvergreen
     *
     * @return integer
     */
    public function getNbDaysSinceEvergreen()
    {
        return $this->nb_days_since_evergreen;
    }

    /**
     * Set dateCalcul
     *
     * @param \DateTime $dateCalcul
     *
     * @return ChartbeatArticleMetrics
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
     * Set pageViewsTotal
     *
     * @param integer $pageViewsTotal
     *
     * @return ChartbeatArticleMetrics
     */
    public function setPageViewsTotal($pageViewsTotal)
    {
        $this->page_views_total = $pageViewsTotal;

        return $this;
    }

    /**
     * Get pageViewsTotal
     *
     * @return integer
     * 
     * @SER\ListeProperty("page_views_total", label="Total page views Measure", sort="number", format="none", credential="view_measure", dbfield="page_views_total")
	 * @SER\ReportingDataProperty("page_views_total", label="Total page views Measure", label_short="Nb. Views", credential="view_measure", format="number") 
     */
    public function getPageViewsTotal()
    {
        return $this->page_views_total;
    }

    /**
     * Set pageTimeTotal
     *
     * @param integer $pageTimeTotal
     *
     * @return ChartbeatArticleMetrics
     */
    public function setPageTimeTotal($pageTimeTotal)
    {
        $this->page_time_total = $pageTimeTotal;

        return $this;
    }

    /**
     * Get pageTimeTotal
     *
     * @return integer
     * 
     * @SER\ListeProperty("page_time_total", label="Total page time Measure", sort="number", format="none", credential="view_measure", dbfield="page_time_total")
	 * @SER\ReportingDataProperty("page_time_total", label="Total page time Measure", label_short="Page time", credential="view_measure", format="number") 
     */
    public function getPageTimeTotal()
    {
        return $this->page_time_total;
    }

    /**
     * Set pageAvgScrollSinceParution
     *
     * @param integer $pageAvgScrollSinceParution
     *
     * @return ChartbeatArticleMetrics
     */
    public function setPageAvgScrollSinceParution($pageAvgScrollSinceParution)
    {
        $this->page_avg_scroll_since_parution = $pageAvgScrollSinceParution;

        return $this;
    }

    /**
     * Get pageAvgScrollSinceParution
     *
     * @return integer
     * 
     * @SER\ListeProperty("page_avg_scroll_since_parution", label="Page average Scroll Measure", sort="number", format="none", credential="view_measure", dbfield="page_avg_scroll_since_parution")
	 * @SER\ReportingDataProperty("page_avg_scroll_since_parution", label="Page average Scroll Measure", label_short="Scroll", credential="view_measure", format="number") 
     */
    public function getPageAvgScrollSinceParution()
    {
        return $this->page_avg_scroll_since_parution;
    }

    /**
     * Set pageAvgScrollSinceEvergreen
     *
     * @param integer $pageAvgScrollSinceEvergreen
     *
     * @return ChartbeatArticleMetrics
     */
    public function setPageAvgScrollSinceEvergreen($pageAvgScrollSinceEvergreen)
    {
        $this->page_avg_scroll_since_evergreen = $pageAvgScrollSinceEvergreen;

        return $this;
    }

    /**
     * Get pageAvgScrollSinceEvergreen
     *
     * @return integer
     */
    public function getPageAvgScrollSinceEvergreen()
    {
        return $this->page_avg_scroll_since_evergreen;
    }

    /**
     * Set pageViewsPerDaySinceParution
     *
     * @param integer $pageViewsPerDaySinceParution
     *
     * @return ChartbeatArticleMetrics
     */
    public function setPageViewsPerDaySinceParution($pageViewsPerDaySinceParution)
    {
        $this->page_views_per_day_since_parution = $pageViewsPerDaySinceParution;

        return $this;
    }

    /**
     * Get pageViewsPerDaySinceParution
     *
     * @return integer
     */
    public function getPageViewsPerDaySinceParution()
    {
        return $this->page_views_per_day_since_parution;
    }

    /**
     * Set pageViewsPerDaySinceEvergreen
     *
     * @param integer $pageViewsPerDaySinceEvergreen
     *
     * @return ChartbeatArticleMetrics
     */
    public function setPageViewsPerDaySinceEvergreen($pageViewsPerDaySinceEvergreen)
    {
        $this->page_views_per_day_since_evergreen = $pageViewsPerDaySinceEvergreen;

        return $this;
    }

    /**
     * Get pageViewsPerDaySinceEvergreen
     *
     * @return integer
     */
    public function getPageViewsPerDaySinceEvergreen()
    {
        return $this->page_views_per_day_since_evergreen;
    }

    /**
     * Set pageTimePerDaySinceParution
     *
     * @param integer $pageTimePerDaySinceParution
     *
     * @return ChartbeatArticleMetrics
     */
    public function setPageTimePerDaySinceParution($pageTimePerDaySinceParution)
    {
        $this->page_time_per_day_since_parution = $pageTimePerDaySinceParution;

        return $this;
    }

    /**
     * Get pageTimePerDaySinceParution
     *
     * @return integer
     */
    public function getPageTimePerDaySinceParution()
    {
        return $this->page_time_per_day_since_parution;
    }

    /**
     * Set pageTimePerDaySinceEvergreen
     *
     * @param integer $pageTimePerDaySinceEvergreen
     *
     * @return ChartbeatArticleMetrics
     */
    public function setPageTimePerDaySinceEvergreen($pageTimePerDaySinceEvergreen)
    {
        $this->page_time_per_day_since_evergreen = $pageTimePerDaySinceEvergreen;

        return $this;
    }

    /**
     * Get pageTimePerDaySinceEvergreen
     *
     * @return integer
     */
    public function getPageTimePerDaySinceEvergreen()
    {
        return $this->page_time_per_day_since_evergreen;
    }

    /**
     * Set metricPageViews
     *
     * @param string $metricPageViews
     *
     * @return ChartbeatArticleMetrics
     */
    public function setMetricPageViews($metricPageViews)
    {
        $this->metric_page_views = $metricPageViews;

        return $this;
    }

    /**
     * Get metricPageViews
     *
     * @return string
     * 
     * @SER\ListeProperty("indic_page_views",labellogo="/images/indic_page_views.png", label="getNameMetricPageViews()", sort="number",class_supp="indicator", format="number", credential="view_indicator", dbfield="metric_page_views")
	 * @SER\ReportingDataProperty("indic_page_views",labellogo="/images/indic_page_views.png", label="getNameMetricPageViews()", label_short="getNameMetricPageViews()", format="number", credential="view_indicator", moyenne=true) 
     * @SER\ReportingColRowProperty("indic_page_views",labellogo="/images/indic_page_views.png", label="getNameMetricPageViews()", option="percent" , credential="view_indicator")
     */
    public function getMetricPageViews()
    {
        return $this->metric_page_views;
    }
    
    public static function getNameMetricPageViews()
    {
    	$ChartbeatMgr = ManagersManager::getManager()->getContainer()->get('seriel_chartbeat.manager');
    	if (false) $ChartbeatMgr= new ChartbeatManager();
    	return $ChartbeatMgr->getLabelIndicator('indic_page_views');
    }
    
    /**
     * Set metricReadingTime
     *
     * @param string $metricReadingTime
     *
     * @return ChartbeatArticleMetrics
     */
    public function setMetricReadingTime($metricReadingTime)
    {
        $this->metric_reading_time = $metricReadingTime;

        return $this;
    }

    /**
     * Get metricReadingTime
     *
     * @return string
     * 
     * @SER\ListeProperty("indic_reading_time",labellogo="/images/indic_reading_time.png", label="getNameMetricReadingTime()", sort="number",class_supp="indicator", format="none", credential="view_indicator", dbfield="metric_reading_time")
	 * @SER\ReportingDataProperty("indic_reading_time",labellogo="/images/indic_reading_time.png", label="getNameMetricReadingTime()", label_short="getNameMetricReadingTime()", format="number", credential="view_indicator", moyenne=true) 
     * @SER\ReportingColRowProperty("indic_reading_time",labellogo="/images/indic_reading_time.png", label="getNameMetricReadingTime()", option="percent" , credential="view_indicator")
     */
    public function getMetricReadingTime()
    {
        return $this->metric_reading_time;
    }

    public static function getNameMetricReadingTime()
    {
    	$ChartbeatMgr = ManagersManager::getManager()->getContainer()->get('seriel_chartbeat.manager');
    	if (false) $ChartbeatMgr= new ChartbeatManager();
    	return $ChartbeatMgr->getLabelIndicator('indic_reading_time');
    }
    
    /**
     * Set metricReadPercent
     *
     * @param string $metricReadPercent
     *
     * @return ChartbeatArticleMetrics
     */
    public function setMetricReadPercent($metricReadPercent)
    {
        $this->metric_read_percent = $metricReadPercent;

        return $this;
    }

    /**
     * Get metricReadPercent
     *
     * @return string
     */
    public function getMetricReadPercent()
    {
        return $this->metric_read_percent;
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
    
    public function preCalculate() {
    	// get raw data.
    	$now = new \DateTime();
    	 
    	$chartbeatMgr = ManagersManager::getManager()->getContainer()->get('seriel_chartbeat.manager');
    	if (false) $chartbeatMgr = new ChartbeatManager();
    	 
    	$nb_days_before_evergreen = $chartbeatMgr->getNbDaysBeforeEvergreen();
    	 
    	$this->date_parution = $this->article->getDateParution();
    	$this->date_evergreen = clone $this->date_parution;
    	$this->date_evergreen->add(date_interval_create_from_date_string("$nb_days_before_evergreen day"));
    	 
    	$cbadrMgr = ManagersManager::getManager()->getContainer()->get('seriel_chartbeat.cbadr_manager');
    	if (false) $cbadrMgr = new ChartbeatArticleDayReportManager();
    
    	$date_evergreen_str = $this->date_evergreen->format('Y-m-d');
    	 
    	$timeParution = $this->date_parution->getTimestamp();
    	$timeEvergreen = $this->date_evergreen->getTimestamp();
    	$timeNow = $now->getTimestamp();
    	 
    	$this->nb_days_since_parution = ($timeNow - $timeParution) / (24 * 3600);
    	$this->nb_days_since_evergreen = ($timeNow - $timeEvergreen) / (24 * 3600);
    	 
    	$paths = $this->article->getUris();
    	if (!$paths) {
    		$this->page_avg_scroll_since_parution = 0;
    		$this->page_avg_scroll_since_evergreen = 0;
    		
    		$this->page_views_total = 0;
    		$this->page_time_total = 0;
    		
    		$this->page_views_per_day_since_parution = 0;
    		$this->page_time_per_day_since_parution = 0;
    		
    		$this->page_views_per_day_since_evergreen = 0;
    		$this->page_time_per_day_since_evergreen = 0;
    		
    		$this->page_views_per_day_between_parution_and_evergreen = 0;
    		$this->page_views_ratio_before_and_after_evergreen = 0;
    		
    		
    		
    		$this->date_calcul = $now;
    	}
    
    	$cbadrs = $cbadrMgr->getAllChartbeatArticleDayReportForPath($paths, array('orderBy' => array('day' => 'asc')));
    
    	$total_views = 0;
    	$total_time = 0;
    	$moy_scroll_cumule = 0;
    	
    	$total_views_between_parution_and_evergreen = 0;
    
    	$total_views_for_evergreen = 0;
    	$total_time_for_evergreen = 0;
    	$moy_scroll_cumule_for_evergreen = 0;
    
    	foreach ($cbadrs as $cbadr) {
    		if (false) $cbadr = new ChartbeatArticleDayReport();
    		$total_views += $cbadr->getPageViewsQuality(); // We only get quality datas.
    		$total_time += $cbadr->getPageTotalTime();
    		$moy_scroll_cumule += $cbadr->getPageViewsQuality() * $cbadr->getPageAvgScroll();
    		 
    		$day = $cbadr->getDay()->format('Y-m-d');
    		if ($day > $date_evergreen_str) {
    			$total_views_for_evergreen += $cbadr->getPageViewsQuality(); // We only get quality datas.
    			$total_time_for_evergreen += $cbadr->getPageTotalTime();
    			$moy_scroll_cumule_for_evergreen += $cbadr->getPageViewsQuality() * $cbadr->getPageAvgScroll();
    		} else {
    			$total_views_between_parution_and_evergreen += $cbadr->getPageViewsQuality(); // We only get quality datas.
    		}
    	}
    	 
    	$this->page_avg_scroll_since_parution = $total_views ? ($moy_scroll_cumule / $total_views) : 0;
    	$this->page_avg_scroll_since_evergreen = $total_views_for_evergreen ? ($moy_scroll_cumule_for_evergreen / $total_views_for_evergreen) : 0;
    	 
    	$this->page_views_total = $total_views;
    	$this->page_time_total = $total_time;
    	
    	$this->page_time_avg = $this->page_views_total ? $this->page_time_total / $this->page_views_total : 0;
    	$this->page_time_avg_on_words = sqrt($this->article->getNbWords()) ? $this->page_time_avg / sqrt($this->article->getNbWords()) : 0;
    
    	$this->page_views_per_day_since_parution = $this->nb_days_since_parution ? ($total_views / $this->nb_days_since_parution) : 0;
    	$this->page_time_per_day_since_parution = $this->nb_days_since_parution ? ($total_time / $this->nb_days_since_parution) : 0;
    	$this->page_average_time_since_parution = $this->page_views_per_day_since_parution ? ($this->page_time_per_day_since_parution / $this->page_views_per_day_since_parution) : 0;
    
    	$this->page_views_per_day_since_evergreen = $this->nb_days_since_evergreen ? ($total_views_for_evergreen / $this->nb_days_since_evergreen) : 0;
    	$this->page_time_per_day_since_evergreen = $this->nb_days_since_evergreen ? ($total_time_for_evergreen / $this->nb_days_since_evergreen) : 0;
    	$this->page_average_time_since_evergreen = $this->page_views_per_day_since_evergreen ? ($this->page_time_per_day_since_evergreen / $this->page_views_per_day_since_evergreen) : 0;
    	
    	$between_nb_days = $this->nb_days_since_parution < $nb_days_before_evergreen ? $this->nb_days_since_parution : $nb_days_before_evergreen;
    	$this->page_views_per_day_between_parution_and_evergreen = $between_nb_days ? ($total_views_between_parution_and_evergreen / $between_nb_days) : 0;
    	
    	$this->page_views_ratio_before_and_after_evergreen = $this->page_views_per_day_between_parution_and_evergreen ? ($this->page_views_per_day_since_evergreen / $this->page_views_per_day_between_parution_and_evergreen) : 0;
    	 
    	
    	//------------Durabilité (Coefficient GINI) indicator-------------------
    	$artDayReportMgr = ManagersManager::getManager()->getContainer()->get('seriel_chartbeat.cbadr_manager');
    	if (false) $artDayReportMgr= new ChartbeatArticleDayReportManager();
    	$paths = $this->article->getUris();
    	$day_start = $date_evergreen_str;
    	$day_end = new \DateTime();
    	$day_end = $day_end->format('Y-m-d');
    	$dataGini =	$artDayReportMgr->getDataForCalculGiniEvergreen($paths,$day_start,$day_end);
    	
    	$NBData = count($dataGini);
    	if ($NBData > 1) {
    		//coefficien gini = 1 - Sum( ((NbDay i) - (NbDay i-1) ) * (((NbPageView i) + (NbPageView i-1))) )
    		// i = class
    		$indicatorDurabilite= 0;
    		
    		$nbjourRatioCurrent= 0;
    		$pageviewRatioCurrent= 0;
    		for ($iterator = 0;$iterator<= $NBData-1; $iterator++) {
    			$nbjourRatioPrev = $nbjourRatioCurrent;
    			$pageviewRatioPrev = $pageviewRatioCurrent;
    			$nbjourRatioCurrent = $dataGini[$iterator]['nb_jour_ratio'];
    			$pageviewRatioCurrent = $dataGini[$iterator]['page_view_ratio'];
    			
    			$indicatorDurabilite += ($nbjourRatioCurrent - $nbjourRatioPrev) * ( $pageviewRatioCurrent+ $pageviewRatioPrev);
    		}
    		$indicatorDurabilite = abs(1 - $indicatorDurabilite);
    		//$indicatorDurabilite = 1 - Coefficient GINI but durabilite is the opposite value of GINI
    		$indicatorDurabilite = 1 -$indicatorDurabilite;
    		$this->setIndicatorDurabilite($indicatorDurabilite);
    	}else {
    		$this->setIndicatorDurabilite(0);
    	}
    	$this->date_calcul = $now;
    }
    
    public function calculate() {
    	$artMetricsMgr = ManagersManager::getManager()->getContainer()->get('seriel_chartbeat.article_metrics_manager');
    	if (false) $artMetricsMgr = new ChartbeatArticleMetricsManager();
    	$artDayReportMgr = ManagersManager::getManager()->getContainer()->get('seriel_chartbeat.cbadr_manager');
    	if (false) $artDayReportMgr= new ChartbeatArticleDayReportManager();
    	
    	// calculate value on 100.
    	
    	$general_moy_view = $artMetricsMgr->getGeneralMoyView();
    	$general_moy_time = $artMetricsMgr->getGeneralMoyTime();
    	
    	$this->metric_page_views = $general_moy_view ? ZombieUtils::getMarkOn100($this->page_views_per_day_since_parution, $general_moy_view) : 0;
    	$this->metric_reading_time = $general_moy_time ? ZombieUtils::getMarkOn100($this->page_average_time_since_parution, $general_moy_time) : 0;
    	

    	$general_moy_page_time = $artMetricsMgr->getGeneralPageTimeAvgOnWords();
    	$this->indicator_attention = $general_moy_page_time ? ZombieUtils::getMarkOn100($this->page_time_avg_on_words, $general_moy_page_time) : 0;

    	
    	$general_ratio_before_and_after_evergreen = $artMetricsMgr->getGeneralRatioViewBeforeAndAfterEvergreen();
    	if ($general_ratio_before_and_after_evergreen == 0 ) $general_ratio_before_and_after_evergreen = null;
    	$ratio_before_and_after_evergreen = $general_ratio_before_and_after_evergreen ? ZombieUtils::getMarkOn100($this->page_views_ratio_before_and_after_evergreen, $general_ratio_before_and_after_evergreen) : 0;
    	
    	$general_moy_view_evergreen = $artMetricsMgr->getGeneralMoyViewEvergreen();
    	$page_views_evergreen = $general_moy_view_evergreen ? ZombieUtils::getMarkOn100($this->page_views_per_day_since_evergreen, $general_moy_view_evergreen) : 0;
    	
    	$this->metric_evergreen = ((2 * $ratio_before_and_after_evergreen) + $page_views_evergreen) / 3;
    	
    	// median = 20 days.
    	$ratio_age = ZombieUtils::getMarkOn100($this->nb_days_since_evergreen, 20);
    	$metrics_orig = $this->metric_evergreen;
    	$this->metric_evergreen *= ($ratio_age) / 100;
    	$this->metric_evergreen = round($this->metric_evergreen,2);

    	//------------Durabilité indicator-------------------
    	$general_moy_durabilite = $artMetricsMgr->getGeneralMoyDurabilite();
    	$this->indicator_durabilite = $general_moy_durabilite? ZombieUtils::getMarkOn100($this->indicator_durabilite, $general_moy_durabilite) : 0;
    }

    /**
     * Set metricEvergreen
     *
     * @param string $metricEvergreen
     *
     * @return ChartbeatArticleMetrics
     */
    public function setMetricEvergreen($metricEvergreen)
    {
        $this->metric_evergreen = $metricEvergreen;

        return $this;
    }

    /**
     * Get metricEvergreen
     *
     * @return string
     * 
     * @SER\ListeProperty("metric_evergreen",labellogo="/images/metric_evergreen.png", label="getNameMetricEvergreen()", sort="number",class_supp="indicator", format="number", credential="view_indicator_temps", dbfield="metric_evergreen")
	 * @SER\ReportingDataProperty("metric_evergreen",labellogo="/images/metric_evergreen.png", label="getNameMetricEvergreen()", label_short="getNameMetricEvergreen()", format="number", credential="view_indicator_temps", moyenne=true) 
     * @SER\ReportingColRowProperty("metric_evergreen",labellogo="/images/metric_evergreen.png", label="getNameMetricEvergreen()", option="percent" , credential="view_indicator_temps")
     */
    public function getMetricEvergreen()
    {
        return $this->metric_evergreen;
    }
    
    public static function getNameMetricEvergreen()
    {
    	$ChartbeatMgr = ManagersManager::getManager()->getContainer()->get('seriel_chartbeat.manager');
    	if (false) $ChartbeatMgr= new ChartbeatManager();
    	return $ChartbeatMgr->getLabelIndicator('metric_evergreen');
    }

    /**
     * Set indicatorAttention
     *
     * @param string $indicatorAttention
     *
     * @return ChartbeatArticleMetrics
     */
    public function setIndicatorAttention($indicator_aAttention)
    {
    	$this->indicator_attention = $indicator_aAttention;
    	
    	return $this;
    }
    
    /**
     * Get metricEvergreen
     *
     * @return string
     *
     * @SER\ListeProperty("indicator_attention",labellogo="/images/indicator_attention.png", label="getNameIndicatorAttention()", sort="number",class_supp="indicator", format="number", credential="view_indicator_temps", dbfield="indicator_attention")
	 * @SER\ReportingDataProperty("indicator_attention",labellogo="/images/indicator_attention.png", label="getNameIndicatorAttention()", label_short="getNameIndicatorAttention()", format="number", credential="view_indicator_temps", moyenne=true) 
     * @SER\ReportingColRowProperty("indicator_attention",labellogo="/images/indicator_attention.png", label="getNameIndicatorAttention()", option="percent" , credential="view_indicator_temps")
     */
    public function getIndicatorAttention()
    {
    	return $this->indicator_attention;
    }

    public static function getNameIndicatorAttention()
    {
    	$ChartbeatMgr = ManagersManager::getManager()->getContainer()->get('seriel_chartbeat.manager');
    	if (false) $ChartbeatMgr= new ChartbeatManager();
    	return $ChartbeatMgr->getLabelIndicator('indicator_attention');
    }
    
    /**
     * Set indicatorDurabilite
     *
     * @param float $indicatorDurabilite
     *
     * @return ChartbeatArticleMetrics
     */
    public function setIndicatorDurabilite($indicatorDurabilite)
    {
    	$this->indicator_durabilite= $indicatorDurabilite;
    	
    	return $this;
    }
    
    /**
     * Get metricEvergreen
     *
     * @return float
     *
     * @SER\ListeProperty("indicator_durabilite",labellogo="/images/indicator_durabilite.png", label="getNameIndicatorDurabilite()", sort="number",class_supp="indicator", format="number", credential="view_indicator_temps", dbfield="indicator_durabilite")
	 * @SER\ReportingDataProperty("indicator_durabilite",labellogo="/images/indicator_durabilite.png", label="getNameIndicatorDurabilite()", label_short="getNameIndicatorDurabilite()", format="number", credential="view_indicator_temps", moyenne=true) 
     * @SER\ReportingColRowProperty("indicator_durabilite",labellogo="/images/indicator_durabilite.png", label="getNameIndicatorDurabilite()", option="percent" , credential="view_indicator_temps")
     */
    public function getIndicatorDurabilite()
    {
    	return $this->indicator_durabilite;
    }
    
    public static function getNameIndicatorDurabilite()
    {
    	$ChartbeatMgr = ManagersManager::getManager()->getContainer()->get('seriel_chartbeat.manager');
    	if (false) $ChartbeatMgr= new ChartbeatManager();
    	return $ChartbeatMgr->getLabelIndicator('indicator_durabilite');
    }
    
    
    /**
     * Set article
     *
     * @param \ZombieBundle\Entity\News\Article $article
     *
     * @return ChartbeatArticleMetrics
     */
    public function setArticle(\ZombieBundle\Entity\News\Article $article = null)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Set pageViewsPerDayBetweenParutionAndEvergreen
     *
     * @param integer $pageViewsPerDayBetweenParutionAndEvergreen
     *
     * @return ChartbeatArticleMetrics
     */
    public function setPageViewsPerDayBetweenParutionAndEvergreen($pageViewsPerDayBetweenParutionAndEvergreen)
    {
        $this->page_views_per_day_between_parution_and_evergreen = $pageViewsPerDayBetweenParutionAndEvergreen;

        return $this;
    }

    /**
     * Get pageViewsPerDayBetweenParutionAndEvergreen
     *
     * @return integer
     */
    public function getPageViewsPerDayBetweenParutionAndEvergreen()
    {
        return $this->page_views_per_day_between_parution_and_evergreen;
    }

    /**
     * Set pageViewsRatioBeforeAndAfterEvergreen
     *
     * @param integer $pageViewsRatioBeforeAndAfterEvergreen
     *
     * @return ChartbeatArticleMetrics
     */
    public function setPageViewsRatioBeforeAndAfterEvergreen($pageViewsRatioBeforeAndAfterEvergreen)
    {
        $this->page_views_ratio_before_and_after_evergreen = $pageViewsRatioBeforeAndAfterEvergreen;

        return $this;
    }

    /**
     * Get pageViewsRatioBeforeAndAfterEvergreen
     *
     * @return integer
     */
    public function getPageViewsRatioBeforeAndAfterEvergreen()
    {
        return $this->page_views_ratio_before_and_after_evergreen;
    }

    /**
     * Set pageAverageTimeSinceParution
     *
     * @param integer $pageAverageTimeSinceParution
     *
     * @return ChartbeatArticleMetrics
     */
    public function setPageAverageTimeSinceParution($pageAverageTimeSinceParution)
    {
        $this->page_average_time_since_parution = $pageAverageTimeSinceParution;

        return $this;
    }

    /**
     * Get pageAverageTimeSinceParution
     *
     * @return integer
     */
    public function getPageAverageTimeSinceParution()
    {
        return $this->page_average_time_since_parution;
    }

    /**
     * Set pageAverageTimeSinceEvergreen
     *
     * @param integer $pageAverageTimeSinceEvergreen
     *
     * @return ChartbeatArticleMetrics
     */
    public function setPageAverageTimeSinceEvergreen($pageAverageTimeSinceEvergreen)
    {
        $this->page_average_time_since_evergreen = $pageAverageTimeSinceEvergreen;

        return $this;
    }

    /**
     * Get pageAverageTimeSinceEvergreen
     *
     * @return integer
     */
    public function getPageAverageTimeSinceEvergreen()
    {
        return $this->page_average_time_since_evergreen;
    }
    
    public function getMetrics() {
    	
    	return array(
    			'page_views' => $this->metric_page_views,
    			'avg_time' => $this->metric_reading_time,
    			'evergreen' => $this->metric_evergreen
    		);
    }
    
    /************** ArticleMetrics Methods ***************/
    
    public function getAllMeasures() {
    	$measures = array();
    	
    	$measures['page_views'] = $this->getMeasure('page_views');
    	$measures['read_time_total'] = $this->getMeasure('read_time_total');
    	
    	return $measures;
    }
    public function getAllIndicators() {
    	$indicators = array();
    	
    	$indicators['evergreen'] = $this->getIndicator('evergreen');
    	$indicators['page_views'] = $this->getIndicator('page_views');
    	$indicators['read_time'] = $this->getIndicator('read_time');
    	$indicators['read_percent'] = $this->getIndicator('read_percent');
    	$indicators['attention'] = $this->getIndicator('attention');
    	$indicators['durabilite'] = $this->getIndicator('durabilite');
    	    	
    	return $indicators;
    }
    
    public static function getAllIdIndicators() {
    	$authChecker = SymfonyUtils::getAuthorizationChecker();
    	
    	$IDindicators = array();
    	$reader = ManagersManager::getManager()->getContainer()->get('annotation_reader');
    	$converter = new SerielListePropertyConverter($reader);
    	$fields = $converter->convert('Seriel\ChartbeatBundle\Entity\ChartbeatArticleMetrics');
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
    				if ( ((isset($cred) ) && ($authChecker->isGranted('ANY_RIGHT_ON[Seriel\ChartbeatBundle\Entity\ChartbeatArticleMetrics >> '.$cred.']'))) OR  ( $authChecker->isGranted($cred))  OR  ( ! isset($cred))) {
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
    	$fields = $converter->convert('Seriel\ChartbeatBundle\Entity\ChartbeatArticleMetrics');
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
    	
    	if ($measure== 'page_views') return $this->getPageViewsTotal();
    	if ($measure== 'read_time_total') return $this->getPageTimeTotal();
    	
    	return null;
    }
    public function getIndicator($indicator) {
    	$indicator = trim(strtolower($indicator));
    	
    	if (!$indicator) return null;
    	
    	if ($indicator == 'evergreen') return $this->getMetricEvergreen();
    	if ($indicator == 'page_views') return $this->getMetricPageViews();
    	if ($indicator == 'read_time') return $this->getMetricReadingTime();
    	if ($indicator == 'read_percent') return $this->getMetricReadPercent();
    	if ($indicator == 'attention') return $this->getIndicatorAttention();
    	if ($indicator == 'durabilite') return $this->getIndicatorDurabilite();
    	return null;
    }

    /**
     * Set pageTimeAvg
     *
     * @param integer $pageTimeAvg
     *
     * @return ChartbeatArticleMetrics
     */
    public function setPageTimeAvg($pageTimeAvg)
    {
        $this->page_time_avg = $pageTimeAvg;

        return $this;
    }

    /**
     * Get pageTimeAvg
     *
     * @return integer
     */
    public function getPageTimeAvg()
    {
        return $this->page_time_avg;
    }

    /**
     * Set pageTimeAvgOnWords
     *
     * @param integer $pageTimeAvgOnWords
     *
     * @return ChartbeatArticleMetrics
     */
    public function setPageTimeAvgOnWords($pageTimeAvgOnWords)
    {
        $this->page_time_avg_on_words = $pageTimeAvgOnWords;

        return $this;
    }

    /**
     * Get pageTimeAvgOnWords
     *
     * @return integer
     */
    public function getPageTimeAvgOnWords()
    {
        return $this->page_time_avg_on_words;
    }
}
