<?php

namespace Seriel\CrossIndicatorBundle\Tests\Managers;

use Seriel\CrossIndicatorBundle\Managers\CrossIndicatorManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use ZombieBundle\Entity\News\Article;
use ZombieBundle\Managers\News\ArticlesManager;


class CrossIndicatorManagerTest extends WebTestCase{
	
	private $container;
	private $CrossIndicatorManager;
	
	public function __construct()
	{
		$kernel = static::createKernel();
		$kernel->boot();
		$GLOBALS['kernel'] = $kernel;
		$this->container = $kernel->getContainer();
		$ManagerManager= $this->container->get('managers_manager');
		$this->forceAuthenticate($this->container);
		$this->CrossIndicatorManager= $this->container->get('seriel_cross_indicator.manager');

	}

	//parameters generatevalue
	public function testGenerateValue() {
		$this->assertInstanceOf(CrossIndicatorManager::class,$this->CrossIndicatorManager);
		
		//test limit
		$calcul = '';
		$indicatorsMetrics = array();
		$measuresMetrics= array();
		$value = $this->CrossIndicatorManager->generateValue($calcul,$indicatorsMetrics,$measuresMetrics);
		$this->assertEquals(null,$value);
		
		$calcul = null;
		$indicatorsMetrics = array();
		$measuresMetrics= array();
		$value = $this->CrossIndicatorManager->generateValue($calcul,$indicatorsMetrics,$measuresMetrics);
		$this->assertEquals(null,$value);
		
		//testvalue
		$calcul ='(((5 + 2) * 1) - 1) / 1';
		$indicatorsMetrics = array();
		$measuresMetrics= array();
		$value = $this->CrossIndicatorManager->generateValue($calcul,$indicatorsMetrics,$measuresMetrics);
		$this->assertEquals(6,$value);
		
		$calcul ='((( $5 + 2) * 1) - 1$) / 1';
		$indicatorsMetrics = array('indicator1'=> 5, 'indicator2'=> 7);
		$measuresMetrics= array('measure1'=> 4, 'measure2'=> 6);
		$value = $this->CrossIndicatorManager->generateValue($calcul,$indicatorsMetrics,$measuresMetrics);
		$this->assertEquals(null,$value);
		
		$calcul ='((( $indicator1$ + 2) * 1) - $measure2$) / 1';
		$indicatorsMetrics = array('indicator1'=> 5, 'indicator2'=> 7);
		$measuresMetrics= array('measure1'=> 4, 'measure2'=> 6);
		$value = $this->CrossIndicatorManager->generateValue($calcul,$indicatorsMetrics,$measuresMetrics);
		$this->assertEquals(1,$value);
	}

	//parameters precalculateArticles
	public function testPrecalculateArticles() {
		
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
		$article1->setTitre('super titre');
		$article1->setUris(array('/article_test_phpunit_1/home','/article_test_phpunit_1/ananas'));
		$article1->setChapeauStriped('super chapeau moyen');
		$article1->setChapeau('super chapeau moyen');
		$article1->setContent('super contenu trop long ');
		$article1->setContentStriped('super contenu trop long ');
		$articleManager->save($article1);
		$article2 = new Article();
		$article2->setGuid('article_test_phpunit_2');
		$article2->setDateParution(new \DateTime('2017-01-03'));
		$article2->setTitre('article titre');
		$article2->setUris(array('/article_test_phpunit_2/home','/article_test_phpunit_2/ananas'));
		$article2->setChapeauStriped('super chapeau fort');
		$article2->setChapeau('super chapeau fort');
		$article2->setContent('super contenu trop court ');
		$article2->setContentStriped('super contenu trop court ');
		$articleManager->save($article2);
		$articleManager->flush();
		
		$articles = array($article1,$article2);
		$crossIndicatorarticles = $this->CrossIndicatorManager->precalculateArticles($articles);
		$this->assertEquals(2,count($crossIndicatorarticles));
		foreach($crossIndicatorarticles as $crossIndicatorarticle) {
			$this->assertEquals(null,$crossIndicatorarticle->getGlobalNoteIndicator());
			$this->assertEquals(null,$crossIndicatorarticle->getIndicator1());
			
		}
		
		$crossIndicatorarticles = $this->CrossIndicatorManager->calculateArticles($crossIndicatorarticles);
		$this->assertEquals(2,count($crossIndicatorarticles));
		foreach($crossIndicatorarticles as $crossIndicatorarticle) {
			$this->assertEquals(null,$crossIndicatorarticle->getGlobalNoteIndicator());
			$this->assertEquals(null,$crossIndicatorarticle->getIndicator1());
		}

	}
	
	private function forceAuthenticate($container) {
		$em = $container->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);
		
		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}

}
