<?php

namespace Seriel\RelatedwordBundle\Managers;


use Seriel\RelatedwordBundle\Managers\RelatedwordArticleWordManager;
use ZombieBundle\API\Managers\ManagerSemantique;
use Seriel\AppliToolboxBundle\Managers\ManagersManager;

class RelatedwordDandelionManager implements ManagerSemantique{

	protected $container = null;
	protected $logger = null;


	public function __construct($container, $logger) {
		$this->container = $container;
		$this->logger = $logger;

	}

	// get list article_id order by Weight with multiple subjects
	public function getArticlebySearchSemantiqueWithSubjects($subjects)
	{
		$relatedWordMgr = ManagersManager::getManager()->getContainer()->get('seriel_related_word.manager');
		if (false) $relatedWordMgr= new RelatedwordManager();
		$ArticlewordMgr = ManagersManager::getManager()->getContainer()->get('seriel_related_word.article_word_manager');
		if (false) $ArticlewordMgr = new RelatedwordArticleWordManager();
		$NbSubjects = count($subjects);
		if($subjects== 0) return array();
		
		$AllSubjectsRelativeWord = array();
		// for each subject, return the best articles with search semantique
		foreach ($subjects as $subject => $weightSubject) {
			$relatedwordBySubject = $relatedWordMgr->getSearchSemantiqueMultipleWord($subject,0,$weightSubject);
			$AllSubjectsRelativeWord = array_merge($AllSubjectsRelativeWord,$relatedwordBySubject);
		}
		if(count($AllSubjectsRelativeWord) == 0) return false;
		return $ArticlewordMgr->getResultSubQuerySearchSemantiquebyMultipleWord($AllSubjectsRelativeWord,false, true);
	}
	
	// get list article_id order by Weight with multiple wordsearch or one wordsearch
	public function getArticlebySearchSemantique($text)
	{
		$relatedWordMgr = ManagersManager::getManager()->getContainer()->get('seriel_related_word.manager');
		if (false) $relatedWordMgr= new RelatedwordManager();
		$ArticlewordMgr = ManagersManager::getManager()->getContainer()->get('seriel_related_word.article_word_manager');
		if (false) $ArticlewordMgr = new RelatedwordArticleWordManager();
		
		$relatedwordByWordSearch = $relatedWordMgr->getSearchSemantiqueMultipleWord($text,0);
		if(count($relatedwordByWordSearch) == 0) return array();
		return $ArticlewordMgr->getResultSubQuerySearchSemantiquebyMultipleWord($relatedwordByWordSearch,false, true);
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


}
