<?php

namespace ZombieBundle\Managers\News;

use ZombieBundle\Managers\ZombieManager;
use ZombieBundle\Entity\News\Article;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\From;
use Seriel\AppliToolboxBundle\Managers\ManagersManager;
use ZombieBundle\Managers\Utils\SearchHelperManager;
use Seriel\AppliToolboxBundle\Utils\Recherche\SearchObject;
use ZombieBundle\Entity\Utils\SearchHelper;
use ZombieBundle\API\Entity\ResultSearchSemantique;
use ZombieBundle\API\Managers\ManagerSemantique;
use ZombieBundle\Entity\Individu\Individu;

class ArticlesManager extends ZombieManager
{
	protected $tmp_links_mgr = null;
	
	public function __construct($doctrine, $logger, $container) {
		parent::__construct($doctrine, $logger, $container);

		$this->tmp_links_mgr = new ArticleTmpLinksManager($doctrine, $logger, $container);
	}

	protected function isGrantedFilter( $individu, $code) {
		
		if ($individu instanceof Individu) {
			

			// get entite
			$entite = $individu->getEntitePrincipale();
			if (!$entite) {
				return false;
			}
			
			$gestionnaire_de_droits = $this->container->get('security_manager')->getCurrentCredentials();
			if (!$gestionnaire_de_droits) {
				return false;
			}
			if (false) $gestionnaire_de_droits = new GestionnaireDeDroits($individu);
			switch ($code) {
				case 'view':
					$levels= $gestionnaire_de_droits->getAllLevelsForDroit('ZombieBundle\Entity\News\Article >> view');
					if ((!$levels) || count($levels) == 0) {
						return false;
					}
					else {
						return true;
					}
				case 'edit':
					$levels = $gestionnaire_de_droits->getAllLevelsForDroit('ZombieBundle\Entity\News\Article >> edit');
					if ((!$levels) || count($levels) == 0) {
						return false;
					}
					else {
						return true;
					}
				default:
					return true;
			}
			
			
		}
		
		// if user is no individu = > block.
		return false;
		
	}

	public function getObjectClass() {
		return 'ZombieBundle\Entity\News\Article';
	}

	protected function createQueryBuilder() {
		$qb = parent::createQueryBuilder();

		$alias = $this->getAlias();

		$modulesMgr = $this->container->get('zombie_modules_manager');
		if (false) $modulesMgr = new ModulesManager();

		$modules = $modulesMgr->getZombieModules();

		if ($modules) {
			foreach ($modules as $name => $params) {
				if (isset($params['metrics_object_class']) && $params['metrics_object_class']) {
					$qb->leftJoin($params['metrics_object_class'], $name, Join::WITH, $alias.'.id = '.$name.'.article');
				}
			}
		}

		return $qb;
	}

	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;
		$hasSearchSemantique = false;
		$alias = $this->getAlias();

		$qb->andWhere($alias.'.deleted is null or '.$alias.'.deleted != 1');


		
		if (isset($params['titre']) && $params['titre']) {
			$titre = $params['titre'];
			if ($this->addWhereFullTextLike($qb, $alias.'.titre', $titre)) {
				$hasParams = true;
			}
		}

		if (isset($params['chapeau']) && $params['chapeau']) {
			$chapeau = $params['chapeau'];
			if ($this->addWhereFullTextLike($qb, $alias.'.chapeau', $chapeau)) {
				$hasParams = true;
			}
		}

		if (isset($params['motcle']) && $params['motcle']) {
			$motcle = $params['motcle'];
			if ($this->addWhereFullTextLike($qb, $alias.'.mot_cle', $motcle)) {
				$hasParams = true;
			}
		}
		if (isset($params['section']) && $params['section']) {
			$sections = explode('-', $params['section']);
			$qb->andwhere($alias.'.section in (:sections)')->setParameter('sections', $sections);
			$hasParams = true;

		}
		
		// Note Semantic search
		$minNoteSemantique = null;
		$maxNoteSemantique = null;
		if (isset($params['note_semantique']) && $params['note_semantique']) {
			$semantiqueMinMAX = explode('::', $params['note_semantique']);
			if (count($semantiqueMinMAX) == 2 ) { 
				$minNoteSemantique= $semantiqueMinMAX[0];
				$maxNoteSemantique= $semantiqueMinMAX[1];
				($minNoteSemantique != '') ? $minNoteSemantique= (float)$minNoteSemantique : $minNoteSemantique = null;
				($maxNoteSemantique!= '') ? $maxNoteSemantique= (float)$maxNoteSemantique: $maxNoteSemantique= null;
			}
		}
		
		/*
		 * Modules
		 */
		$modulesMgr = $this->container->get('zombie_modules_manager');
		if (false) $modulesMgr = new ModulesManager();
		$modules = $modulesMgr->getZombieModules();
		if ($modules) {
			foreach ($modules as $name => $paramsModule) {
				
				//  Metrics parameters indicators ( min - max)
				if (isset($paramsModule['metrics_object_class']) && $paramsModule['metrics_object_class']) {		
					//get indicator in object metrics
					$indicators = array_keys($paramsModule['metrics_object_class']::getAllIdIndicators());
					foreach ($indicators as $indicator) {
						$id = $name.'_'. $indicator;					
						if (isset($params[$id]) && $params[$id]) {
							$valueModuleParam = explode('::', $params[$id]);
							if (count($valueModuleParam) == 2 ) {
								$min = $valueModuleParam[0];
								$max = $valueModuleParam[1];
								if ($min!= '') {
									$qb->andwhere($name.'.'.$indicator.' >= :'.$id.'_min')->setParameter($id.'_min', $min);
									$hasParams = true;
								}
								if ($max!= '') {
									$qb->andwhere($name.'.'.$indicator.' <= :'.$id.'_max')->setParameter($id.'_max', $max);
									$hasParams = true;
								}
							}
						}
					}
				}
				// Semantic
				if (isset($paramsModule['search_semantique']) && $paramsModule['search_semantique']) {
					$id = 'semantique_'.$name;
					$managerSemantique = $paramsModule['search_semantique'];
					if (isset($params[$id]) && $params[$id]) {
						if ( !$hasSearchSemantique) {
							$value= $params[$id];
							if ($this->addWhereSearchSemantique($qb, $alias, $value,$managerSemantique,$minNoteSemantique,$maxNoteSemantique,$options,$params,$execvars)) {
								$hasParams = true;
								$hasSearchSemantique = true;
							}
						}
						else {
							throw new \Exception('Une seule recherche semantique est autorisée');
						}
					}
				}
				// Trend
				if (isset($paramsModule['service_trend']) && $paramsModule['service_trend']) {
					foreach ($modules as $nameSemantique => $paramsModuleSemantique) {
						if (isset($paramsModuleSemantique['search_semantique']) && $paramsModuleSemantique['search_semantique']) {
							$id = 'trend_'.$name.'_'.$nameSemantique;
							$managerTrend= $paramsModule['service_trend'];
							$managerSemantique = $paramsModuleSemantique['search_semantique'];
							if (isset($params[$id]) && $params[$id]) {
								$value = $params[$id];
								$trends = explode('-', $value);
								if (count($trends)> 0 ) {
									if ( !$hasSearchSemantique) {
										if ($this->addWhereSearchSemantiqueTrend($qb, $alias,$trends,$managerSemantique,$minNoteSemantique,$maxNoteSemantique,$options,$params,$execvars)) {
											$hasParams = true;
											$hasSearchSemantique = true;
										}
									}else {
										throw new \Exception('Une seule recherche semantique est autorisée');
									}

								}
							}
						}
					}
				}
			}
		}
		
		if (isset($params['guid']) && $params['guid']) {
			$guids = $params['guid'];
			if (!is_array($guids)) $guids = explode('-', $guids);
			$qb->andWhere($alias.'.guid in (:guids)')->setParameter('guids', $guids);
			$hasParams = true;
		}
		
		if (isset($params['uris']) && $params['uris']) {
			$uris= $params['uris'];
			if (!is_array($uris)) $uris = explode('-', $uris);
			$orX = $qb->expr()->orX();
			$orX->add($alias.'.guid in (:uris)');
			$orX->add($alias.'.article_uri_1 in (:uris)');
			$orX->add($alias.'.article_uri_2 in (:uris)');
			$orX->add($alias.'.article_uri_3 in (:uris)');
			$orX->add($alias.'.article_uri_4 in (:uris)');
			$orX->add($alias.'.article_uri_5 in (:uris)');
			$orX->add($alias.'.article_uri_6 in (:uris)');
			$orX->add($alias.'.article_uri_7 in (:uris)');
			$orX->add($alias.'.article_uri_8 in (:uris)');
			$orX->add($alias.'.article_uri_9 in (:uris)');
			$orX->add($alias.'.article_uri_10 in (:uris)');
			$qb->andWhere($orX);
			$qb->setParameter('uris', $uris);
			$hasParams = true;
		}

		if (isset($params['short_guid']) && $params['short_guid']) {
			$short_guid = $params['short_guid'];
			$qb->andWhere($alias.'.short_guid = :short_guid')->setParameter('short_guid', $short_guid);
			$hasParams = true;
		}

		if (isset($params['date_parution']) && $params['date_parution']) {
			if ($this->addWhereDate($qb, $alias.'.date_parution', $params['date_parution'])) {
				$hasParams = true;
			}
		}
		if (isset($params['date_parution_day']) && $params['date_parution_day']) {
			$day = $params['date_parution_day'];
			$qb->andWhere('WEEKDAY('.$alias.'.date_parution) = WEEKDAY(:day)')->setParameter('day', $day);
			$hasParams = true;
		}
		
		if (!$options || (!isset($options['count']))) {
			if (isset($params['orderby']) && $params['orderby']) {
				$order = $params['orderby'];
				if (strstr($order, '.') == false) {
					$order = $this->getAlias().'.'.$order;
				}
				$desc = 'asc';
				if (isset($params['desc']) && $params['desc']) {
					$desc = 'desc';
				}
				$alias = $this->getAlias();
				if ( $qb->addOrderBy($order, $desc)) {
					$hasParams = true;
				}
			}
			if (isset($params['random']) && $params['random']) {
				$random= $params['random'];
				if ( $qb->addOrderBy('RAND() ')) {
					$hasParams = true;
				}
			}
		}
		

		
		
		return $hasParams;
	}
	
	public function query($params, $options = null, &$execvars = null) {
		//options
		if (isset($params['limit']) && $params['limit']) {
			$limit= $params['limit'];
			$options['limit'] = $limit;
			if (isset($options['count'])) {
				return $limit;
			}
		}

		
		$execvars = array();
		$res = parent::query($params, $options, $execvars);
		if (!$options || (!isset($options['count']))) {
			if (isset($execvars['semantique_map'])) {
				$this->mapSemantiqueNotes($res, $execvars['semantique_map']);
			}
		}
		
		return $res;
	}
	
	public function search($params, $options = null, &$execvars = null) {		
		return $this->query($params, $options, $execvars );
	}
	
	protected function mapSemantiqueNotes(&$result, $notesMap) {
		$articles = $result instanceof SearchObject ? $result->getResult() : $result;
		
		if ($articles) {
			foreach ($articles as $art) {
				if (false) $art = new Article();
				if (isset($notesMap[$art->getId()])) {
					$art->setNoteSemantique($notesMap[$art->getId()]);
				}
			}
		}
	}

	/**
	 * @return Article
	 */
	public function getArticle($id) {
		return $this->get($id);
	}

	/**
	 * @return Article
	 */
	public function getArticleForGuid($guid) {
		return $this->query(array('guid' => $guid), array('one' => true));
	}

	/**
	 * @return Article[]
	 */
	public function getAllArticleForListGuid($guids) {
		return $this->query(array('guid' => $guids));
	}

	/**
	 * @return Article[]
	 */
	public function getAllArticleForListUris($uris) {
		return $this->query(array('uris' => $uris));
	}
	
	/**
	 * @return Article
	 */
	public function getArticleForShortGuid($short_guid) {
		return $this->query(array('short_guid' => $short_guid), array('one' => true));
	}

	public function getAllArticlesForPeriode($date_debut, $date_fin, $options = array()) {
		if ((!$date_debut) && (!$date_fin)) return array();
		if (!$date_debut) $date_debut = '';
		if (!$date_fin) $date_fin = '';

		$date_parution = $date_debut.'::'.$date_fin;

		return $this->query(array('date_parution' => $date_parution), $options);
	}
	
	
	public function getAllArticlesForDay($day, $options = array()) {
		if (!$day) return array();
		return $this->query(array('date_parution_day' => $day), $options);
	}

	/**
	 * @return Article[]
	 */
	public function getAllArticles($options = null) {
		return $this->getAll($options);
	}

	public function getAllSection() {

		$query = $this->getDoctrineEM()->createQuery('SELECT a.section FROM '.$this->getObjectClass()." a WHERE a.section <> '' GROUP BY a.section ORDER BY a.section");
			
		$sectionsBD = $query->getScalarResult();
		$sections= array_column($sectionsBD, "section");
		
		return $sections; 
	}

	public function getLastImport() {
		$Lastarticle = $this->query(array(), array('orderBy' => array('created_at' => 'desc'), 'limit' => 1));
		if (count($Lastarticle) == 1){
			return $Lastarticle[0]->getCreatedAt();
		}
		else {
			return null;
		}
		
	}
	
	public function getSearchParamsFromRequest($request) {
		$datas = array();

		if ($request instanceof Request) {
			$datas = $request->request->all();
		} else {
			$datas = $request;
		}
		//delete %20 of url in value
		foreach ($datas as $key=> $data) {
			if (is_string($data)) $datas[$key] = str_replace('%20', ' ', $data);
			
		}

		return $this->getSearchParams($datas);
	}
	
	// get parameters in array with parameter url ( exemple : [semantique_mot_cle=trump,evergreen=3::,t=article] )
	public function getSearchParamsFromUrl($params) {
		$parameters = array();
		$datas= explode(",", substr($params,1,-1));
		if (count($datas) > 0) {
			foreach ($datas as $param) {
				$datas= explode("=", $param);
				if (count($datas) == 2) {
					$parameters[$datas[0]] = $datas[1];
				}
			}
		}
		return $this->getSearchParams($parameters);
	}
	public function getSearchParams($datas) {
		
		$params = array();
		//article
		$titre = isset($datas['titre']) ? $datas['titre'] : null;
		$chapeau = isset($datas['chapeau']) ? $datas['chapeau'] : null;
		$motcle = isset($datas['motcle']) ? $datas['motcle'] : null;
		$section = isset($datas['section']) ? $datas['section'] : null;
		
		//semantique
		$note_semantique = isset($datas['note_semantique']) ? $datas['note_semantique'] : null;

		//options
		$limit = isset($datas['limit']) ? $datas['limit'] : null;
		$random= isset($datas['random']) ? $datas['random'] : null;
		$orderby= isset($datas['orderby']) ? $datas['orderby'] : null;
		$desc= isset($datas['desc']) ? $datas['desc'] : null;
		
		//date
		$date_parution = isset($datas['date_parution']) ? $datas['date_parution'] : null;
		
		//article
		if ($titre) $params['titre'] = $titre;
		if ($chapeau) $params['chapeau'] = $chapeau;
		if ($motcle) $params['motcle'] = $motcle;
		if ($section) $params['section'] = $section;
		//semantique
		if ($note_semantique) $params['note_semantique'] = $note_semantique;
		
		
		//date
		if ($date_parution) $params['date_parution'] = $date_parution;
		
		//options
		if ($limit) $params['limit'] = $limit;
		if ($random) $params['random'] = $random;
		if ($orderby) $params['orderby'] = $orderby;
		if ($desc) $params['desc'] = $desc;
		
		// MODULE
		$modulesMgr = $this->container->get('zombie_modules_manager');
		if (false) $modulesMgr = new ModulesManager();
		$modules = $modulesMgr->getZombieModules();
		if ($modules) {
			foreach ($modules as $name => $paramsModule) {
				// Metrics
				if (isset($paramsModule['metrics_object_class']) && $paramsModule['metrics_object_class']) {
					
					//get indicator in object metrics
					$indicators = array_keys($paramsModule['metrics_object_class']::getAllIdIndicators());
					foreach ($indicators as $indicator) {
						$id = $name.'_'. $indicator;
						$value = isset($datas[$id]) ? $datas[$id] : null;
						if ($value) $params[$id] = $value;
					}	
				}
				// Semantique
				if (isset($paramsModule['search_semantique']) && $paramsModule['search_semantique']) {
					$id = 'semantique_'.$name;
					$value = isset($datas[$id]) ? $datas[$id] : null;
					if ($value) $params[$id] = $value;
				}
				
				// Trend
				if (isset($paramsModule['service_trend']) && $paramsModule['service_trend']) {
					foreach ($modules as $nameSemantique => $paramsModuleSemantique) {
						if (isset($paramsModuleSemantique['search_semantique']) && $paramsModuleSemantique['search_semantique']) {
							$id = 'trend_'.$name.'_'.$nameSemantique;
							$value = isset($datas[$id]) ? $datas[$id] : null;
							if ($value) $params[$id] = rawurldecode($value);
						}
					}
				}

			}
		}
		
		
		return $params;
	}
	
	public function createArticleTmpLink($article, $duration_in_seconds) {
		return $this->tmp_links_mgr->createArticleTmpLink($article, $duration_in_seconds);
	}

	public function cleanTmpLinkDB() {
		$this->tmp_links_mgr->cleanDatabase();
	}

	public function getArticleFromTmpCode($code) {
		$tmp_link = $this->tmp_links_mgr->getArticleArticleTmpLink($code);

		if ($tmp_link) {
			$article = $tmp_link->getArticle();
			if ($article) return $article;
		}

		return null;
	}
	

	// Search semantic article with subject trends of the day
	public function addWhereSearchSemantiqueTrend($qb, $alias,$trends,$managerSemantique,$minNoteSemantique,$maxNoteSemantique,$options = array(),$params = array(), &$execvars = null) {

		$NbTrends = count($trends);
		if($NbTrends== 0) return false;
		$trends= array_fill_keys($trends, 1);

		$this->addWhereSearchSemantiqueByManySubjects($qb, $alias, $trends,$managerSemantique,$minNoteSemantique,$maxNoteSemantique,$options,$params,$execvars);

	}
	
	// Search semantic article by array subject with weight
	public function addWhereSearchSemantiqueByManySubjects($qb, $alias, $subjects,$managerSemantique,$minNoteSemantique,$maxNoteSemantique,$options = array(),$params = array(), &$execvars = null) {
		
		/*
		// example format subjects
		  $subjects =array();
		  $subjects['trump toyota'] = 50;
		  $subjects['voiture autonome'] = 50;
		  $subjects['Fete des lumières Lyon'] = 10;
		  $subjects['Risque attentats France'] = 8;
		 */
		$SemantiqueMgr = ManagersManager::getManager()->getContainer()->get($managerSemantique);
		if (! $SemantiqueMgr instanceof ManagerSemantique) throw new \Exception('The managers must implements ManagerSemantique');
		
		
		// Select with multiple wordsearch
		$result = $SemantiqueMgr->getArticlebySearchSemantiqueWithSubjects($subjects,true,false);

		// mark on 100 and filter
		$resultSearchSemantique =  new ResultSearchSemantique($result);
		$resultSearchSemantique->calculIndice();
		$resultSearchSemantique->setFiltreMin($minNoteSemantique);
		$resultSearchSemantique->setFiltreMax($maxNoteSemantique);
		$resultSearchSemantique->calculResult();	
		
		$execvars['semantique_map'] = $resultSearchSemantique->getCalculResult();
		
		//main query
		$search_helper_uid = null;
		if ((!$options) || (!isset($options['count']))) {
			$searchHelperMgr = $this->container->get('search_helper_manager');
			if (false) $searchHelperMgr = new SearchHelperManager();
			$search_helper_uid = $searchHelperMgr->mapDatas($resultSearchSemantique->getCalculResult());
			
			// order Semantic Note
			$qb->innerJoin('ZombieBundle\Entity\Utils\SearchHelper', 'sh', Join::WITH, $alias.'.id = sh.obj_id and sh.uid = \''.$search_helper_uid.'\'');
			if (!isset($options['orderBy']) and !isset($params['orderby']) and !isset($params['random'])) {
				$qb->add('orderBy', 'sh.num1 desc');
			}
		}
		else {
			if (count($resultSearchSemantique->getCalculResultId())> 0) {
				$qb->andWhere($qb->expr()->in($alias.'.id', $resultSearchSemantique->getCalculResultId()));
			}
		}
		$hasValue = true;
		return $hasValue;
	}
	// Search semantic article by one subject
	public function addWhereSearchSemantique($qb, $alias, $text, $managerSemantique,$minNoteSemantique,$maxNoteSemantique,$options = array(),$params = array(), &$execvars) {
		if (!($text)) return false;
		

		$SemantiqueMgr = ManagersManager::getManager()->getContainer()->get($managerSemantique);
		if (! $SemantiqueMgr instanceof ManagerSemantique) throw new \Exception('The managers must implements ManagerSemantique');
		
		// get id article with weight (mark on 100)
		$result = $SemantiqueMgr->getArticlebySearchSemantique($text);
		
		// mark on 100 and filter
		$resultSearchSemantique =  new ResultSearchSemantique($result);
		$resultSearchSemantique->calculIndice();
		$resultSearchSemantique->setFiltreMin($minNoteSemantique);
		$resultSearchSemantique->setFiltreMax($maxNoteSemantique);
		$resultSearchSemantique->calculResult();		
	
		$execvars['semantique_map'] = $resultSearchSemantique->getCalculResult();
		
		//main query
		$search_helper_uid = null;
		if ((!$options) || (!isset($options['count']))) {
			$searchHelperMgr = $this->container->get('search_helper_manager');
			if (false) $searchHelperMgr = new SearchHelperManager();
			$search_helper_uid = $searchHelperMgr->mapDatas($resultSearchSemantique->getCalculResult());
			
			$qb->innerJoin('ZombieBundle\Entity\Utils\SearchHelper', 'sh', Join::WITH, $alias.'.id = sh.obj_id and sh.uid = \''.$search_helper_uid.'\'');
			if (!isset($options['orderBy']) and !isset($params['orderby']) and !isset($params['random'])) {
				$qb->add('orderBy', 'sh.num1 desc');
			}
		}
		else {
			if (count($resultSearchSemantique->getCalculResultId())> 0) {
				$qb->andWhere($qb->expr()->in($alias.'.id', $resultSearchSemantique->getCalculResultId()));
			}
		}

		$hasValue = true;
		return $hasValue;
	}

}

?>
