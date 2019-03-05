<?php

namespace Seriel\DandelionBundle\Tests\Managers;

use Seriel\DandelionBundle\Managers\DandelionManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use ZombieBundle\Entity\News\Article;
use ZombieBundle\Managers\News\ArticlesManager;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use Seriel\DandelionBundle\Entity\DandelionArticleSemantics;

class DandelionManagerTest extends WebTestCase{
	
	private $container;
	private $DandelionManager;
	
	public function __construct()
	{
		$kernel = static::createKernel();
		$kernel->boot();
		$GLOBALS['kernel'] = $kernel;
		$this->container = $kernel->getContainer();
		$ManagerManager= $this->container->get('managers_manager');
		$this->forceAuthenticate($this->container);
		$this->DandelionManager= $this->container->get('seriel_dandelion.manager');
	}

	public function testEntityExtraction() {
		$this->assertInstanceOf(DandelionManager::class,$this->DandelionManager);
		
		//test limit
		$result = $this->DandelionManager->entityExtraction(''); 
		$this->assertEquals(0,count($result));
		
		$result = $this->DandelionManager->entityExtraction(null);
		$this->assertEquals(0,count($result));
	

		//test value
		$result = $this->DandelionManager->entityExtraction('dhgezhrehre', array());
		$result = json_decode($result, true);
		$this->assertEquals(0,count($result['annotations']));
		
		$result = $this->DandelionManager->entityExtraction('salut Donald Trump', array());
		$result = json_decode($result, true);
		$this->assertEquals(1,count($result['annotations']));
		
		$result = $this->DandelionManager->entityExtraction('Donald Trump salut Donald Trump', array());
		$result = json_decode($result, true);
		$this->assertEquals(2,count($result['annotations']));
	
	}
	
	public function testEntityExtractionFromArticle () {
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
		$article2 = new Article();
		$article2->setGuid('article_test_phpunit_2');
		$article2->setDateParution(new \DateTime('2017-01-03'));
		$article2->setTitre('article titre Donald Trump');
		$article2->setSource('01');
		$article2->setUris(array('/home'));
		$article2->setChapeauStriped('super chapeau fort');
		$article2->setChapeau('super chapeau fort');
		$article2->setContent('super contenu trop court Donald Trump');
		$article2->setContentStriped('super contenu trop court Donald Trump');
		$articleManager->save($article2);
		$articleManager->flush();
	
		$result = $this->DandelionManager->entityExtractionFromArticle($article1);
		$this->assertInstanceOf(DandelionArticleSemantics::class,$result);
		$this->assertEquals($article1->getId(), $result->getArticle()->getId());
		$this->assertEquals(new \DateTime('2017-01-01'), $result->getDateParution());
		$this->assertEquals(new \DateTime(), $result->getDateCalcul());
		$this->assertEquals('', $result->getTitre());
		$this->assertEquals(array(), $result->getTitreSemantics());
		$this->assertEquals('', $result->getChapeau());
		$this->assertEquals(array(), $result->getChapeauSemantics());
		$this->assertEquals('', $result->getContent());
		$this->assertEquals(array(), $result->getContentSemantics());
		
		$result = $this->DandelionManager->entityExtractionFromArticle($article2);
		$this->assertInstanceOf(DandelionArticleSemantics::class,$result);
		$this->assertEquals($article2->getId(), $result->getArticle()->getId());
		$this->assertEquals(new \DateTime('2017-01-03'), $result->getDateParution());
		$this->assertEquals(new \DateTime(), $result->getDateCalcul());
		$this->assertEquals('article titre Donald Trump', $result->getTitre());
		
		$this->assertEquals(1, count(json_decode($result->getTitreSemantics(), true)['annotations']) );
		$this->assertEquals('super chapeau fort', $result->getChapeau());
		$this->assertEquals(array(), json_decode($result->getChapeauSemantics(), true)['annotations']) ;
		$this->assertEquals('super contenu trop court Donald Trump', $result->getContent());
		$this->assertEquals(2, count(json_decode($result->getContentSemantics(), true)['annotations']));

	}

	private function forceAuthenticate($container) {
		$em = $container->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);
		
		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}

}
