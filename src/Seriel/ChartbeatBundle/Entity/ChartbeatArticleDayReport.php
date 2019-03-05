<?php

namespace Seriel\ChartbeatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Annotation as SER;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;

/**
 * @ORM\Entity
 * @ORM\Table(name="chartbeat_article_day_report",options={"engine"="MyISAM"},indexes={@ORM\Index(name="day_cbadr_idx", columns={"day"}),@ORM\Index(name="path_cbadr_idx_idx", columns={"path"})})
 */
class ChartbeatArticleDayReport extends RootObject implements Listable
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="date", nullable=false)
	 */
	protected $day;
	
	/**
	 * @ORM\Column(type="string", length=300, unique=false, nullable=false)
	 */
	protected $path;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $page_avg_scroll;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $page_scroll_starts;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $page_avg_time;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $page_total_time;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $page_uniques;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $page_views;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $page_views_loyal;
	
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $page_views_quality;
	
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
	
	
	public static function updateFromArray(ChartbeatArticleDayReport $cbadr, $data, $opt1 = null, $opt2 = null, $opt3 = null, $opt4 = null
																, $opt5 = null, $opt6 = null, $opt7 = null, $opt8 = null, $opt9 = null) {
		if (!$data) return null;
		
		$data = (array)$data;
		
		$day = $data['day'];
		$path = $data['path'];
		$page_avg_scroll = $data['page_avg_scroll'];
		$page_scroll_starts = $data['page_scroll_starts'];
		$page_avg_time = $data['page_avg_time'];
		$page_total_time = $data['page_total_time'];
		$page_uniques = $data['page_uniques'];
		$page_views = $data['page_views'];
		$page_views_loyal = $data['page_views_loyal'];
		$page_views_quality = $data['page_views_quality'];
		
		$cbadr->setDay($day);
		$cbadr->setPath($path);
		$cbadr->setPageAvgScroll($page_avg_scroll);
		$cbadr->setPageScrollStarts($page_scroll_starts);
		$cbadr->setPageAvgTime($page_avg_time);
		$cbadr->setPageTotalTime($page_total_time);
		$cbadr->setPageUniques($page_uniques);
		$cbadr->setPageViews($page_views);
		$cbadr->setPageViewsLoyal($page_views_loyal);
		$cbadr->setPageViewsQuality($page_views_quality);
		
		$cbadr->setOption1($opt1);
		$cbadr->setOption2($opt2);
		$cbadr->setOption3($opt3);
		$cbadr->setOption4($opt4);
		$cbadr->setOption5($opt5);
		$cbadr->setOption6($opt6);
		$cbadr->setOption7($opt7);
		$cbadr->setOption8($opt8);
		$cbadr->setOption9($opt9);
		
		return $cbadr;
	}
	
	public static function createFromArray($data, $opt1 = null, $opt2 = null, $opt3 = null, $opt4 = null
									, $opt5 = null, $opt6 = null, $opt7 = null, $opt8 = null, $opt9 = null) {
		if (!$data) return null;
		$cbadr = new ChartbeatArticleDayReport();
		
		return self::updateFromArray($cbadr, $data, $opt1, $opt2, $opt3, $opt4, $opt5, $opt6, $opt7, $opt8, $opt9);
	}
	
	
	public function __construct()
	{
		parent::__construct();
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
     * Set day
     *
     * @param \DateTime $day
     *
     * @return ChartbeatArticleDayReport
     */
    public function setDay($day)
    {
    	if (is_string($day)) {
    		$day = \DateTime::createFromFormat('Y-m-d', $day);
    	}
        $this->day = $day;

        return $this;
    }

    /**
     * Get day
     *
     * @return \DateTime
     * 
     * @SER\ListeProperty("day", label="Jour", sort="date", format="date", dbfield="day")
     * @SER\ReportingColRowProperty("day", label="Jour", sort="date", format="date", option="date")
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
     * @return ChartbeatArticleDayReport
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     * 
     * @SER\ListeProperty("path", label="URI", sort="string", format="none", dbfield="path")
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set pageAvgScroll
     *
     * @param integer $pageAvgScroll
     *
     * @return ChartbeatArticleDayReport
     */
    public function setPageAvgScroll($pageAvgScroll)
    {
        $this->page_avg_scroll = $pageAvgScroll;

        return $this;
    }

    /**
     * Get pageAvgScroll
     *
     * @return integer
     * 
     * @SER\ListeProperty("page_avg_scroll", label="Scroll", sort="number", format="none", dbfield="page_avg_scroll")
     */
    public function getPageAvgScroll()
    {
        return $this->page_avg_scroll;
    }

    /**
     * Set pageScrollStarts
     *
     * @param integer $pageScrollStarts
     *
     * @return ChartbeatArticleDayReport
     */
    public function setPageScrollStarts($pageScrollStarts)
    {
        $this->page_scroll_starts = $pageScrollStarts;

        return $this;
    }

    /**
     * Get pageScrollStarts
     *
     * @return integer
     */
    public function getPageScrollStarts()
    {
        return $this->page_scroll_starts;
    }

    /**
     * Set pageAvgTime
     *
     * @param integer $pageAvgTime
     *
     * @return ChartbeatArticleDayReport
     */
    public function setPageAvgTime($pageAvgTime)
    {
        $this->page_avg_time = $pageAvgTime;

        return $this;
    }

    /**
     * Get pageAvgTime
     *
     * @return integer
     * 
     * @SER\ListeProperty("page_avg_time", label="Avg Time", sort="number", format="none", dbfield="page_avg_time")
     */
    public function getPageAvgTime()
    {
        return $this->page_avg_time;
    }

    /**
     * Set pageTotalTime
     *
     * @param integer $pageTotalTime
     *
     * @return ChartbeatArticleDayReport
     */
    public function setPageTotalTime($pageTotalTime)
    {
        $this->page_total_time = $pageTotalTime;

        return $this;
    }

    /**
     * Get pageTotalTime
     *
     * @return integer
     * 
     * @SER\ListeProperty("page_total_time", label="Tot. Time", sort="number", format="none", dbfield="page_total_time")
     */
    public function getPageTotalTime()
    {
        return $this->page_total_time;
    }

    /**
     * Set pageUniques
     *
     * @param integer $pageUniques
     *
     * @return ChartbeatArticleDayReport
     */
    public function setPageUniques($pageUniques)
    {
        $this->page_uniques = $pageUniques;

        return $this;
    }

    /**
     * Get pageUniques
     *
     * @return integer
     * 
     * @SER\ListeProperty("page_uniques", label="page uniques", sort="number", format="none", dbfield="page_uniques")
     */
    public function getPageUniques()
    {
        return $this->page_uniques;
    }

    /**
     * Set pageViews
     *
     * @param integer $pageViews
     *
     * @return ChartbeatArticleDayReport
     */
    public function setPageViews($pageViews)
    {
        $this->page_views = $pageViews;

        return $this;
    }

    /**
     * Get pageViews
     *
     * @return integer
     * 
     * @SER\ListeProperty("page_views", label="page views", sort="number", format="none", dbfield="page_views")
     */
    public function getPageViews()
    {
        return $this->page_views;
    }

    /**
     * Set pageViewsLoyal
     *
     * @param integer $pageViewsLoyal
     *
     * @return ChartbeatArticleDayReport
     */
    public function setPageViewsLoyal($pageViewsLoyal)
    {
        $this->page_views_loyal = $pageViewsLoyal;

        return $this;
    }

    /**
     * Get pageViewsLoyal
     *
     * @return integer
     * 
     * @SER\ListeProperty("page_views_loyal", label="page loyal", sort="number", format="none", dbfield="page_views_loyal")
     */
    public function getPageViewsLoyal()
    {
        return $this->page_views_loyal;
    }

    /**
     * Set pageViewsQuality
     *
     * @param integer $pageViewsQuality
     *
     * @return ChartbeatArticleDayReport
     */
    public function setPageViewsQuality($pageViewsQuality)
    {
        $this->page_views_quality = $pageViewsQuality;

        return $this;
    }

    /**
     * Get pageViewsQuality
     *
     * @return integer
     * 
     * @SER\ListeProperty("page_views_quality", label="page quality", sort="number", format="none", dbfield="page_views_quality")
     */
    public function getPageViewsQuality()
    {
        return $this->page_views_quality;
    }


    /**
     * Set option1
     *
     * @param string $option1
     *
     * @return ChartbeatArticleDayReport
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
     * @return ChartbeatArticleDayReport
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
     * @return ChartbeatArticleDayReport
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
     * @return ChartbeatArticleDayReport
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
     * @return ChartbeatArticleDayReport
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
     * @return ChartbeatArticleDayReport
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
     * @return ChartbeatArticleDayReport
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
     * @return ChartbeatArticleDayReport
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
     * @return ChartbeatArticleDayReport
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
}
