<?php

namespace Seriel\RelatedwordBundle\Tests\Managers;

use Seriel\RelatedwordBundle\Managers\RelatedwordManager;
use Seriel\RelatedwordBundle\Managers\RelatedwordWordManager;
use Seriel\RelatedwordBundle\Managers\RelatedwordArticleWordManager;
use Seriel\RelatedwordBundle\Managers\RelatedwordArticleLinkWordManager;
use Seriel\RelatedwordBundle\Managers\RelatedwordLinkWordManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use ZombieBundle\Utils\StopWordTokenizer;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use ZombieBundle\Entity\News\Article;
use ZombieBundle\Managers\News\ArticlesManager;

class RelatedwordWordManagerTest extends WebTestCase{
	

	public function testSaveArticleword()
	{
		$kernel = static::createKernel();
		$kernel->boot();
		$container = $kernel->getContainer();
		$ManagerManager= $container->get('managers_manager');
		$this->forceAuthenticate($container);
		
		$RelatedwordManager = $container->get('seriel_related_word.manager');
		$wordMgr= $container->get('seriel_related_word.word_manager');
		$ArticlewordMgr= $container->get('seriel_related_word.article_word_manager');
		$articleManager = $container->get('articles_manager');
		
		// test service exist
		$this->assertInstanceOf(RelatedwordManager::class,$RelatedwordManager);
		$this->assertInstanceOf(RelatedwordWordManager::class,$wordMgr);
		$this->assertInstanceOf(RelatedwordArticleWordManager::class,$ArticlewordMgr);
		$this->assertInstanceOf(ArticlesManager::class,$articleManager);
		
		//clear word
		$words  = $wordMgr->getAllWord();
		foreach($words as $word) {
			$wordMgr->remove($word);
		}
		$wordMgr->flush();
		//clear wordarticle
		$articlewords  = $ArticlewordMgr->getAllArticleWord();
		foreach($articlewords as $articleword) {
			$ArticlewordMgr->remove($articleword);
		}
		$ArticlewordMgr->flush();
		
		//clear article
		$article= $articleManager->getArticleForGuid('article_test_phpunit');
		if (isset($article)) {
			$articleManager->remove($article);
			$articleManager->flush();
		}
		//create article unique for test
		$article = new Article();
		$article->setGuid('article_test_phpunit');
		$article->setDateParution(new \DateTime());
		$article->setTitre('super titre');
		$article->setChapeauStriped('super chapeau moyen');
		$article->setChapeau('super chapeau moyen');
		$article->setContent('super contenu trop long ');
		$article->setContentStriped('super contenu trop long ');	
		$articleManager->save($article);
		$articleManager->flush();
		
		
		
		$tokenizer = new StopWordTokenizer('fr',true);
		$RelatedwordManager->saveArticleword($article,$tokenizer);
		
		//test quantity
		$articlewords  = $ArticlewordMgr->getAllArticleWord();
		$this->assertEquals(7, count($articlewords));
		$words  = $wordMgr->getAllWord();
		$this->assertEquals(7, count($words));
		
		foreach($articlewords as $articleword) {
			switch ($articleword->getWord()->getName()) {
				case 'sup':
					$this->assertEquals(0, $articleword->getWord()->getQuantity());
					$this->assertEquals(3, $articleword->getQuantity());
					$this->assertTrue($articleword->getIntitle());
					$this->assertTrue($articleword->getInchapeau());
					break;
				case 'titr':
					$this->assertEquals(0, $articleword->getWord()->getQuantity());
					$this->assertEquals(1, $articleword->getQuantity());
					$this->assertTrue($articleword->getIntitle());
					$this->assertFalse($articleword->getInchapeau());
					break;
				case 'chapeau':
					$this->assertEquals(0, $articleword->getWord()->getQuantity());
					$this->assertEquals(1, $articleword->getQuantity());
					$this->assertFalse($articleword->getIntitle());
					$this->assertTrue($articleword->getInchapeau());
					break;
				case 'contenu':
					$this->assertEquals(0, $articleword->getWord()->getQuantity());
					$this->assertEquals(1, $articleword->getQuantity());
					$this->assertFalse($articleword->getIntitle());
					$this->assertFalse($articleword->getInchapeau());
					break;
			}
		}
	}

	public function testCalculRelatedword()
	{
		$kernel = static::createKernel();
		$kernel->boot();
		$container = $kernel->getContainer();
		$ManagerManager= $container->get('managers_manager');
		$this->forceAuthenticate($container);
		
		$RelatedwordManager = $container->get('seriel_related_word.manager');
		$wordMgr= $container->get('seriel_related_word.word_manager');
		$ArticlewordMgr= $container->get('seriel_related_word.article_word_manager');
		$articleManager = $container->get('articles_manager');
		$ArticleLinkwordMgr =  $container->get('seriel_related_word.article_link_word_manager');
		$linkwordMgr = $container->get('seriel_related_word.link_word_manager');

		// test service exist
		$this->assertInstanceOf(RelatedwordManager::class,$RelatedwordManager);
		$this->assertInstanceOf(RelatedwordWordManager::class,$wordMgr);
		$this->assertInstanceOf(RelatedwordArticleWordManager::class,$ArticlewordMgr);
		$this->assertInstanceOf(ArticlesManager::class,$articleManager);
		$this->assertInstanceOf(RelatedwordArticleLinkWordManager::class,$ArticleLinkwordMgr);
		$this->assertInstanceOf(RelatedwordLinkWordManager::class,$linkwordMgr);
		
		//clear word
		$words  = $wordMgr->getAllWord();
		foreach($words as $word) {
			$wordMgr->remove($word);
		}
		$wordMgr->flush();
		//clear wordarticle
		$articlewords  = $ArticlewordMgr->getAllArticleWord();
		foreach($articlewords as $articleword) {
			$ArticlewordMgr->remove($articleword);
		}
		$ArticlewordMgr->flush();
		
		//clear article
		$article= $articleManager->getArticleForGuid('article_test_phpunit_1');
		if (isset($article)) {
			$articleManager->remove($article);
			$articleManager->flush();
		}
		$article= $articleManager->getArticleForGuid('article_test_phpunit_2');
		if (isset($article)) {
			$articleManager->remove($article);
			$articleManager->flush();
		}
		//clear article link word
		$ArticleLinkWords =$ArticleLinkwordMgr->getAllArticleLinkWord();
		foreach ($ArticleLinkWords as $ArticleLinkWord) {
			$ArticleLinkwordMgr->remove($ArticleLinkWord);

		}
		$ArticleLinkwordMgr->flush();
		//clear link word
		$linkWords = $linkwordMgr->getAllLinkWord();
		foreach ($linkWords as $linkWord) {
			$linkwordMgr->remove($linkWord);
			
		}
		$linkwordMgr->flush();
		
		//create article unique for test
		$article1 = new Article();
		$article1->setGuid('article_test_phpunit_1');
		$article1->setDateParution(new \DateTime('2017-01-01'));
		$article1->setTitre('super titre');
		$article1->setChapeauStriped('super chapeau moyen');
		$article1->setChapeau('super chapeau moyen');
		$article1->setContent('super contenu trop long ');
		$article1->setContentStriped('super contenu trop long ');
		$articleManager->save($article1);
		$article2 = new Article();
		$article2->setGuid('article_test_phpunit_2');
		$article2->setDateParution(new \DateTime('2017-01-03'));
		$article2->setTitre('article titre');
		$article2->setChapeauStriped('super chapeau fort');
		$article2->setChapeau('super chapeau fort');
		$article2->setContent('super contenu trop court ');
		$article2->setContentStriped('super contenu trop court ');
		$articleManager->save($article2);
		$articleManager->flush();
		
		$articles= array($article1, $article2);
		$RelatedwordManager->calculRelatedword($articles);
		
		// Test calcul for insert fo the intermediate table
		//$ArticleLinkwordMgr->generateAllArticleLinkWordByArticle($article);
		$countfordouble = 0;
		$ArticleLinkWords =$ArticleLinkwordMgr->getAllArticleLinkWord();
		foreach ($ArticleLinkWords as $ArticleLinkWord) {
			if($ArticleLinkWord->getWordSource()->getName() == 'sup' AND $ArticleLinkWord->getWordTarget()->getName() == 'titr' AND $ArticleLinkWord->getArticle()->getId() == $article1->getId()) {
				$this->assertEquals(15, $ArticleLinkWord->getWeight());
				$countfordouble ++;
			}
		}
		$this->assertEquals(1, $countfordouble);
		$this->assertEquals(49, count($ArticleLinkWords));
		
		// Test calcul total quantity word
		$words  = $wordMgr->getAllWord();
		$this->assertEquals(10, count($words));
		
		foreach($words as $word) {
			$wordMgr->refresh($word);
			switch ($word->getName()) {
				case 'sup':
					$this->assertEquals(5, $word->getQuantity());
					break;
				case 'titr':
					$this->assertEquals(2, $word->getQuantity());
					break;
				case 'chapeau':
					$this->assertEquals(2, $word->getQuantity());
					break;
				case 'contenu':
					$this->assertEquals(2, $word->getQuantity());
					break;
				case 'articl':	
					$this->assertEquals(1, $word->getQuantity());
					break;
					
			}
		}
		
		// Test calcul link word
		//$linkwordMgr->generateAllLinkWordByWord($ArraywordId);
		$linkWords = $linkwordMgr->getAllLinkWord();
		$this->assertEquals(78, count($linkWords));
		
		$countfordouble = 0;
		foreach($linkWords as $linkWord) {
			if($linkWord->getWordSource()->getName() == 'sup' AND $linkWord->getWordTarget()->getName() == 'titr') {
				//(7+15)/sqrt(2) = 15,556349186
				$this->assertEquals(15, floor($linkWord->getWeight()));
				$countfordouble ++;
			}
			if($linkWord->getWordSource()->getName() == 'titr' AND $linkWord->getWordTarget()->getName() == 'sup') {
				//(7+15)/sqrt(5) = 9,838699101
				$this->assertEquals(9, floor($linkWord->getWeight()));
				$countfordouble ++;
			}
		}
		$this->assertEquals(2, $countfordouble);
		
	}	
	
	public function testGetRelatedWord() {
		
		$kernel = static::createKernel();
		$kernel->boot();
		$container = $kernel->getContainer();
		$ManagerManager= $container->get('managers_manager');
		$this->forceAuthenticate($container);
		
		$RelatedwordManager = $container->get('seriel_related_word.manager');
		$wordMgr= $container->get('seriel_related_word.word_manager');
		$ArticlewordMgr= $container->get('seriel_related_word.article_word_manager');
		$articleManager = $container->get('articles_manager');
		$ArticleLinkwordMgr =  $container->get('seriel_related_word.article_link_word_manager');
		$linkwordMgr = $container->get('seriel_related_word.link_word_manager');
		
		$coeffBase = $container->getParameter('relatedword.coeff_Base');
		if (!isset($coeffBase)) $coeffBase = 0.75;
		// test service exist
		$this->assertInstanceOf(RelatedwordManager::class,$RelatedwordManager);
		$this->assertInstanceOf(RelatedwordWordManager::class,$wordMgr);
		$this->assertInstanceOf(RelatedwordArticleWordManager::class,$ArticlewordMgr);
		$this->assertInstanceOf(ArticlesManager::class,$articleManager);
		$this->assertInstanceOf(RelatedwordArticleLinkWordManager::class,$ArticleLinkwordMgr);
		$this->assertInstanceOf(RelatedwordLinkWordManager::class,$linkwordMgr);
		
		//clear word
		$words  = $wordMgr->getAllWord();
		foreach($words as $word) {
			$wordMgr->remove($word);
		}
		$wordMgr->flush();
		//clear wordarticle
		$articlewords  = $ArticlewordMgr->getAllArticleWord();
		foreach($articlewords as $articleword) {
			$ArticlewordMgr->remove($articleword);
		}
		$ArticlewordMgr->flush();
		
		//clear article
		$article= $articleManager->getArticleForGuid('article_test_phpunit_1');
		if (isset($article)) {
			$articleManager->remove($article);
			$articleManager->flush();
		}
		$article= $articleManager->getArticleForGuid('article_test_phpunit_2');
		if (isset($article)) {
			$articleManager->remove($article);
			$articleManager->flush();
		}
		//clear article link word
		$ArticleLinkWords =$ArticleLinkwordMgr->getAllArticleLinkWord();
		foreach ($ArticleLinkWords as $ArticleLinkWord) {
			$ArticleLinkwordMgr->remove($ArticleLinkWord);
			
		}
		$ArticleLinkwordMgr->flush();
		//clear link word
		$linkWords = $linkwordMgr->getAllLinkWord();
		foreach ($linkWords as $linkWord) {
			$linkwordMgr->remove($linkWord);
			
		}
		$linkwordMgr->flush();
		//create article unique for test
		$article1 = new Article();
		$article1->setGuid('article_test_phpunit_1');
		$article1->setDateParution(new \DateTime('2017-01-01'));
		$article1->setTitre('super titre');
		$article1->setChapeauStriped('super chapeau moyen');
		$article1->setChapeau('super chapeau moyen');
		$article1->setContent('super contenu trop long ');
		$article1->setContentStriped('super contenu trop long ');
		$articleManager->save($article1);
		$article2 = new Article();
		$article2->setGuid('article_test_phpunit_2');
		$article2->setDateParution(new \DateTime('2017-01-03'));
		$article2->setTitre('article titre');
		$article2->setChapeauStriped('super chapeau fort');
		$article2->setChapeau('super chapeau fort');
		$article2->setContent('super contenu trop court ');
		$article2->setContentStriped('super contenu trop court ');
		$articleManager->save($article2);
		$articleManager->flush();
		$articles= array($article1, $article2);
		$RelatedwordManager->calculRelatedword($articles);
		
		
		//test text parameter
		//$text,$nbRelatedWordbyWord,$weight,$addWordText = false
		$relatedWords = $RelatedwordManager->getRelatedWord('super',10,1);
		$this->assertEquals(9, count($relatedWords));
		// verify contains
		$this->assertEquals(15,floor($relatedWords["titr"]));
		$this->assertEquals(13,floor($relatedWords["chapeau"]));
		$this->assertEquals(13,floor($relatedWords["moyen"]));
		$this->assertEquals(10,floor($relatedWords["long"]));
		$this->assertEquals(10,floor($relatedWords["trop"]));
		$this->assertEquals(10,floor($relatedWords["contenu"]));
		$this->assertEquals(7,floor($relatedWords["articl"]));
		$this->assertEquals(6,floor($relatedWords["fort"]));
		$this->assertEquals(4,floor($relatedWords["court"]));
	
		
		$relatedWords = $RelatedwordManager->getRelatedWord('ananas',10,1);
		$this->assertEquals(0, count($relatedWords));
		$relatedWords = $RelatedwordManager->getRelatedWord('',10,1);
		$this->assertEquals(0, count($relatedWords));
		$relatedWords = $RelatedwordManager->getRelatedWord(57,10,1);
		$this->assertEquals(0, count($relatedWords));
		$relatedWords = $RelatedwordManager->getRelatedWord(null,10,1);
		$this->assertEquals(0, count($relatedWords));

		//test depth parameter
		$relatedWords = $RelatedwordManager->getRelatedWord('super',5,1);
		$this->assertEquals(5, count($relatedWords));
		$relatedWords = $RelatedwordManager->getRelatedWord('super',0,1);
		$this->assertEquals(0, count($relatedWords));
		$relatedWords = $RelatedwordManager->getRelatedWord('super',null,1);
		$this->assertEquals(0, count($relatedWords));
		
		//test weight parameter
		$relatedWords = $RelatedwordManager->getRelatedWord('super',10,2);
		$this->assertEquals(31,floor($relatedWords["titr"]));
		$this->assertEquals(27,floor($relatedWords["chapeau"]));
		$this->assertEquals(27,floor($relatedWords["moyen"]));
		$this->assertEquals(21,floor($relatedWords["long"]));
		$this->assertEquals(20,floor($relatedWords["trop"]));
		$this->assertEquals(20,floor($relatedWords["contenu"]));
		$this->assertEquals(14,floor($relatedWords["articl"]));
		$this->assertEquals(12,floor($relatedWords["fort"]));
		$this->assertEquals(8,floor($relatedWords["court"]));
		
		//test $addWordText parameter
		$relatedWords = $RelatedwordManager->getRelatedWord('super',10,1,true);
		$this->assertEquals(1,$relatedWords["sup"]);

		$this->assertEquals($coeffBase,$relatedWords["titr"]);
		$this->assertEquals(0.4,round($relatedWords["chapeau"],1));
		$this->assertEquals(0.4,round($relatedWords["moyen"],1));
		$this->assertEquals(0.3,round($relatedWords["long"],1));
		$this->assertEquals(0.3,round($relatedWords["trop"],1));
		$this->assertEquals(0.3,round($relatedWords["contenu"],1));
		$this->assertEquals(0.2,round($relatedWords["articl"],1));
		$this->assertEquals(0.2,round($relatedWords["fort"],1));
		$this->assertEquals(0.1,round($relatedWords["court"],1));
		
		
		//test getSearchSemantiqueMultipleWord
		$relatedWords = $RelatedwordManager->getSearchSemantiqueMultipleWord('super article ananas',3,1);
		$this->assertEquals(3,count($relatedWords));
		$this->assertEquals(1,$relatedWords[0]["sup"]);
		$this->assertEquals($coeffBase,$relatedWords[0]["titr"]);
		$this->assertEquals(0.4,round($relatedWords[0]["chapeau"],1));
		$this->assertEquals(1,$relatedWords[1]["articl"]);
		$this->assertEquals($coeffBase,$relatedWords[1]["fort"]);
		$this->assertEquals(0.4,round($relatedWords[1]["sup"],1));
		$this->assertEquals(1,$relatedWords[2]["anan"]);

	}

	public function testGetSimilarity() {
		
		$kernel = static::createKernel();
		$kernel->boot();
		$container = $kernel->getContainer();
		$ManagerManager= $container->get('managers_manager');
		$this->forceAuthenticate($container);
		
		$RelatedwordManager = $container->get('seriel_related_word.manager');
		$wordMgr= $container->get('seriel_related_word.word_manager');
		$ArticlewordMgr= $container->get('seriel_related_word.article_word_manager');
		$articleManager = $container->get('articles_manager');
		$ArticleLinkwordMgr =  $container->get('seriel_related_word.article_link_word_manager');
		$linkwordMgr = $container->get('seriel_related_word.link_word_manager');
		
		$coeffBase = $container->getParameter('relatedword.coeff_Base');
		if (!isset($coeffBase)) $coeffBase = 0.75;
		// test service exist
		$this->assertInstanceOf(RelatedwordManager::class,$RelatedwordManager);
		$this->assertInstanceOf(RelatedwordWordManager::class,$wordMgr);
		$this->assertInstanceOf(RelatedwordArticleWordManager::class,$ArticlewordMgr);
		$this->assertInstanceOf(ArticlesManager::class,$articleManager);
		$this->assertInstanceOf(RelatedwordArticleLinkWordManager::class,$ArticleLinkwordMgr);
		$this->assertInstanceOf(RelatedwordLinkWordManager::class,$linkwordMgr);
		
		//clear word
		$words  = $wordMgr->getAllWord();
		foreach($words as $word) {
			$wordMgr->remove($word);
		}
		$wordMgr->flush();
		//clear wordarticle
		$articlewords  = $ArticlewordMgr->getAllArticleWord();
		foreach($articlewords as $articleword) {
			$ArticlewordMgr->remove($articleword);
		}
		$ArticlewordMgr->flush();
		
		//clear article
		$article= $articleManager->getArticleForGuid('article_test_phpunit_1');
		if (isset($article)) {
			$articleManager->remove($article);
			$articleManager->flush();
		}
		$article= $articleManager->getArticleForGuid('article_test_phpunit_2');
		if (isset($article)) {
			$articleManager->remove($article);
			$articleManager->flush();
		}
		//clear article link word
		$ArticleLinkWords =$ArticleLinkwordMgr->getAllArticleLinkWord();
		foreach ($ArticleLinkWords as $ArticleLinkWord) {
			$ArticleLinkwordMgr->remove($ArticleLinkWord);
			
		}
		$ArticleLinkwordMgr->flush();
		//clear link word
		$linkWords = $linkwordMgr->getAllLinkWord();
		foreach ($linkWords as $linkWord) {
			$linkwordMgr->remove($linkWord);
			
		}
		$linkwordMgr->flush();
		//create article unique for test
		$article1 = new Article();
		$article1->setGuid('article_test_phpunit_1');
		$article1->setDateParution(new \DateTime('2017-01-01'));
		$article1->setTitre('super titre');
		$article1->setChapeauStriped('super chapeau moyen');
		$article1->setChapeau('super chapeau moyen');
		$article1->setContent('super contenu trop long ');
		$article1->setContentStriped('super contenu trop long ');
		$articleManager->save($article1);
		$article2 = new Article();
		$article2->setGuid('article_test_phpunit_2');
		$article2->setDateParution(new \DateTime('2017-01-03'));
		$article2->setTitre('article titre');
		$article2->setChapeauStriped('super chapeau fort');
		$article2->setChapeau('super chapeau fort');
		$article2->setContent('super contenu trop court ');
		$article2->setContentStriped('super contenu trop court ');
		$articleManager->save($article2);
		$articleManager->flush();
		$articles= array($article1, $article2);
		$RelatedwordManager->calculRelatedword($articles);
		
		// test limit
		$similarity = $RelatedwordManager->getSimilarity('','');
		$this->assertEquals(100,$similarity);
		$similarity = $RelatedwordManager->getSimilarity('article','titre',0);
		$this->assertEquals(0,$similarity);
		$similarity = $RelatedwordManager->getSimilarity('ananas','zoro');
		$this->assertEquals(0,$similarity);
		$similarity = $RelatedwordManager->getSimilarity('article','');
		$this->assertEquals(0,$similarity);
		
		//test value
		$similarity = $RelatedwordManager->getSimilarity('titre','super',10);
		$this->assertEquals(80,$similarity);
		$similarity = $RelatedwordManager->getSimilarity('titre','super',2);
		$this->assertEquals(50,$similarity);
		$similarity = $RelatedwordManager->getSimilarity('article','super',10);
		$this->assertEquals(60,$similarity);
		$similarity = $RelatedwordManager->getSimilarity('article','super',1);
		$this->assertEquals(0,$similarity);

		
	}

	private function forceAuthenticate($container) {
		$em = $container->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);
		
		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}

}
