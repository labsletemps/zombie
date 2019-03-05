<?php

namespace Seriel\GoogleAnalyticsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Seriel\AppliToolboxBundle\Annotation as SER;

/**
 * DayReportEntrance
 *
 * @ORM\Entity
 * @ORM\Table(name="day_report_entrance",options={"engine"="MyISAM"} ,indexes={@ORM\Index(name="path_ga_dayreportentrance_idx", columns={"path"})} ,uniqueConstraints={@UniqueConstraint(name="unique_dayreportentrance_idx", columns={"dayreport_id", "path"})} )
 * @UniqueEntity(fields={"dayreport", "path"})
 */
class DayReportEntrance  extends RootObject implements Listable
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
     * @ORM\Column(name="path", type="string", length=330)
     */
    private $path;

    /**
     * @var int
     *
     * @ORM\Column(name="count", type="integer")
     */
    private $count;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="day", type="date")
     */
    private $day;
    
    /**
     * @ORM\ManyToOne(targetEntity="Seriel\GoogleAnalyticsBundle\Entity\GoogleAnalyticsDayReport", inversedBy="sourceEntrances")
     * @ORM\JoinColumn(name="dayreport_id", referencedColumnName="id")
     **/
    private $dayreport;
    
    public function __construct()
    {
    	parent::__construct();
    	$this->day = new \DateTime();
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
     * Set path
     *
     * @param string $path
     *
     * @return DayReportEntrance
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
     * Set count
     *
     * @param integer $count
     *
     * @return DayReportEntrance
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Add count
     *
     * @param integer $count
     *
     * @return DayReportEntrance
     */
    public function addCount($count)
    {
    	$this->count += $count;
    	
    	return $this;
    }
    
    /**
     * Get count
     *
     * @SER\ListeProperty("count", label="Count", sort="number", format="none", dbfield="count")
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }
    
    /**
     * Set day
     *
     * @param \DateTime $day
     *
     * @return DayReportEntrance
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
     * Set dayreport
     *
     * @param \Seriel\GoogleAnalyticsBundle\Entity\GoogleAnalyticsDayReport
     *
     * @return dayreport
     */
    public function setDayreport(\Seriel\GoogleAnalyticsBundle\Entity\GoogleAnalyticsDayReport $dayreport)
    {
    	$this->dayreport= $dayreport;
    	
    	return $this;
    }
    
    /**
     * Get dayreport
     *
     * @return \Seriel\GoogleAnalyticsBundle\Entity\GoogleAnalyticsDayReport
     */
    public function getDayreport()
    {
    	return $this->dayreport;
    }
    
}

