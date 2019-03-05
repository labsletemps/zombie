<?php

namespace Seriel\DandelionBundle\Tests\Entity;

use Seriel\DandelionBundle\Entity\DandelionArticleSemantics;
use Seriel\DandelionBundle\Entity\DandelionArticleEntityLink;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use ZombieBundle\Entity\News\Article;
use ZombieBundle\Managers\News\ArticlesManager;

class DandelionArticleSemanticsTest extends WebTestCase{
	
	private $container;
	
	public function __construct()
	{
		$kernel = static::createKernel();
		$kernel->boot();
		$GLOBALS['kernel'] = $kernel;
		$this->container = $kernel->getContainer();
		$ManagerManager= $this->container->get('managers_manager');
		$this->forceAuthenticate($this->container);
		
	}

	public function testDealWithEntitiesFromAnnotations()
	{

		$articleManager = $this->container->get('articles_manager');
		$this->assertInstanceOf(ArticlesManager::class,$articleManager);
		//clear article
		$articles= $articleManager->getAllArticles();
		foreach ($articles as $article){
			$articleManager->remove($article);
		}
		
		//create article unique for test
		$article1 = new Article();
		$article1->setGuid('article_test_phpunit_1');
		$article1->setDateParution(new \DateTime('2017-01-01'));
		$article1->setTitre('');
		$article1->setChapeauStriped('');
		$article1->setChapeau('');
		$article1->setContent('');
		$article1->setContentStriped('');
		$articleManager->save($article1);
		$articleManager->flush();
		
		$DandelionArticleSemantics = new DandelionArticleSemantics($article1);
		$DandelionArticleSemantics->setDateCalcul(new \DateTime());

		$entitiesHash = $DandelionArticleSemantics->dealWithEntitiesFromAnnotations(array());
		$this->assertEquals(null, $entitiesHash);
	
		$annotations =array();
		$annotations["id"] = 222336;
		$annotations["start"] = 14;
		$annotations["end"] = 26;
		$annotations["spot"] = "Donald Trump";
		$annotations["confidence"] = 1;
		$annotations["title"] = "Donald Trump";
		$annotations["uri"] = "http://ro.wikipedia.org/wiki/Donald_Trump";
		$annotations["label"] = "Donald Trump";
		$annotations["categories"] = array();
		$annotations["types"] = array("http://dbpedia.org/ontology/Person","http://dbpedia.org/ontology/Agent");
		$annotations["alternateLabels"] = array();
		$annotations["lod"] = array("dbpedia"=>"http://dbpedia.org/resource/Donald_Trump","wikipedia"=>"http://ro.wikipedia.org/wiki/Donald_Trump");
		
		
		$annotations= array($annotations);
		$entities= $DandelionArticleSemantics->dealWithEntitiesFromAnnotations($annotations);
		$this->assertEquals(1, count($entities));
		foreach ($entities as $entitie) {
			$this->assertEquals(222336, $entitie->getDandelionId());
			$this->assertEquals("Donald Trump", $entitie->getTitle());
			$this->assertEquals("http://ro.wikipedia.org/wiki/Donald_Trump", $entitie->getUri());
			$this->assertEquals("Donald Trump", $entitie->getLabel());
			$this->assertEquals("Donald Trump", $entitie->getLabel());
			$this->assertEquals("Person", $entitie->getTypes()->toArray()[0]->getName());
		}

	}
	
	public function testLinkEntities()
	{
		
		$articleManager = $this->container->get('articles_manager');
		$this->assertInstanceOf(ArticlesManager::class,$articleManager);
		//clear article
		$articles= $articleManager->getAllArticles();
		foreach ($articles as $article){
			$articleManager->remove($article);
		}
		
		//create article unique for test
		$article1 = new Article();
		$article1->setGuid('article_test_phpunit_1');
		$article1->setDateParution(new \DateTime('2017-01-01'));
		$article1->setTitre('');
		$article1->setChapeauStriped('');
		$article1->setChapeau('');
		$article1->setContent('');
		$article1->setContentStriped('');
		$articleManager->save($article1);
		$articleManager->flush();
		
		$DandelionArticleSemantics = new DandelionArticleSemantics($article1);
		$DandelionArticleSemantics->setDateCalcul(new \DateTime());

		
		$entitiesHash = $DandelionArticleSemantics->dealWithEntitiesFromAnnotations(array());
		$this->assertEquals(null, $entitiesHash);
		
		$DandelionArticleSemantics->linkEntities(DandelionArticleEntityLink::TEXT_TYPE_TITLE, array(), $entitiesHash);
		
		$annotations =array();
		$annotations["id"] = 222336;
		$annotations["start"] = 14;
		$annotations["end"] = 26;
		$annotations["spot"] = "Donald Trump";
		$annotations["confidence"] = 1;
		$annotations["title"] = "Donald Trump";
		$annotations["uri"] = "http://ro.wikipedia.org/wiki/Donald_Trump";
		$annotations["label"] = "Donald Trump";
		$annotations["categories"] = array();
		$annotations["types"] = array("http://dbpedia.org/ontology/Person","http://dbpedia.org/ontology/Agent");
		$annotations["alternateLabels"] = array();
		$annotations["lod"] = array("dbpedia"=>"http://dbpedia.org/resource/Donald_Trump","wikipedia"=>"http://ro.wikipedia.org/wiki/Donald_Trump");
		
		$annotations= array($annotations);
		$entities= $DandelionArticleSemantics->dealWithEntitiesFromAnnotations($annotations);
		$DandelionArticleSemantics->linkEntities(DandelionArticleEntityLink::TEXT_TYPE_TITLE, $annotations, $entities);
		
		$this->assertEquals(1,count($DandelionArticleSemantics->getEntities()));
		foreach ($DandelionArticleSemantics->getEntities() as $entitiesLink){
			$this->assertEquals(14,$entitiesLink->getStart());
			$this->assertEquals(26,$entitiesLink->getEnd());
			$this->assertEquals(1,$entitiesLink->getConfidence());
			$this->assertEquals("Donald Trump",$entitiesLink->getSpot());

		}
	}

	private function forceAuthenticate($container) {
		$em = $container->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);
		
		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}
}
