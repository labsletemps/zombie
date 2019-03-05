<?php

namespace Seriel\TrendBundle\Tests\Managers;

use Seriel\TrendBundle\Managers\CrossTrendManager;
use Seriel\TrendBundle\Managers\TrendManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Seriel\TrendBundle\Entity\Trend;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;

class CrossTrendManagerTest extends WebTestCase{
	
	public function testGetTrends()
	{
		$kernel = static::createKernel();
		$kernel->boot();
		$container = $kernel->getContainer();
		$ManagerManager= $container->get('managers_manager');
		$this->forceAuthenticate($container);
		
		$crossTrendManager= $container->get('seriel_trend.cross_trends_manager');
		$TrendManager= $container->get('seriel_trend.manager');
		// test service exist
		$this->assertInstanceOf(CrossTrendManager::class,$crossTrendManager);
		$this->assertInstanceOf(TrendManager::class,$TrendManager);
		
		//clear trends
		$oldtrends = $TrendManager->getAll();
		foreach($oldtrends as $trend) {
			$TrendManager->remove($trend);
		}
		$TrendManager->flush();
		
		//Add trend
		$trend = new Trend();
		$trend->setDate(new \DateTime('2017-02-01'));
		$trend->setName('trend1');
		$trend->setPosition(1);
		$trend->setModule('google_news');
		$TrendManager->save($trend);
		$trend = new Trend();
		$trend->setDate(new \DateTime('2017-02-01'));
		$trend->setName('trend2');
		$trend->setPosition(2);
		$trend->setModule('google_news');
		$TrendManager->save($trend);
		$trend = new Trend();
		$trend->setDate(new \DateTime('2017-02-01'));
		$trend->setName('trend3');
		$trend->setPosition(3);
		$trend->setModule('google_news');
		$TrendManager->save($trend);
		$trend = new Trend();
		$trend->setDate(new \DateTime('2017-02-01'));
		$trend->setName('trend4');
		$trend->setPosition(4);
		$trend->setModule('google_news');
		$TrendManager->save($trend);
		$trend = new Trend();
		$trend->setDate(new \DateTime('2017-02-02'));
		$trend->setName('trend5');
		$trend->setPosition(1);
		$trend->setModule('google_news');
		$TrendManager->save($trend);
		$TrendManager->flush();
		
		//test quantity
		$result = $crossTrendManager->getTrends(3, new \Datetime('2017-02-01'), null, array());
		$this->assertEquals(3, count($result));
		
		// test data
		$result = $crossTrendManager->getTrends(-1, new \Datetime('2017-02-01'), new \Datetime('2017-02-02'), array());
		foreach ($result as $key => $trend) {
			$this->assertTrue(is_string($key));
			$this->assertTrue(is_numeric($trend));
		}
		
		
	}
	public function testGetTrendsFutur()
	{

		$kernel = static::createKernel();
		$kernel->boot();
		$container = $kernel->getContainer();
		$ManagerManager= $container->get('managers_manager');
		$this->forceAuthenticate($container);
		
		$crossTrendManager= $container->get('seriel_trend.cross_trends_manager');
		$TrendManager= $container->get('seriel_trend.manager');
		// test service exist
		$this->assertInstanceOf(CrossTrendManager::class,$crossTrendManager);
		$this->assertInstanceOf(TrendManager::class,$TrendManager);
		
		//clear trends
		$oldtrends = $TrendManager->getAll();
		foreach($oldtrends as $trend) {
			$TrendManager->remove($trend);
		}
		$TrendManager->flush();
		
		//Add trend
		$trend = new Trend();
		$trend->setDate(new \DateTime('2009-01-01'));
		$trend->setName('trend1');
		$trend->setPosition(1);
		$trend->setModule('google_news');
		$TrendManager->save($trend);
		$trend = new Trend();
		$trend->setDate(new \DateTime('2009-01-01'));
		$trend->setName('trend2');
		$trend->setPosition(2);
		$trend->setModule('google_news');
		$TrendManager->save($trend);
		$trend = new Trend();
		$trend->setDate(new \DateTime('2009-01-01'));
		$trend->setName('trend3');
		$trend->setPosition(3);
		$trend->setModule('google_news');
		$TrendManager->save($trend);
		$trend = new Trend();
		$trend->setDate(new \DateTime('2010-01-01'));
		$trend->setName('trend1');
		$trend->setPosition(4);
		$trend->setModule('google_news');
		$TrendManager->save($trend);
		$trend = new Trend();
		$trend->setDate(new \DateTime('2010-01-01'));
		$trend->setName('trend3');
		$trend->setPosition(1);
		$trend->setModule('google_news');
		$TrendManager->save($trend);
		$TrendManager->flush();
		// test data with precision 100 (no similarity)
		$result = $crossTrendManager->getTrendsFutur(new \Datetime('2100-01-01'),100 , array());
		$this->assertNotEquals(null, $result);
		foreach ($result as $key => $trend) {
			$this->assertTrue(is_string($key));
			$this->assertTrue(is_numeric($trend));
		}
		
		$ManagerManager= $container->get('managers_manager');
		$result = $crossTrendManager->getTrendsFutur(new \Datetime('2100-01-01'),90 , array());
		$this->assertNotEquals(null, $result);
		foreach ($result as $key => $trend) {
			$this->assertTrue(is_string($key));
			$this->assertTrue(is_numeric($trend));
		}
	}
	
	private function forceAuthenticate($container) {
		$em = $container->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);
		
		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}
}
