<?php

namespace Seriel\CrossIndicatorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Seriel\AppliToolboxBundle\Annotation as SER;
use Seriel\AppliToolboxBundle\Entity\Listable;
use Seriel\AppliToolboxBundle\Entity\RootObject;
use Seriel\AppliToolboxBundle\Managers\ManagersManager;
use Seriel\AppliToolboxBundle\Annotation\SerielListePropertyConverter;
use ZombieBundle\API\Entity\ArticleCrossMetrics;
use ZombieBundle\Utils\ZombieUtils;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;

/**
 * CrossIndicatorArticle
 *
 * @ORM\Entity
 * @ORM\Table(name="cross_indicator_article",options={"engine"="MyISAM"})
 */
class CrossIndicatorArticle extends RootObject implements ArticleCrossMetrics, Listable
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
     * @ORM\Column(name="indicator1", type="float", nullable=true)
     */
    private $indicator1;
    
    /**
     * @var int
     *
     * @ORM\Column(name="indicator2", type="float", nullable=true)
     */
    private $indicator2;
    
    /**
     * @var int
     *
     * @ORM\Column(name="indicator3", type="float", nullable=true)
     */
    private $indicator3;
    
    /**
     * @var int
     *
     * @ORM\Column(name="indicator4", type="float", nullable=true)
     */
    private $indicator4;
    
    /**
     * @var int
     *
     * @ORM\Column(name="indicator5", type="float", nullable=true)
     */
    private $indicator5;
    
    /**
     * @var int
     *
     * @ORM\Column(name="indicator6", type="float", nullable=true)
     */
    private $indicator6;
    
    /**
     * @var int
     *
     * @ORM\Column(name="indicator7", type="float", nullable=true)
     */
    private $indicator7;
    
    /**
     * @var int
     *
     * @ORM\Column(name="indicator8", type="float", nullable=true)
     */
    private $indicator8;
    
    /**
     * @var int
     *
     * @ORM\Column(name="indicator9", type="float", nullable=true)
     */
    private $indicator9;
    
    /**
     * @var int
     *
     * @ORM\Column(name="indicator10", type="float", nullable=true)
     */
    private $indicator10;
    
    /**
     * @var int
     *
     * @ORM\Column(name="indicator11", type="float", nullable=true)
     */
    private $indicator11;
    
    /**
     * @var int
     *
     * @ORM\Column(name="indicator12", type="float", nullable=true)
     */
    private $indicator12;
    
    /**
     * @var int
     *
     * @ORM\Column(name="indicator13", type="float", nullable=true)
     */
    private $indicator13;
    
    /**
     * @var int
     *
     * @ORM\Column(name="indicator14", type="float", nullable=true)
     */
    private $indicator14;
    
    /**
     * @var int
     *
     * @ORM\Column(name="indicator15", type="float", nullable=true)
     */
    private $indicator15;

    /**
     * @var int
     *
     * @ORM\Column(name="global_note_indicator", type="float", nullable=true)
     */
    private $globalNoteIndicator;
    
    public function __construct(\ZombieBundle\Entity\News\Article $article)
    {
    	parent::__construct();
    	$this->article = $article;
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
     * @return CrossIndicatorArticle
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
     * @return CrossIndicatorArticle
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
     * Set globalNoteIndicator
     *
     * @param integer $globalNoteIndicator
     *
     * @return CrossIndicatorArticle
     */
    public function setGlobalNoteIndicator($globalNoteIndicator)
    {
    	$this->globalNoteIndicator= $globalNoteIndicator;
    	
    	return $this;
    }
    
    /**
     * Get globalNoteIndicator
     * @SER\ListeProperty("globalNoteIndicator",labellogo="/images/zombie.png", label="Global Note", sort="number",class_supp="indicator", format="number", credential="view_indicator_temps", dbfield="globalNoteIndicator")
	 * @SER\ReportingDataProperty("globalNoteIndicator",labellogo="/images/zombie.png", label="Global Note", label_short="G. Note", format="number", credential="view_indicator_temps", moyenne=true) 
     * @SER\ReportingColRowProperty("globalNoteIndicator",labellogo="/images/zombie.png", label="Global Note", option="percent" , credential="view_indicator")
     * @return integer
     */
    public function getGlobalNoteIndicator()
    {
    	return $this->globalNoteIndicator;
    }

    
    /*
     * CALCUL indicator
     */    
    public function calculGeneralNoteIndicator($indicatorsMetrics) {
    	$indicatorValue= 0 ;
    	$NBindicator= 0;
    	
    	foreach ($indicatorsMetrics as $indicator) {
    		if(isset($indicator)) {
    			$indicatorValue+= $indicator;
    			$NBindicator++;
    		}
    	}
    	if($NBindicator > 0)  $indicatorValue= $indicatorValue/ $NBindicator;
    	$indicatorValue= round($indicatorValue, 2);
    	$this->setGlobalNoteIndicator($indicatorValue);
    }
    
    //Pre-Calcul All indicator with measure in module metrics
    public function precalculIndicator() {
    	
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	
    	$indicatorsMetrics = $CrossMgr->getAllIndicators($this->article);
    	$measuresMetrics = $CrossMgr->getAllMeasures($this->article);
    	
    	//indicators
    	$value = $CrossMgr->getValueIndicator('indicator1', $indicatorsMetrics,$measuresMetrics);
    	$this->setIndicator1($value);
    	$value = $CrossMgr->getValueIndicator('indicator2', $indicatorsMetrics,$measuresMetrics);
    	$this->setIndicator2($value);
    	$value = $CrossMgr->getValueIndicator('indicator3', $indicatorsMetrics,$measuresMetrics);
    	$this->setIndicator3($value);
    	$value = $CrossMgr->getValueIndicator('indicator4', $indicatorsMetrics,$measuresMetrics);
    	$this->setIndicator4($value);
    	$value = $CrossMgr->getValueIndicator('indicator5', $indicatorsMetrics,$measuresMetrics);
    	$this->setIndicator5($value);
    	$value = $CrossMgr->getValueIndicator('indicator6', $indicatorsMetrics,$measuresMetrics);
    	$this->setIndicator6($value);
    	$value = $CrossMgr->getValueIndicator('indicator7', $indicatorsMetrics,$measuresMetrics);
    	$this->setIndicator7($value);
    	$value = $CrossMgr->getValueIndicator('indicator8', $indicatorsMetrics,$measuresMetrics);
    	$this->setIndicator8($value);
    	$value = $CrossMgr->getValueIndicator('indicator9', $indicatorsMetrics,$measuresMetrics);
    	$this->setIndicator9($value);
    	$value = $CrossMgr->getValueIndicator('indicator10', $indicatorsMetrics,$measuresMetrics);
    	$this->setIndicator10($value);
    	$value = $CrossMgr->getValueIndicator('indicator11', $indicatorsMetrics,$measuresMetrics);
    	$this->setIndicator11($value);
    	$value = $CrossMgr->getValueIndicator('indicator12', $indicatorsMetrics,$measuresMetrics);
    	$this->setIndicator12($value);
    	$value = $CrossMgr->getValueIndicator('indicator13', $indicatorsMetrics,$measuresMetrics);
    	$this->setIndicator13($value);
    	$value = $CrossMgr->getValueIndicator('indicator14', $indicatorsMetrics,$measuresMetrics);
    	$this->setIndicator14($value);
    	$value = $CrossMgr->getValueIndicator('indicator15', $indicatorsMetrics,$measuresMetrics);
    	$this->setIndicator15($value);
    	
    	return $this;
    }
  
    //Calcul All indicator with measure in module metrics
    public function calculIndicator() {
  
    	$CrossIndicatorArticleMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.article_metrics_manager');
    	if (false) $CrossIndicatorArticleMgr= new CrossIndicatorArticleManager();
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	
    	$indicatorsMetrics = $CrossMgr->getAllIndicators($this->article);
    	
		//indicators
    	$avg_indicator1 = $CrossIndicatorArticleMgr->getAvgIndicatorView('indicator1');
    	$this->indicator1 = $avg_indicator1? ZombieUtils::getMarkOn100($this->indicator1, $avg_indicator1) : 0;

    	$avg_indicator2 = $CrossIndicatorArticleMgr->getAvgIndicatorView('indicator2');
    	$this->indicator2 = $avg_indicator2? ZombieUtils::getMarkOn100($this->indicator2, $avg_indicator2) : 0;
    	
    	$avg_indicator3 = $CrossIndicatorArticleMgr->getAvgIndicatorView('indicator3');
    	$this->indicator3 = $avg_indicator3? ZombieUtils::getMarkOn100($this->indicator3, $avg_indicator3) : 0;
    	
    	$avg_indicator4 = $CrossIndicatorArticleMgr->getAvgIndicatorView('indicator4');
    	$this->indicator4 = $avg_indicator4? ZombieUtils::getMarkOn100($this->indicator4, $avg_indicator4) : 0;
    	
    	$avg_indicator5 = $CrossIndicatorArticleMgr->getAvgIndicatorView('indicator5');
    	$this->indicator5 = $avg_indicator5? ZombieUtils::getMarkOn100($this->indicator5, $avg_indicator5) : 0;

    	$avg_indicator6 = $CrossIndicatorArticleMgr->getAvgIndicatorView('indicator6');
    	$this->indicator6 = $avg_indicator6? ZombieUtils::getMarkOn100($this->indicator6, $avg_indicator6) : 0;
    	
    	$avg_indicator7 = $CrossIndicatorArticleMgr->getAvgIndicatorView('indicator7');
    	$this->indicator7 = $avg_indicator7? ZombieUtils::getMarkOn100($this->indicator7, $avg_indicator7) : 0;
    	
    	$avg_indicator8 = $CrossIndicatorArticleMgr->getAvgIndicatorView('indicator8');
    	$this->indicator8 = $avg_indicator8? ZombieUtils::getMarkOn100($this->indicator8, $avg_indicator8) : 0;
    	
    	$avg_indicator9 = $CrossIndicatorArticleMgr->getAvgIndicatorView('indicator9');
    	$this->indicator9 = $avg_indicator9? ZombieUtils::getMarkOn100($this->indicator9, $avg_indicator9) : 0;
    	
    	$avg_indicator10 = $CrossIndicatorArticleMgr->getAvgIndicatorView('indicator10');
    	$this->indicator10 = $avg_indicator10? ZombieUtils::getMarkOn100($this->indicator10, $avg_indicator10) : 0;
    	
    	$avg_indicator11 = $CrossIndicatorArticleMgr->getAvgIndicatorView('indicator11');
    	$this->indicator11 = $avg_indicator11? ZombieUtils::getMarkOn100($this->indicator11, $avg_indicator11) : 0;
    	
    	$avg_indicator12 = $CrossIndicatorArticleMgr->getAvgIndicatorView('indicator12');
    	$this->indicator12 = $avg_indicator12? ZombieUtils::getMarkOn100($this->indicator12, $avg_indicator12) : 0;
    	
    	$avg_indicator13 = $CrossIndicatorArticleMgr->getAvgIndicatorView('indicator13');
    	$this->indicator13 = $avg_indicator13? ZombieUtils::getMarkOn100($this->indicator13, $avg_indicator13) : 0;
    	
    	$avg_indicator14 = $CrossIndicatorArticleMgr->getAvgIndicatorView('indicator14');
    	$this->indicator14 = $avg_indicator14? ZombieUtils::getMarkOn100($this->indicator14, $avg_indicator14) : 0;
    	
    	$avg_indicator15 = $CrossIndicatorArticleMgr->getAvgIndicatorView('indicator15');
    	$this->indicator15 = $avg_indicator15? ZombieUtils::getMarkOn100($this->indicator15, $avg_indicator15) : 0;
    	
    	$this->calculGeneralNoteIndicator($indicatorsMetrics);
    	
    	return $this;
    }
    
    /************** ArticleCrossMetrics Methods ***************/
    
    public function getAllIndicators() {
    	
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	
    	$indicators = array();
    	$indicators['generalNote'] = $this->getIndicator('generalNote');
    	
    	$label1 = $CrossMgr->getLabelIndicator('indicator1');
    	$indicators[$label1] = $this->getIndicator($label1);
    	
    	$label2 = $CrossMgr->getLabelIndicator('indicator2');
    	$indicators[$label2] = $this->getIndicator($label2);
    	
    	$label3 = $CrossMgr->getLabelIndicator('indicator3');
    	$indicators[$label3] = $this->getIndicator($label3);
    	
    	$label4 = $CrossMgr->getLabelIndicator('indicator4');
    	$indicators[$label4] = $this->getIndicator($label4);
    	
    	$label5 = $CrossMgr->getLabelIndicator('indicator5');
    	$indicators[$label5] = $this->getIndicator($label5);
  
    	$label6 = $CrossMgr->getLabelIndicator('indicator6');
    	$indicators[$label6] = $this->getIndicator($label6);
    	
    	$label7 = $CrossMgr->getLabelIndicator('indicator7');
    	$indicators[$label7] = $this->getIndicator($label7);
    	
    	$label8 = $CrossMgr->getLabelIndicator('indicator8');
    	$indicators[$label8] = $this->getIndicator($label8);
    	
    	$label9 = $CrossMgr->getLabelIndicator('indicator9');
    	$indicators[$label9] = $this->getIndicator($label9);
    	
    	$label10 = $CrossMgr->getLabelIndicator('indicator10');
    	$indicators[$label10] = $this->getIndicator($label10);
    	
    	$label11 = $CrossMgr->getLabelIndicator('indicator11');
    	$indicators[$label11] = $this->getIndicator($label11);
    	
    	$label12 = $CrossMgr->getLabelIndicator('indicator12');
    	$indicators[$label12] = $this->getIndicator($label12);
    	
    	$label13 = $CrossMgr->getLabelIndicator('indicator13');
    	$indicators[$label13] = $this->getIndicator($label13);
    	
    	$label14 = $CrossMgr->getLabelIndicator('indicator14');
    	$indicators[$label14] = $this->getIndicator($label14);
    	
    	$label15 = $CrossMgr->getLabelIndicator('indicator15');
    	$indicators[$label15] = $this->getIndicator($label15);
    	
    	return $indicators;
    }
    public static function getAllIdIndicators() {
    	$authChecker = SymfonyUtils::getAuthorizationChecker();
    	$IDindicators = array();
    	$reader = ManagersManager::getManager()->getContainer()->get('annotation_reader');
    	$converter = new SerielListePropertyConverter($reader);
    	$fields = $converter->convert('Seriel\CrossIndicatorBundle\Entity\CrossIndicatorArticle');
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
    				if ( ((isset($cred) ) && ($authChecker->isGranted('ANY_RIGHT_ON[Seriel\CrossIndicatorBundle\Entity\CrossIndicatorArticle >> '.$cred.']'))) OR  ( $authChecker->isGranted($cred))  OR  ( ! isset($cred))) {
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
    	$fields = $converter->convert('Seriel\CrossIndicatorBundle\Entity\CrossIndicatorArticle');
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
    public function getIndicator($indicator) {
    	$indicator = trim(strtolower($indicator));
    	
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	
    	if (!$indicator) return null;
    	if ($indicator == 'generalNote') return $this->globalNoteIndicator;
    	
    	$label1 = $CrossMgr->getLabelIndicator('indicator1');
    	if ($indicator == $label1) return $this->indicator1;

    	$label2 = $CrossMgr->getLabelIndicator('indicator2');
    	if ($indicator == $label2) return $this->indicator2;
    	
    	$label3 = $CrossMgr->getLabelIndicator('indicator3');
    	if ($indicator == $label3) return $this->indicator3;

    	$label4 = $CrossMgr->getLabelIndicator('indicator4');
    	if ($indicator == $label4) return $this->indicator4;
    	
    	$label5 = $CrossMgr->getLabelIndicator('indicator5');
    	if ($indicator == $label5) return $this->indicator5;
    	
    	$label6 = $CrossMgr->getLabelIndicator('indicator6');
    	if ($indicator == $label6) return $this->indicator6;
    	
    	$label7 = $CrossMgr->getLabelIndicator('indicator7');
    	if ($indicator == $label7) return $this->indicator7;
    	
    	$label8 = $CrossMgr->getLabelIndicator('indicator8');
    	if ($indicator == $label8) return $this->indicator8;
    	
    	$label9 = $CrossMgr->getLabelIndicator('indicator9');
    	if ($indicator == $label9) return $this->indicator9;
    	
    	$label10 = $CrossMgr->getLabelIndicator('indicator10');
    	if ($indicator == $label10) return $this->indicator10;
    	
    	$label11 = $CrossMgr->getLabelIndicator('indicator11');
    	if ($indicator == $label11) return $this->indicator11;
    	
    	$label12 = $CrossMgr->getLabelIndicator('indicator12');
    	if ($indicator == $label12) return $this->indicator12;
    	
    	$label13 = $CrossMgr->getLabelIndicator('indicator13');
    	if ($indicator == $label13) return $this->indicator13;
    	
    	$label14 = $CrossMgr->getLabelIndicator('indicator14');
    	if ($indicator == $label14) return $this->indicator14;
    	
    	$label15 = $CrossMgr->getLabelIndicator('indicator15');
    	if ($indicator == $label15) return $this->indicator15;
    	
    	return null;
    }

    /**
     * Set indicator1
     *
     * @param float $indicator1
     *
     * @return CrossIndicatorArticle
     */
    public function setIndicator1($indicator1)
    {
        $this->indicator1 = $indicator1;

        return $this;
    }

    /**
     * Get indicator1
     * @SER\ListeProperty("indicator1",labellogo="getLogoIndicator1()", label="getNameIndicator1()", sort="number",class_supp="indicator", format="number", credential="view_indicator_parametre_1", dbfield="indicator1")
	 * @SER\ReportingDataProperty("indicator1",labellogo="getLogoIndicator1()", label="getNameIndicator1()", label_short="getNameIndicator1()", format="number", credential="view_indicator_parametre_1", moyenne=true) 
     * @SER\ReportingColRowProperty("indicator1",labellogo="getLogoIndicator1()", label="getNameIndicator1()", option="percent" , credential="view_indicator_parametre_1")
     * @return float
     */
    public function getIndicator1()
    {
        return $this->indicator1;
    }

    public static function getNameIndicator1()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLabelIndicator('indicator1');
    }
 
    public static function getLogoIndicator1()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLogoIndicator('indicator1');
    }
    
    /**
     * Set indicator2
     *
     * @param float $indicator2
     *
     * @return CrossIndicatorArticle
     */
    public function setIndicator2($indicator2)
    {
        $this->indicator2 = $indicator2;

        return $this;
    }

    /**
     * Get indicator2
     * @SER\ListeProperty("indicator2",labellogo="getLogoIndicator2()", label="getNameIndicator2()", sort="number",class_supp="indicator", format="number", credential="view_indicator_parametre_2", dbfield="indicator2")
	 * @SER\ReportingDataProperty("indicator2",labellogo="getLogoIndicator2()", label="getNameIndicator2()", label_short="getNameIndicator2()", format="number", credential="view_indicator_parametre_2", moyenne=true) 
     * @SER\ReportingColRowProperty("indicator2",labellogo="getLogoIndicator2()", label="getNameIndicator2()", option="percent" , credential="view_indicator_parametre_2")
     * @return float
     */
    public function getIndicator2()
    {
        return $this->indicator2;
    }

    public static function getNameIndicator2()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLabelIndicator('indicator2');
    }

    public static function getLogoIndicator2()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLogoIndicator('indicator2');
    }
    
    /**
     * Set indicator3
     *
     * @param float $indicator3
     *
     * @return CrossIndicatorArticle
     */
    public function setIndicator3($indicator3)
    {
        $this->indicator3 = $indicator3;

        return $this;
    }

    /**
     * Get indicator3
     * @SER\ListeProperty("indicator3",labellogo="getLogoIndicator3()", label="getNameIndicator3()", sort="number",class_supp="indicator", format="number", credential="view_indicator_parametre_3", dbfield="indicator3")
	 * @SER\ReportingDataProperty("indicator3",labellogo="getLogoIndicator3()", label="getNameIndicator3()", label_short="getNameIndicator3()", format="number", credential="view_indicator_parametre_3", moyenne=true) 
     * @SER\ReportingColRowProperty("indicator3",labellogo="getLogoIndicator3()", label="getNameIndicator3()", option="percent" , credential="view_indicator_parametre_3")     
     * @return float
     */
    public function getIndicator3()
    {
        return $this->indicator3;
    }
    
    public static function getNameIndicator3()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLabelIndicator('indicator3');
    }

    public static function getLogoIndicator3()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLogoIndicator('indicator3');
    }
    /**
     * Set indicator4
     *
     * @param float $indicator4
     *
     * @return CrossIndicatorArticle
     */
    public function setIndicator4($indicator4)
    {
        $this->indicator4 = $indicator4;

        return $this;
    }

    /**
     * Get indicator4
     * @SER\ListeProperty("indicator4",labellogo="getLogoIndicator4()", label="getNameIndicator4()", sort="number",class_supp="indicator", format="number", credential="view_indicator_parametre_4", dbfield="indicator4")
	 * @SER\ReportingDataProperty("indicator4",labellogo="getLogoIndicator4()", label="getNameIndicator4()", label_short="getNameIndicator4()", format="number", credential="view_indicator_parametre_4", moyenne=true) 
     * @SER\ReportingColRowProperty("indicator4",labellogo="getLogoIndicator4()", label="getNameIndicator4()", option="percent" , credential="view_indicator_parametre_4")
     * @return float
     */
    public function getIndicator4()
    {
        return $this->indicator4;
    }
    
    public static function getNameIndicator4()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLabelIndicator('indicator4');
    }
    public static function getLogoIndicator4()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLogoIndicator('indicator4');
    }
    /**
     * Set indicator5
     *
     * @param float $indicator5
     *
     * @return CrossIndicatorArticle
     */
    public function setIndicator5($indicator5)
    {
        $this->indicator5 = $indicator5;

        return $this;
    }

    /**
     * Get indicator5
     *
     * @SER\ListeProperty("indicator5",labellogo="getLogoIndicator5()", label="getNameIndicator5()", sort="number",class_supp="indicator", format="number", credential="view_indicator_parametre_5", dbfield="indicator5")
	 * @SER\ReportingDataProperty("indicator5",labellogo="getLogoIndicator5()", label="getNameIndicator5()", label_short="getNameIndicator5()", format="number", credential="view_indicator_parametre_5", moyenne=true) 
     * @SER\ReportingColRowProperty("indicator5",labellogo="getLogoIndicator5()", label="getNameIndicator5()", option="percent" , credential="view_indicator_parametre_5")
     * @return float
     */
    public function getIndicator5()
    {
        return $this->indicator5;
    }

    public static function getNameIndicator5()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLabelIndicator('indicator5');
    }
    
    public static function getLogoIndicator5()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLogoIndicator('indicator5');
    }
    
    /**
     * Set indicator6
     *
     * @param float $indicator6
     *
     * @return CrossIndicatorArticle
     */
    public function setIndicator6($indicator6)
    {
        $this->indicator6 = $indicator6;

        return $this;
    }

    /**
     * Get indicator6
     * @SER\ListeProperty("indicator6",labellogo="getLogoIndicator6()", label="getNameIndicator6()", sort="number",class_supp="indicator", format="number", credential="view_indicator_parametre_6", dbfield="indicator6")
	 * @SER\ReportingDataProperty("indicator6",labellogo="getLogoIndicator6()", label="getNameIndicator6()", label_short="getNameIndicator6()", format="number", credential="view_indicator_parametre_6", moyenne=true) 
     * @SER\ReportingColRowProperty("indicator6",labellogo="getLogoIndicator6()", label="getNameIndicator6()", option="percent" , credential="view_indicator_parametre_6")     
     * @return float
     */
    public function getIndicator6()
    {
        return $this->indicator6;
    }

    public static function getNameIndicator6()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLabelIndicator('indicator6');
    }
    
    public static function getLogoIndicator6()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLogoIndicator('indicator6');
    }
    /**
     * Set indicator7
     *
     * @param float $indicator7
     *
     * @return CrossIndicatorArticle
     */
    public function setIndicator7($indicator7)
    {
        $this->indicator7 = $indicator7;

        return $this;
    }

    /**
     * Get indicator7
     * @SER\ListeProperty("indicator7",labellogo="getLogoIndicator7()", label="getNameIndicator7()", sort="number",class_supp="indicator", format="number", credential="view_indicator_parametre_7", dbfield="indicator7")
	 * @SER\ReportingDataProperty("indicator7",labellogo="getLogoIndicator7()", label="getNameIndicator7()", label_short="getNameIndicator7()", format="number", credential="view_indicator_parametre_7", moyenne=true) 
     * @SER\ReportingColRowProperty("indicator7",labellogo="getLogoIndicator7()", label="getNameIndicator7()", option="percent" , credential="view_indicator_parametre_7")     
     * @return float
     */
    public function getIndicator7()
    {
        return $this->indicator7;
    }

    public static function getNameIndicator7()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLabelIndicator('indicator7');
    }

    public static function getLogoIndicator7()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLogoIndicator('indicator7');
    }
    
    /**
     * Set indicator8
     *
     * @param float $indicator8
     *
     * @return CrossIndicatorArticle
     */
    public function setIndicator8($indicator8)
    {
        $this->indicator8 = $indicator8;

        return $this;
    }

    /**
     * Get indicator8
     * @SER\ListeProperty("indicator8",labellogo="getLogoIndicator8()", label="getNameIndicator8()", sort="number",class_supp="indicator", format="number", credential="view_indicator_parametre_8", dbfield="indicator8")
	 * @SER\ReportingDataProperty("indicator8",labellogo="getLogoIndicator8()", label="getNameIndicator8()", label_short="getNameIndicator8()", format="number", credential="view_indicator_parametre_8", moyenne=true) 
     * @SER\ReportingColRowProperty("indicator8",labellogo="getLogoIndicator8()", label="getNameIndicator8()", option="percent" , credential="view_indicator_parametre_8")
     * @return float
     */
    public function getIndicator8()
    {
        return $this->indicator8;
    }
    
    public static function getNameIndicator8()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLabelIndicator('indicator8');
    }

    public static function getLogoIndicator8()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLogoIndicator('indicator8');
    }
    
    /**
     * Set indicator9
     *
     * @param float $indicator9
     *
     * @return CrossIndicatorArticle
     */
    public function setIndicator9($indicator9)
    {
        $this->indicator9 = $indicator9;

        return $this;
    }

    /**
     * Get indicator9
     * @SER\ListeProperty("indicator9",labellogo="getLogoIndicator9()", label="getNameIndicator9()", sort="number",class_supp="indicator", format="number", credential="view_indicator_parametre_9", dbfield="indicator9")
	 * @SER\ReportingDataProperty("indicator9",labellogo="getLogoIndicator9()", label="getNameIndicator9()", label_short="getNameIndicator9()", format="number", credential="view_indicator_parametre_9", moyenne=true)      
     * @SER\ReportingColRowProperty("indicator9",labellogo="getLogoIndicator9()", label="getNameIndicator9()", option="percent" , credential="view_indicator_parametre_9")
     * @return float
     */
    public function getIndicator9()
    {
        return $this->indicator9;
    }

    public static function getNameIndicator9()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLabelIndicator('indicator9');
    }
    
    public static function getLogoIndicator9()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLogoIndicator('indicator9');
    }
    
    /**
     * Set indicator10
     *
     * @param float $indicator10
     *
     * @return CrossIndicatorArticle
     */
    public function setIndicator10($indicator10)
    {
        $this->indicator10 = $indicator10;

        return $this;
    }

    /**
     * Get indicator10
     * @SER\ListeProperty("indicator10",labellogo="getLogoIndicator10()", label="getNameIndicator10()", sort="number",class_supp="indicator", format="number", credential="view_indicator_parametre_10", dbfield="indicator10")
	 * @SER\ReportingDataProperty("indicator10",labellogo="getLogoIndicator10()", label="getNameIndicator10()", label_short="getNameIndicator10()", format="number", credential="view_indicator_parametre_10", moyenne=true)      
     * @SER\ReportingColRowProperty("indicator10",labellogo="getLogoIndicator10()", label="getNameIndicator10()", option="percent" , credential="view_indicator_parametre_10")
     * @return float
     */
    public function getIndicator10()
    {
        return $this->indicator10;
    }

    public static function getNameIndicator10()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLabelIndicator('indicator10');
    }
  
    public static function getLogoIndicator10()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLogoIndicator('indicator10');
    }
    
    /**
     * Set indicator11
     *
     * @param float $indicator11
     *
     * @return CrossIndicatorArticle
     */
    public function setIndicator11($indicator11)
    {
        $this->indicator11 = $indicator11;

        return $this;
    }

    /**
     * Get indicator11
     * @SER\ListeProperty("indicator11",labellogo="getLogoIndicator11()", label="getNameIndicator11()", sort="number",class_supp="indicator", format="number", credential="view_indicator_parametre_11", dbfield="indicator11")
	 * @SER\ReportingDataProperty("indicator11",labellogo="getLogoIndicator11()", label="getNameIndicator11()", label_short="getNameIndicator11()", format="number", credential="view_indicator_parametre_11", moyenne=true) 
     * @SER\ReportingColRowProperty("indicator11",labellogo="getLogoIndicator11()", label="getNameIndicator11()", option="percent" , credential="view_indicator_parametre_11")
     * @return float
     */
    public function getIndicator11()
    {
        return $this->indicator11;
    }

    public static function getNameIndicator11()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLabelIndicator('indicator11');
    }
    public static function getLogoIndicator11()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLogoIndicator('indicator11');
    }
    /**
     * Set indicator12
     *
     * @param float $indicator12
     *
     * @return CrossIndicatorArticle
     */
    public function setIndicator12($indicator12)
    {
        $this->indicator12 = $indicator12;

        return $this;
    }

    /**
     * Get indicator12
     * @SER\ListeProperty("indicator12",labellogo="getLogoIndicator12()", label="getNameIndicator12()", sort="number",class_supp="indicator", format="number", credential="view_indicator_parametre_12", dbfield="indicator12")
	 * @SER\ReportingDataProperty("indicator12",labellogo="getLogoIndicator12()", label="getNameIndicator12()", label_short="getNameIndicator12()", format="number", credential="view_indicator_parametre_12", moyenne=true) 
     * @SER\ReportingColRowProperty("indicator12",labellogo="getLogoIndicator12()", label="getNameIndicator12()", option="percent" , credential="view_indicator_parametre_12")
     * @return float
     */
    public function getIndicator12()
    {
        return $this->indicator12;
    }

    public static function getNameIndicator12()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLabelIndicator('indicator12');
    }
    public static function getLogoIndicator12()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLogoIndicator('indicator12');
    }

    /**
     * Set indicator13
     *
     * @param float $indicator13
     *
     * @return CrossIndicatorArticle
     */
    public function setIndicator13($indicator13)
    {
        $this->indicator13 = $indicator13;

        return $this;
    }

    
    /**
     * Get indicator13
     * @SER\ListeProperty("indicator13",labellogo="getLogoIndicator13()", label="getNameIndicator13()", sort="number",class_supp="indicator", format="number", credential="view_indicator_parametre_13", dbfield="indicator13")
	 * @SER\ReportingDataProperty("indicator13",labellogo="getLogoIndicator13()", label="getNameIndicator13()", label_short="getNameIndicator13()", format="number", credential="view_indicator_parametre_13", moyenne=true) 
     * @SER\ReportingColRowProperty("indicator13",labellogo="getLogoIndicator13()", label="getNameIndicator13()", option="percent" , credential="view_indicator_parametre_13")
     * @return float
     */
    public function getIndicator13()
    {
        return $this->indicator13;
    }
    
    public static function getNameIndicator13()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLabelIndicator('indicator13');
    }

    public static function getLogoIndicator13()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLogoIndicator('indicator13');
    }
    
    /**
     * Set indicator14
     *
     * @param float $indicator14
     *
     * @return CrossIndicatorArticle
     */
    public function setIndicator14($indicator14)
    {
        $this->indicator14 = $indicator14;

        return $this;
    }

    /**
     * Get indicator14
     * @SER\ListeProperty("indicator14",labellogo="getLogoIndicator14()", label="getNameIndicator14()", sort="number",class_supp="indicator", format="number", credential="view_indicator_parametre_14", dbfield="indicator14")
	 * @SER\ReportingDataProperty("indicator14",labellogo="getLogoIndicator14()", label="getNameIndicator14()", label_short="getNameIndicator14()", format="number", credential="view_indicator_parametre_14", moyenne=true)    
     * @SER\ReportingColRowProperty("indicator14",labellogo="getLogoIndicator14()", label="getNameIndicator14()", option="percent" , credential="view_indicator_parametre_14")
     * @return float
     */
    public function getIndicator14()
    {
        return $this->indicator14;
    }

    public static function getNameIndicator14()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLabelIndicator('indicator14');
    }
    
    public static function getLogoIndicator14()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLogoIndicator('indicator14');
    }

    /**
     * Set indicator15
     *
     * @param float $indicator15
     *
     * @return CrossIndicatorArticle
     */
    public function setIndicator15($indicator15)
    {
        $this->indicator15 = $indicator15;

        return $this;
    }

    /**
     * Get indicator15
     * @SER\ListeProperty("indicator15",labellogo="getLogoIndicator15()", label="getNameIndicator15()", sort="number",class_supp="indicator", format="number", credential="view_indicator_parametre_15", dbfield="indicator15")
	 * @SER\ReportingDataProperty("indicator15",labellogo="getLogoIndicator15()", label="getNameIndicator15()", label_short="getNameIndicator15()", format="number", credential="view_indicator_parametre_15", moyenne=true)    
     * @SER\ReportingColRowProperty("indicator15",labellogo="getLogoIndicator15()", label="getNameIndicator15()", option="percent" , credential="view_indicator_parametre_15")
     * @return float
     */
    public function getIndicator15()
    {
        return $this->indicator15;
    }
    
    public static function getNameIndicator15()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLabelIndicator('indicator15');
    }
    public static function getLogoIndicator15()
    {
    	$CrossMgr = ManagersManager::getManager()->getContainer()->get('seriel_cross_indicator.manager');
    	if (false) $CrossMgr= new CrossIndicatorManager();
    	return $CrossMgr->getLogoIndicator('indicator15');
    }
}
