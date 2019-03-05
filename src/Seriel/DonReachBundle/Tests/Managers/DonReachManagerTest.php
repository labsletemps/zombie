<?php

namespace Seriel\DonReachBundle\Tests\Managers;

use Seriel\DonReachBundle\Managers\DonReachManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use ZombieBundle\Entity\News\Article;
use ZombieBundle\Managers\News\ArticlesManager;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;


class DonReachManagerTest extends WebTestCase{
	
	private $container;
	private $DonReachManager;
	
	public function __construct()
	{
		$kernel = static::createKernel();
		$kernel->boot();
		$GLOBALS['kernel'] = $kernel;
		$this->container = $kernel->getContainer();
		$ManagerManager= $this->container->get('managers_manager');
		$this->forceAuthenticate($this->container);
		$this->DonReachManager= $this->container->get('seriel_donreach.manager');

	}
	
	public function testGetDatasForArticleUrl() {
		$this->assertInstanceOf(DonReachManager::class,$this->DonReachManager);
		
		//test limit
		$result = $this->DonReachManager->getDatasForArticleUrl('');
		$this->assertFalse($result);
		
		$result = $this->DonReachManager->getDatasForArticleUrl(null);
		$this->assertFalse($result);
		
		//test value
		$result = $this->DonReachManager->getDatasForArticleUrl('/frhrthjetehtehj/tjrtjrtjrtj/rtjrtjeszrhre');
		if (isset($result->{'error'})) $result =false;
		$this->assertFalse($result);

		$result = $this->DonReachManager->getDatasForArticleUrl('https://www.example.com/home');
		$return = false;
		if (isset($result->{'shares'})) $return=true;
		$this->assertTrue($return);
		$return = false;
		if (isset($result->{'shares'}->{'buffer'})) $return=true;
		$this->assertTrue($return);
		$return = false;
		if (isset($result->{'shares'}->{'facebook'})) $return=true;
		$this->assertTrue($return);
		$return = false;
		if (isset($result->{'shares'}->{'fancy'})) $return=true;
		$this->assertTrue($return);
		$return = false;
		if (isset($result->{'shares'}->{'google'})) $return=true;
		$this->assertTrue($return);
		$return = false;
		if (isset($result->{'shares'}->{'hackernews'})) $return=true;
		$this->assertTrue($return);
		$return = false;
		if (isset($result->{'shares'}->{'hatena'})) $return=true;
		$this->assertTrue($return);
		$return = false;
		if (isset($result->{'shares'}->{'mailru'})) $return=true;
		$this->assertTrue($return);
		$return = false;
		if (isset($result->{'shares'}->{'odnoklassniki'})) $return=true;
		$this->assertTrue($return);
		$return = false;
		if (isset($result->{'shares'}->{'pinterest'})) $return=true;
		$this->assertTrue($return);
		$return = false;
		if (isset($result->{'shares'}->{'pocket'})) $return=true;
		$this->assertTrue($return);
		$return = false;
		if (isset($result->{'shares'}->{'reddit'})) $return=true;
		$this->assertTrue($return);
		$return = false;
		if (isset($result->{'shares'}->{'scoopit'})) $return=true;
		$this->assertTrue($return);
		$return = false;
		if (isset($result->{'shares'}->{'stumbleupon'})) $return=true;
		$this->assertTrue($return);
		$return = false;
		if (isset($result->{'shares'}->{'tumblr'})) $return=true;
		$this->assertTrue($return);
		$return = false;
		if (isset($result->{'shares'}->{'vk'})) $return=true;
		$this->assertTrue($return);
		$return = false;
		if (isset($result->{'shares'}->{'weibo'})) $return=true;
		$this->assertTrue($return);
		$return = false;
		if (isset($result->{'shares'}->{'xing'})) $return=true;
		$this->assertTrue($return);
		$return = false;
		if (isset($result->{'shares'}->{'yummly'})) $return=true;
		$this->assertTrue($return);

	
	}
	
	
	public function testgetDatasForArticle() {
		
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
		$article1->setChapeauStriped('super chapeau moyen');
		$article1->setChapeau('super chapeau moyen');
		$article1->setContent('super contenu trop long ');
		$article1->setContentStriped('super contenu trop long ');
		$articleManager->save($article1);
		$article2 = new Article();
		$article2->setGuid('article_test_phpunit_2');
		$article2->setDateParution(new \DateTime('2017-01-03'));
		$article2->setTitre('article titre');
		$article2->setSource('01');
		$article2->setUris(array('/home'));
		$article2->setChapeauStriped('super chapeau fort');
		$article2->setChapeau('super chapeau fort');
		$article2->setContent('super contenu trop court ');
		$article2->setContentStriped('super contenu trop court ');
		$articleManager->save($article2);
		$articleManager->flush();
			
		//test article with no url
		
		$result= $this->DonReachManager->getDatasForArticle($article1);
		$this->assertFalse($result);
		
		//test value
		
		$result= $this->DonReachManager->getDatasForArticle($article2);
		$this->assertEquals(20,count($result));
		$this->assertTrue(isset($result['buffer']));
		$this->assertTrue(isset($result['facebook']));
		$this->assertTrue(isset($result['fancy']));
		$this->assertTrue(isset($result['google']));
		$this->assertTrue(isset($result['hackernews']));
		$this->assertTrue(isset($result['hatena']));
		$this->assertTrue(isset($result['linkedin']));
		$this->assertTrue(isset($result['mailru']));
		$this->assertTrue(isset($result['odnoklassniki']));
		$this->assertTrue(isset($result['pinterest']));
		$this->assertTrue(isset($result['pocket']));
		$this->assertTrue(isset($result['reddit']));
		$this->assertTrue(isset($result['scoopit']));
		$this->assertTrue(isset($result['stumbleupon']));
		$this->assertTrue(isset($result['tumblr']));
		$this->assertTrue(isset($result['vk']));
		$this->assertTrue(isset($result['weibo']));
		$this->assertTrue(isset($result['xing']));
		$this->assertTrue(isset($result['yummly']));
		$this->assertTrue(isset($result['day']));
	}
	
	private function forceAuthenticate($container) {
		$em = $container->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);
		
		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}

}
