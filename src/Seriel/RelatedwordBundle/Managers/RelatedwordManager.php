<?php

namespace Seriel\RelatedwordBundle\Managers;

use ZombieBundle\Entity\News\Article;
use Seriel\AppliToolboxBundle\Managers\ManagersManager;
use ZombieBundle\Utils\StopWordTokenizer;
use Seriel\RelatedwordBundle\Managers\RelatedwordWordManager;
use Seriel\RelatedwordBundle\Managers\RelatedwordArticleWordManager;
use Seriel\RelatedwordBundle\Managers\RelatedwordArticleLinkWordManager;
use Seriel\RelatedwordBundle\Managers\RelatedwordLinkWordManager;
use Seriel\RelatedwordBundle\Entity\ArticleWord;
use Seriel\RelatedwordBundle\Entity\Word;
use Seriel\RelatedwordBundle\Entity\LinkWord;
use Seriel\RelatedwordBundle\Entity\LinkWordTemp;
use ZombieBundle\Utils\ZombieUtils;
use Seriel\DandelionBundle\Entity\DandelionArticleSemantics;
use Seriel\DandelionBundle\Managers\DandelionArticleSemanticsManager;
use ZombieBundle\API\Managers\ManagerSemantique;
use ZombieBundle\API\Managers\ManagerStateData;
use ZombieBundle\API\Entity\StateImport;

class RelatedwordManager implements ManagerSemantique, ManagerStateData{

	protected $container = null;
	protected $logger = null;


	public function __construct($container, $logger) {
		$this->container = $container;
		$this->logger = $logger;

	}

	// get list article_id order by Weight with multiple subjects
	public function getArticlebySearchSemantiqueWithSubjects($subjects)
	{
		$ArticlewordMgr = ManagersManager::getManager()->getContainer()->get('seriel_related_word.article_word_manager');
		if (false) $ArticlewordMgr = new RelatedwordArticleWordManager();
		$NbSubjects = count($subjects);
		if($subjects== 0) return array();
		
		$AllSubjectsRelativeWord = array();
		// for each subject, return the best articles with search semantique
		foreach ($subjects as $subject => $weightSubject) {
			$relatedwordBySubject = $this->getSearchSemantiqueMultipleWord($subject,0,$weightSubject);
			$AllSubjectsRelativeWord = array_merge($AllSubjectsRelativeWord,$relatedwordBySubject);
		}
		if(count($AllSubjectsRelativeWord) == 0) return false;
		return $ArticlewordMgr->getResultSubQuerySearchSemantiquebyMultipleWord($AllSubjectsRelativeWord,true, false);
	}
	
	// get list article_id order by Weight with multiple wordsearch or one wordsearch
	public function getArticlebySearchSemantique($text)
	{
		$ArticlewordMgr = ManagersManager::getManager()->getContainer()->get('seriel_related_word.article_word_manager');
		if (false) $ArticlewordMgr = new RelatedwordArticleWordManager();

		$relatedwordByWordSearch = $this->getSearchSemantiqueMultipleWord($text,0);
		if(count($relatedwordByWordSearch) == 0) return array();
		return $ArticlewordMgr->getResultSubQuerySearchSemantiquebyMultipleWord($relatedwordByWordSearch,true, false);
	}
	
	// find related word with BD by word search
	public function getSearchSemantiqueMultipleWord($text,$nbTotal, $weight =1)
	{
		//transform text
		$tokenizer = new StopWordTokenizer('fr',true);
		$textSearch = strtolower($text);
		$wordsSearch= $tokenizer->tokenize($textSearch);	
		
		$RelatedWordByWordSearch = array();
		foreach ($wordsSearch as $wordSearch) {
			$words = $this->getSearchSemantiqueWord($wordSearch,$nbTotal,$weight);
			if (count($words) > 0) {
				$RelatedWordByWordSearch[] = $words;
			}
		}
		
		return ($RelatedWordByWordSearch);
	}

	// transform result of getSearchSemantiqueMultipleWord => list of LinkWordTemp
	public function getAssociationWordSemantiqueSearch($RelatedWordByWordSearch,$Max = 0)
	{		
		$ListLinkWord= array();
		foreach ($RelatedWordByWordSearch as $relatedWord1) {
			foreach ($RelatedWordByWordSearch as $relatedWord2) {
				if ($relatedWord1!= $relatedWord2) {
					// only array relatedwordBySearchWord is different
					foreach ($relatedWord1 as $keySource => $valueSource) {
						foreach ($relatedWord2 as $keyTarget => $valueTarget) {
							// if word is different
							if ($keySource!= $keyTarget) {
								$LinkWordTemp= new LinkWordTemp();
								$LinkWordTemp->setWordSource($keySource);
								$LinkWordTemp->setWordTarget($keyTarget);
								$LinkWordTemp->setWeight($valueSource + $valueTarget);
								$ListLinkWord[] = $LinkWordTemp;
							}

						}
					}
				}		
				
			}
		}
		//sort by weight
		usort($ListLinkWord, array("Seriel\RelatedwordBundle\Entity\LinkWordTemp", "compare"));
		
		// if limit => trunc
		if ($Max != 0) {
			$ListLinkWordTrunc= array();
			$i = 0;
			foreach ($ListLinkWord as $LinkWord ) {
				if ($Max> $i) {
					$ListLinkWordTrunc[] = $LinkWord;
					$i ++;
				}
			}
			return $ListLinkWordTrunc;
		}
		else {
			return $ListLinkWord;		
		}
		
	}
	
	// find related word with $nbTotalRelatedWordByWord
 	public function getSearchSemantiqueWord($text,$nbTotal,$weight = 1)
	{
		//10 by default change in parameter.yml
		if ($this->container->hasParameter('relatedword.related_word_by_word')) {
			$nbTotalRelatedWordByWord = $this->container->getParameter('relatedword.related_word_by_word');
		}
		else {
			$nbTotalRelatedWordByWord = 10;
		}
		$relatedWords = $this->getRelatedWord($text,$nbTotalRelatedWordByWord,$weight,true);
		// no limit if $nbTotal =0
		if ($nbTotal != 0 ) {
			$searchword = array();
			$i = 0;
			foreach ($relatedWords as $key => $value) {
				if ($nbTotal > $i) {
					$searchword[$key] = $value ;
					$i ++;
				}
			}
			return $searchword;
		}
		else {
			return $relatedWords;
		}
	}
	
	// // return similarity (percent) between 2 subjects with related word
	public function getSimilarity($text1,$text2,$depth=40)
	{
		if (strtolower($text1) == strtolower($text2)) {
			return 100;
		}
		if ($depth == 0) {
			return 0;
		}
		$similarity = 0;
		
		//getRelatedWord for text 1 
		$relatedWord1 = $this->getRelatedWord($text1, $depth, 1);
		//calcul moy on RelatedWord for text 1 
		$moy1 = 0;
		foreach ($relatedWord1 as $word => $value){
			$moy1+= $value;
		}
		if (count($relatedWord1) > 0 )  $moy1 = $moy1 / count($relatedWord1);
		
		//getRelatedWord for text 2 
		$relatedWord2 = $this->getRelatedWord($text2, $depth, 1);
		//calcul moy on RelatedWord for text 1
		$moy2= 0;
		foreach ($relatedWord2 as $word => $value){
			$moy2+= $value;
		}
		if (count($relatedWord2) > 0 )  $moy2= $moy2/ count($relatedWord2);
		
		$weightTotal = 0;
		$NbrelatedWordEqual = 0;
		foreach ($relatedWord1 as $key1 => $value1) {
			$weightTotal +=  ZombieUtils::getMarkOn100($value1, $moy1);
			foreach ($relatedWord2 as $key2 => $value2) {
				$weightTotal += ZombieUtils::getMarkOn100($value2, $moy2);
				if ($key1 == $key2) {
					$similarity += ZombieUtils::getMarkOn100($value1, $moy1) + ZombieUtils::getMarkOn100($value2, $moy2);
					$NbrelatedWordEqual++;
				}
			}
		}

		$similarity = ($NbrelatedWordEqual / $depth ) * 100;
		return $similarity;
	}
	
	// find related word
	public function getRelatedWord($text,$nbRelatedWordbyWord,$weight,$addWordText = false)
  	{
		$linkwordMgr = ManagersManager::getManager()->getContainer()->get('seriel_related_word.link_word_manager');
 	    if (false) $linkwordMgr = new RelatedwordLinkWordManager();
		$wordMgr = ManagersManager::getManager()->getContainer()->get('seriel_related_word.word_manager');
		if (false) $wordMgr = new RelatedwordWordManager();

		if ((!isset($nbRelatedWordbyWord)) or $nbRelatedWordbyWord == 0) return array();
		//transform text search
		$tokenizer = new StopWordTokenizer('fr',true);
		$text = strtolower($text);
		$arrayWord = $tokenizer->tokenize($text);
		$relatedWords = array();

		// get Related Words
		foreach ($arrayWord as $word) {
			$wordDB = $wordMgr->getWordByName($word);
			if ($wordDB != null) {
				$LinkWords = $linkwordMgr->getBestLinkWordBySource($wordDB,$nbRelatedWordbyWord);
				foreach ($LinkWords as $linkword) {
					$name = $linkword->getWordTarget()->getName();
					if (isset($relatedWords[$name]) ) {
						$relatedWords[$name] += $linkword->getWeight();
					}
					else {
						$relatedWords[$name] = $linkword->getWeight();
					}
				}
			}

		}

		if ($addWordText) {
			//Rationalize the weight with base 0.75 and 1 for words search
			$maxWeight = 0;
			foreach ($relatedWords as $key => $weightWord) {
				if ($maxWeight < $weightWord) {
					$maxWeight = $weightWord;
				}
			}
			//The first word transform weight on 0.75 or value in parameters.yml
			if ($this->container->hasParameter('relatedword.coeff_Base')) {
				$coeffBase = $this->container->getParameter('relatedword.coeff_Base');
			}
			else {
				$coeffBase = 0.75;
			}
			if ($maxWeight == 0) {
				$coeff = 1;
			}else {
				$coeff = $coeffBase / $maxWeight;
			}
			foreach ($relatedWords as $key => $weightWord) {
				$relatedWords[$key] = $weightWord * $coeff;
			}
			foreach ($arrayWord as $word) {
				$relatedWords[$word] = 1;
			}

		}
		//Add weight
		foreach ($relatedWords as $key => $weightWord) {
			$relatedWords[$key] *= $weight;
		}

		// sort by weight
		arsort($relatedWords);
		return $relatedWords;
	}

	 // intialize Database for the calcul Related Word
	public function calculRelatedword($articles, $tokenizer=null)
	{
		$ArticlewordMgr = ManagersManager::getManager()->getContainer()->get('seriel_related_word.article_word_manager');
		if (false) $ArticlewordMgr = new RelatedwordArticleWordManager();
		$ArticleLinkwordMgr = ManagersManager::getManager()->getContainer()->get('seriel_related_word.article_link_word_manager');
		if (false) $ArticleLinkwordMgr= new RelatedwordArticleLinkWordManager();
		$linkwordMgr = ManagersManager::getManager()->getContainer()->get('seriel_related_word.link_word_manager');
 	    if (false) $linkwordMgr = new RelatedwordLinkWordManager();
	    $wordMgr = ManagersManager::getManager()->getContainer()->get('seriel_related_word.word_manager');
	    if (false) $wordMgr = new RelatedwordWordManager();

		if ($tokenizer == null) {
			$tokenizer = new StopWordTokenizer('fr',true);
		}
		$nbTotalArticle = count($articles);
		$nbTotalArticleTraite = 0;

		foreach ($articles as $article ) {
			$id = $article->getId();
			echo "Article :  $id ". PHP_EOL;
			$this->saveArticleword($article, $tokenizer);

			// calcul for insert fo the intermediate table
			$ArticleLinkwordMgr->generateAllArticleLinkWordByArticle($article);

			$nbTotalArticleTraite ++;
			echo "Progression :  $nbTotalArticleTraite / $nbTotalArticle ". PHP_EOL;
		}
		$ArraywordId = array();
		$AllArticleWords = $ArticlewordMgr->getAllWordIdByArticles($articles);
		foreach ($AllArticleWords as $articleWord) {
			$ArraywordId[$articleWord->getWord()->getId()] = $articleWord->getWord()->getId();
		}
		//calcul quantity total for word
		$wordMgr->generateAllWordQuantityByWord($ArraywordId);
		//calcul LinkWord
		//$linkwordMgr->generateAllLinkWordByWord($ArraywordId);
	 }

	// Save related word for one article
	public function saveArticleword($article, $tokenizer)
	{
		$wordMgr = ManagersManager::getManager()->getContainer()->get('seriel_related_word.word_manager');
		if (false) $wordMgr = new RelatedwordWordManager();
		$ArticlewordMgr = ManagersManager::getManager()->getContainer()->get('seriel_related_word.article_word_manager');
		if (false) $ArticlewordMgr = new RelatedwordArticleWordManager();
		
		//text of title
		$arrayArticleTitre= $tokenizer->tokenize($article->getTitre());
		
		//text of chapeau
		$arrayArticleChapeau= $tokenizer->tokenize($article->getChapeauStriped());
		
		//text of content
		//merge word of content and title and chapeau
		$arrayArticle = $tokenizer->tokenize($article->getContentStriped() .' '. $article->getTitre().' '. $article->getChapeauStriped());
		
		$mapWordArticle = array();
		foreach ($arrayArticle as $word ) {
			if ($word != '') {
				isset($mapWordArticle[$word]) ? $mapWordArticle[$word] ++ : $mapWordArticle[$word] = 1;
			}
		}
		foreach ($mapWordArticle as $name => $nbTotal) {
			//Save in BD
			if (is_string($name)) {
				$nameString = $name;
			}else {
				$nameString= strval($name);
			}
			$word = $wordMgr->getWordByName($nameString);
			if ($word == null) {
				//if new word in BD
				$word = new Word();
				$word->setName($nameString);
			}
			if ($word->getId() == null) {
				$articleWord = null;
			}else {
				$articleWord = $ArticlewordMgr->getArticleWordByWordArticle($word->getId(),$article->getId());
			}
			if ($articleWord == null) {
				$articleWord = new ArticleWord();
				$articleWord->setArticle($article);
				$articleWord->setWord($word);
			}
			$articleWord->setQuantity($nbTotal);
			
			// calcul if word in title
			//Save in BD
			if (in_array($name, $arrayArticleTitre)) {
				$articleWord->setIntitle(true);
			}

			// calcul if word in chapeau
			//Save in BD
			if (in_array($name, $arrayArticleChapeau)) {
				$articleWord->setInchapeau(true);
			}

			try {
				$ArticlewordMgr->save($articleWord);
			} catch (Exception $e) {
			    echo 'Exception : ',  $e->getMessage(), "\n";
			}
		}
		try {
			$ArticlewordMgr->flush();
		} catch (Exception $e) {
			echo 'Exception : ',  $e->getMessage(), "\n";
		}
		
	}

	// View the related words of dandelion entity of article
	public function articleSemantics($article) {
		if (!$article) return '';
		if (false) $article = new Article();

		$templating = $this->container->get('templating');

		$semanticsMgr = $this->container->get('seriel_dandelion.article_semantics_manager');
		if (false) $semanticsMgr = new DandelionArticleSemanticsManager();

		$semantics = $semanticsMgr->getDandelionArticleSemanticsForArticleId($article->getId());
		if (false) $semantics = new DandelionArticleSemantics($article);

		$wordsearch = '';
		if ($semantics != null) {
			foreach ($semantics->getEntitiesTitle() as $entitie) {
				if ($wordsearch == '') {
					$wordsearch .= $entitie->getSpot();
				}else {
					$wordsearch .= ' '.$entitie->getSpot();
				}			
			}
			foreach ($semantics->getEntitiesChapeau() as $entitie) {
				if ($wordsearch == '') {
					$wordsearch .= $entitie->getSpot();
				}else {
					$wordsearch .= ' '.$entitie->getSpot();
				}
			}
		}
		$relatedWords = $this->getRelatedWord($wordsearch,10,1);
					
		$articlesManager = $this->container->get("articles_manager");
		if (false) $articlesManager = new ArticlesManager();
		
		$params = array('semantique_relatedword' => $wordsearch);
		$articlesSimilarity= $articlesManager->query($params, array('result_type' => 'search_object','limit' => 10));
		
		$wordsearch = explode(" ", $wordsearch);
		return $templating->render('SerielRelatedwordBundle:Article:article_semantics.html.twig', array(
			'article' => $article,
			'wordsearch' => $wordsearch,
			'relatedWords' => $relatedWords,
		    'articlesSimilarity' => $articlesSimilarity
		));
	}
	
	// return check list of import and calculate data for relatedword
	public function getStateImports() {
		$stateInports = array();
		
		$linkwordMgr = ManagersManager::getManager()->getContainer()->get('seriel_related_word.link_word_manager');
		if (false) $linkwordMgr = new RelatedwordLinkWordManager();
		
		$date = $linkwordMgr->getLastUpdateAt();
		if (isset($date)) {
			$stateInports[] = New StateImport('Relatedword - Dernière mise à jour', $date);
		}
		return $stateInports;
	}
}
