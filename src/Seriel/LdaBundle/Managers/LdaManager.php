<?php

namespace Seriel\LdaBundle\Managers;

use ZombieBundle\Entity\News\Article;
use Seriel\AppliToolboxBundle\Managers\ManagersManager;
use NlpTools\Documents\TrainingSet;
use NlpTools\Documents\TokensDocument;
use NlpTools\FeatureFactories\DataAsFeatures;
use NlpTools\Models\Lda;
use ZombieBundle\Utils\StopWordTokenizer;
use Seriel\LdaBundle\Managers\LdaWordManager;

class LdaManager {

	protected $container = null;
	protected $logger = null;


	public function __construct($container, $logger) {
		$this->container = $container;
		$this->logger = $logger;

	}

	// intialize array for the calcul LDA
	public function getDataLda($articles, $nbsubject, $tokenizer=null)
    {
		   if ($tokenizer == null) {
			  $tokenizer = new StopWordTokenizer('fr');
		   }
           $docs = new TrainingSet();

		   foreach ($articles as $article ) {

			 $texteArticle = strtolower($article->getContentStriped());
			 $arrayArticle = $tokenizer->tokenize($texteArticle);
			   $docs->addDocument(
			       "",
			       new TokensDocument(
			    		$arrayArticle
			       )
			   );
		   }
           $lda = new Lda(
               new DataAsFeatures(),
               $nbsubject,
               1,
               1
           );
           $lda->train($docs, 50);

		   return $lda;
     }

	 // Insert data of getWordsPerTopicsProbabilities
	 public function recWordsPerTopicsProbabilities($DataLda)
     {
		 $wordDone = array();
		 $dateCalcul = new \Datetime();
		 $wordMgr = ManagersManager::getManager()->getContainer()->get('seriel_lda.lda_word_manager');
     	 if (false) $wordMgr = new LdaWordManager();

		 foreach ($DataLda->getWordsPerTopicsProbabilities() as $topicCalcul) {
			$topic = new \Seriel\LdaBundle\Entity\Topic();
			$topic->setCalculateAt($dateCalcul);
			//if count($topicCalcul) > 2 {
				$topic->setName('');
 			foreach ($topicCalcul as $key => $value) {
				if (is_string($key)) {
					$wordName = $key;
				}else {
					$wordName = strval($key);
				}
				$word = $wordMgr->getWordByName($wordName);
				if ($word == null) {
					//search if word is in list pre-save
					$word = isset($wordDone[$wordName]) ? $wordDone[$wordName] : null;

					if ($word == null) {
						//new word
						$word = new \Seriel\LdaBundle\Entity\Word();
						$word->setName($wordName);
						$wordDone [$wordName] = $word;
					}
				}
				$topicWord = new \Seriel\LdaBundle\Entity\TopicWord();
				$topicWord->setTopic($topic);
				$topicWord->setWord($word);
				$topicWord->setWeight($value);
				$topic->addTopicWord($topicWord);
 			}
 			$wordMgr->save($topic);
 		}
		$wordMgr->flush();
	 }

	 // remove all data lda with a date $datelimit
	 public function removeDataCalculate($datelimit)
     {
		 $topicMgr = ManagersManager::getManager()->getContainer()->get('seriel_lda.lda_topic_manager');
     	 if (false) $topicMgr = new LdaTopicManager();

		 $topics = $topicMgr->getTopicByDateLimit($datelimit);
		 foreach ($topics as $topic) {
		 	$topicMgr->remove($topic);
		 }
		 $topicMgr->flush();
	 }

}
