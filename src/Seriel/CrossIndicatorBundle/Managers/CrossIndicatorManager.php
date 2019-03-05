<?php

namespace Seriel\CrossIndicatorBundle\Managers;


use ZombieBundle\Entity\News\Article;
use ZombieBundle\API\Managers\ManagerMetrics;
use ZombieBundle\API\Managers\ManagerCrossMetrics;
use Seriel\CrossIndicatorBundle\Entity\CrossIndicatorArticle;
use Seriel\CrossIndicatorBundle\Entity\ParamIndicatorGeneric;
use ZombieBundle\API\Managers\ManagerStateData;
use ZombieBundle\API\Entity\StateImport;

class CrossIndicatorManager implements ManagerMetrics, ManagerCrossMetrics, ManagerStateData{
	
	const nameModule = 'zombie.modules.crossindicator';
	protected $container = null;
	protected $logger = null;
	private $managerMetricsReferences = array();
	private $parameterCrossIndicator;
	
	public function __construct($container, $logger, $parameterCrossIndicator) {
		$this->container = $container;
		$this->logger = $logger;
		$this->parameterCrossIndicator = $parameterCrossIndicator;
		
		//get manager Metrics
		$parameters = $this->container->getParameter(self::nameModule);
		if (isset($parameters['references_manager_metrics']) && $parameters['references_manager_metrics']) {
			foreach($parameters['references_manager_metrics'] as $nameModule){
				$manager = $this->container->get($nameModule);
				if($manager instanceof ManagerMetrics) {
					$this->managerMetricsReferences[$nameModule] = $manager;
				}
				else {
					throw new \Exception('The managers must implements ManagerMetrics');
				}
				
			}
		}

		//check diff params in file and params in database; if is not equal change in database and recalculate if formula change
		//get parameter in database
		$paramIndMgr= $this->container->get('seriel_cross_indicator.param_indicator_manager');
		if (false) $paramIndMgr = new ParamIndicatorGenericManager();
		
		$paramIndicatorGeneric = $paramIndMgr->getParamIndicatorGenericDatabase();
		$paramIndicatorGenericChange = false;
		if (!isset($paramIndicatorGeneric)) {
			$paramIndicatorGeneric = new ParamIndicatorGeneric();
			$paramIndicatorGenericChange = true;
		}
		
		if ($this->isLabelChanged($paramIndicatorGeneric->getLabelsGeneric())) {
			$paramIndicatorGeneric->setLabelsGeneric($this->getLabelsParam());
			
			$paramIndMgr->save($paramIndicatorGeneric);
			$paramIndicatorGenericChange = true;
		}
		
		if ($this->isFormulaChanged($paramIndicatorGeneric->getFormulasGeneric())) {
			$paramIndicatorGeneric->setFormulasGeneric($this->getFormulasParam());
			$paramIndMgr->save($paramIndicatorGeneric);
			$paramIndicatorGenericChange = true;
		}
		
		if ($paramIndicatorGenericChange) $paramIndMgr->flush();
		
	}
	
	// precalculate cross indicator
	public function precalculateArticles($articles){
		$CrossIndicatorArticleMgr = $this->container->get('seriel_cross_indicator.article_metrics_manager');
		if (false) $CrossIndicatorArticleMgr= new CrossIndicatorArticleManager();
		
		$crossIndicatorarticles  = array();
		$Nbarticles = count($articles);
		$day = new \DateTime();
		$iterateur = 1;
		echo 'Number articles: '.$Nbarticles. PHP_EOL;
		foreach($articles as $article) {
			echo $iterateur.' / '.$Nbarticles. PHP_EOL;
			$crossIndicatorArticle= $CrossIndicatorArticleMgr->getCrossIndicatorArticleForArticleId($article);
			if ( !isset($crossIndicatorArticle)) {
				$crossIndicatorArticle = new CrossIndicatorArticle($article);
			}
			$crossIndicatorArticle->setDateCalcul($day);
			$crossIndicatorArticle->precalculIndicator();
			$CrossIndicatorArticleMgr->save($crossIndicatorArticle);
			$crossIndicatorarticles[] = $crossIndicatorArticle;
			$iterateur ++;
		}
		echo 'Save pre-calcul in database'. PHP_EOL;
		$CrossIndicatorArticleMgr->flush();
		$CrossIndicatorArticleMgr->updateAvgAll();
		return $crossIndicatorarticles;
	}
	
	// calculate cross indicator
	public function calculateArticles($crossIndicatorarticles){
		
		$CrossIndicatorArticleMgr = $this->container->get('seriel_cross_indicator.article_metrics_manager');
		if (false) $CrossIndicatorArticleMgr= new CrossIndicatorArticleManager();
		
		//echo 'Save calcul in database'. PHP_EOL;
		$iterateur = 1;
		$Nbarticles = count($crossIndicatorarticles);
		//echo 'Number articles: '.$Nbarticles. PHP_EOL;
		foreach($crossIndicatorarticles as $crossIndicatorarticle) {
			//echo $iterateur.' / '.$Nbarticles. PHP_EOL;
			$crossIndicatorarticle->calculIndicator();
			$CrossIndicatorArticleMgr->save($crossIndicatorarticle);			
			$iterateur ++;
		}
		$CrossIndicatorArticleMgr->flush();
		return $crossIndicatorarticles;
	}
	
	// update param cross indicator and avg
	public function updateParameterIndicator(){
		//AVG
		$CrossIndicatorArticleMgr = $this->container->get('seriel_cross_indicator.article_metrics_manager');
		if (false) $CrossIndicatorArticleMgr= new CrossIndicatorArticleManager();
		$CrossIndicatorArticleMgr->updateAvgAll();
		
		//Param
		echo 'Update dataupdated paramIndicatorGeneric in database'. PHP_EOL;
		$paramIndMgr= $this->container->get('seriel_cross_indicator.param_indicator_manager');
		if (false) $paramIndMgr = new ParamIndicatorGenericManager();
		$paramIndicatorGeneric = $paramIndMgr->getParamIndicatorGenericDatabase();
		if (!isset($paramIndicatorGeneric)) $paramIndicatorGeneric = new ParamIndicatorGeneric();
		$paramIndicatorGeneric->setDataupdated(true);
		$paramIndMgr->save($paramIndicatorGeneric);
		$paramIndMgr->flush();
	}
	
	// All calculate cross indicator
	public function calculateForAllArticle(){	
		$ArticleMgr = $this->container->get('articles_manager');
		if (false) $ArticleMgr= new ArticleManager();
		
		$CrossIndicatorArticleMgr = $this->container->get('seriel_cross_indicator.article_metrics_manager');
		if (false) $CrossIndicatorArticleMgr= new CrossIndicatorArticleManager();
		
		//Calcul for all articles
		$articles  = $ArticleMgr->getAllArticles();
		
		$crossIndicatorarticles = $this->precalculateArticles($articles);
		$this->calculateArticles($crossIndicatorarticles);

		$this->updateParameterIndicator();
	}

	// get all indicator in all metrics references
	public function getAllIndicators($article){
		$indicators = array();
		foreach($this->managerMetricsReferences as $manager) {
			$articleMetrics = $manager->getMetricsObjectForArticle($article);
			if (isset($articleMetrics)) $indicators = array_merge($indicators,$articleMetrics->getAllIndicators());
		}
		return $indicators;
	}
	// get all measure in all metrics references
	public function getAllMeasures($article){
		$measures = array();
		foreach($this->managerMetricsReferences as $manager) {
			$articleMetrics = $manager->getMetricsObjectForArticle($article);
			if (isset($articleMetrics)) $measures = array_merge($measures,$articleMetrics->getAllMeasures());
		}
		return $measures;
	}
	
	// get indicator in all metrics references
	public function getIndicator($article,$indicator) {
		$indicator = null;
		foreach($this->managerMetricsReferences as $manager) {
			$articleMetrics = $manager->getMetricsObjectForArticle($article);
			if (isset($articleMetrics)) $indicator = $articleMetrics->getIndicator($indicator);
			if (isset($indicator)) return $indicator;
		}
		return $indicator;
	}

	// get measure in all metrics references
	public function getMeasure($article,$measure) {
		$measure = null;
		foreach($this->managerMetricsReferences as $manager) {
			$articleMetrics = $manager->getMetricsObjectForArticle($article);
			if (isset($articleMetrics)) $measure= $articleMetrics->getMeasure($measure);
			if (isset($measure)) return $measure;
		}
		return $measure;
	}
	// return all metrics Cross of for article
	public function getMetricsObjectForArticle($article) {
		$article_id = null;
		if ($article instanceof Article) {
			$article_id = $article->getId();
		} else {
			$article_id = $article;
			$article = null;
		}
		
		$artMetricsMgr = $this->container->get('seriel_cross_indicator.article_metrics_manager');
		if (false) $artMetricsMgr = new CrossIndicatorArticleManager();
		
		$artMetrics = $artMetricsMgr->getCrossIndicatorArticleForArticleId($article_id);
		
		return $artMetrics;
	}
	
	// return label of indicator generique in parameter
	public function getLabelIndicator($idIndicator) {
		foreach ($this->parameterCrossIndicator as $rowParameter) {
			if (isset($rowParameter[$idIndicator])) {
				return $rowParameter[$idIndicator][0];
			}
			
		}
		return $idIndicator;	
	}
	
	// return logo of indicator generique in parameter
	public function getLogoIndicator($idIndicator) {
		foreach ($this->parameterCrossIndicator as $rowParameter) {
			if (isset($rowParameter[$idIndicator])) {
				if (isset($rowParameter[$idIndicator][2])) return $rowParameter[$idIndicator][2];
			}
		}
		return null;
	}
	
	// return value of indicator generique calculate by formula parameter
	public function getValueIndicator($idIndicator, $indicatorsMetrics,$measuresMetrics) {
		$calcul = $this->getCalculIndicator($idIndicator);
		if ( $calcul == '') {
			return null;
		}else {
			return $this->generateValue($calcul,$indicatorsMetrics,$measuresMetrics);
		}
		
	}
	
	// return formula of indicator generique in parameter
	public function getCalculIndicator($idIndicator) {
		foreach ($this->parameterCrossIndicator as $rowParameter) {
			if (isset($rowParameter[$idIndicator])) {
				return $rowParameter[$idIndicator][1];
			}
			
		}
		return '';
	}
	
	// return value formula of indicator generique in parameter
	public function generateValue($calcul,$indicatorsMetrics,$measuresMetrics) {
		$valuereturn = null;
		if ((!isset($calcul)) or $calcul =='' ) return null;
		//replace id by value
		foreach ($indicatorsMetrics as $name => $value ) {
			if (!isset($value)) $value = 0;
			$calcul = str_replace('$'.$name.'$', $value, $calcul);
		}
		foreach ($measuresMetrics as $name => $value ) {
			if (!isset($value)) $value = 0;
			$calcul = str_replace('$'.$name.'$', $value, $calcul);
		}		
		if (strpos($calcul, '$') === false and $calcul != '') {
			eval( '$valuereturn = '.$calcul.';' );
		}
		return $valuereturn;
	}
	
	// get formulas in parameter
	public function getFormulasParam() {		
		$formulas = array();
		foreach ($this->parameterCrossIndicator as $rowParameter) {
			foreach ($rowParameter as $indicator => $Parameter) {
				$formulas[$indicator] = $Parameter[1];
			}
		}
		return $formulas;
	}

	// get labels in parameter
	public function getLabelsParam() {
		$labels = array();
		foreach ($this->parameterCrossIndicator as $rowParameter) {
			foreach ($rowParameter as $indicator => $Parameter) {
				$labels[$indicator] = $Parameter[0];
			}
		}
		return $labels;
	}
	
	// Check if one formula to be changed
	public function isFormulaChanged($paramsUsed=null) {
		if (!isset($paramsUsed)) {
			//get parameter in database
			$paramIndMgr= $this->container->get('seriel_cross_indicator.param_indicator_manager');
			if (false) $paramIndMgr = new ParamIndicatorGenericManager();
			$paramIndicatorGeneric = $paramIndMgr->getParamIndicatorGenericDatabase();
			if (isset($paramIndicatorGeneric)) {
				$paramsUsed = $paramIndicatorGeneric->getFormulasGeneric();
			}else {
				$paramsUsed = array();
			}	
		}

		$formulas = $this->getFormulasParam();
		$diff = array_diff_assoc($formulas, $paramsUsed);
		$diff2 = array_diff_assoc($paramsUsed, $formulas);
		if (count($diff) > 0 OR count($diff2) > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	// Check if one label to be changed
	public function isLabelChanged($paramsUsed=null) {
		if (!isset($paramsUsed)) {
			//get parameter in database
			$paramIndMgr= $this->container->get('seriel_cross_indicator.param_indicator_manager');
			if (false) $paramIndMgr = new ParamIndicatorGenericManager();
			$paramIndicatorGeneric = $paramIndMgr->getParamIndicatorGenericDatabase();
			if (isset($paramIndicatorGeneric)) {
				$paramsUsed = $paramIndicatorGeneric->getLabelsGeneric();
			}else {
				$paramsUsed = array();
			}	
		}
		$labels= $this->getLabelsParam();
		$diff = array_diff_assoc($labels, $paramsUsed);
		$diff2 = array_diff_assoc($paramsUsed, $labels);
		if (count($diff) > 0 OR count($diff2) > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	// return check list of import and calculate data for CrossIndicator
	public function getStateImports() {
		$stateInports = array();
		
		$artMetricsMgr = $this->container->get('seriel_cross_indicator.article_metrics_manager');
		if (false) $artMetricsMgr = new CrossIndicatorArticleManager();
		
		$date= $artMetricsMgr->getLastDateCalcul();
		if (isset($date)) {
			$stateInports[] = New StateImport('CrossIndicator - Dernier calcul', $date);
		}
		return $stateInports;
	}
}