<?php

namespace Seriel\TrendBundle\Tests\Managers;

use Seriel\TrendBundle\Managers\GoogleTrendManager;
use Seriel\TrendBundle\Managers\TrendManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Seriel\TrendBundle\Entity\Trend;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;

class GoogleTrendManagerTest extends WebTestCase{
	
	public function testPrepareHotTrendsUrl()
	{
		$kernel = static::createKernel();
		$kernel->boot();
		$container = $kernel->getContainer();
		
		$googleTrendManager= $container->get('seriel_trend.google_trends_manager');			
		$code_location = $container->getParameter('trend.google.codelocation');
		
		// test service exist
		$this->assertInstanceOf(GoogleTrendManager::class,$googleTrendManager);
		// test parameters
		$this->assertNotEmpty($code_location);
		// whithout date
		$result = $googleTrendManager->prepareHotTrendsUrl('');
		$this->assertEquals('', $result);
		$result = $googleTrendManager->prepareHotTrendsUrl(null);
		$this->assertEquals('', $result);
		// date is valid
		$result = $googleTrendManager->prepareHotTrendsUrl('20170101');
		$this->assertEquals('http://www.google.com/trends/hottrends/hotItems?ajax=1&htd=20170101&pn='.$code_location.'&htv=l', $result);
		
	}
	
	public function testGetLastSubject()
	{
		$kernel = static::createKernel();
		$kernel->boot();
		$container = $kernel->getContainer();
		
		$googleTrendManager= $container->get('seriel_trend.google_trends_manager');
		
		// test service exist
		$this->assertInstanceOf(GoogleTrendManager::class,$googleTrendManager);
		
		
		// whithout date
		$result = $googleTrendManager->getLastSubject(null);
		$this->assertNotEquals(null, $result);
		$this->assertNotEquals(array(), $result);
		
		// whith date
		$result = $googleTrendManager->getLastSubject(new \DateTime('2017-01-01'));
		$this->assertNotEquals(null, $result);
		$this->assertNotEquals(array(), $result);
		
		
		// whith date > date current
		$date = new \DateTime();
		$date->add(new \DateInterval('P10D'));
		$result = $googleTrendManager->getLastSubject($date);
		$this->assertNotEquals(null, $result);
		$this->assertEquals(array(), $result);

	}
	
	public function testGetTrendsDirect()
	{
		$kernel = static::createKernel();
		$kernel->boot();
		$container = $kernel->getContainer();
		
		$googleTrendManager= $container->get('seriel_trend.google_trends_manager');
		
		// test service exist
		$this->assertInstanceOf(GoogleTrendManager::class,$googleTrendManager);
			
		//if not $endDate
		$startDate= new \Datetime('2017-01-01');
		$result = $googleTrendManager->getTrendsDirect(-1, $startDate, null, array());
		$lastSubject = $googleTrendManager->getLastSubject($startDate);
		$this->assertNotEquals(null, $result);
		$this->assertNotEquals(array(), $result);
		$this->assertEquals(count($lastSubject), count($result));
		
		//test quantity
		$result = $googleTrendManager->getTrendsDirect(5, new \Datetime(), null, array());
		$this->assertEquals(5, count($result));
		
		// test sort and data
		$result = $googleTrendManager->getTrendsDirect(-1, new \Datetime('2017-01-03'), new \Datetime('2017-01-04'), array());
		$oldvalue = null;
		foreach ($result as $key => $googletrend) {	
			$this->assertTrue(is_string($key));
			if (isset($oldvalue) and $googletrend > $oldvalue) $this->assertTrue(false);
			$oldvalue = $googletrend;
		}
		
	}

	
	public function testGetTrends()
	{
		$kernel = static::createKernel();
		$kernel->boot();
		$container = $kernel->getContainer();
		$ManagerManager= $container->get('managers_manager');
		$this->forceAuthenticate($container);
		
		$googleTrendManager= $container->get('seriel_trend.google_trends_manager');
		$TrendManager= $container->get('seriel_trend.manager');
		
		
		// test service exist
		$this->assertInstanceOf(GoogleTrendManager::class,$googleTrendManager);
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
		$result = $googleTrendManager->getTrends(3, new \Datetime('2017-02-01'), null, array());
		$this->assertEquals(3, count($result));
		
		
		// test data
		$result = $googleTrendManager->getTrends(-1, new \Datetime('2017-02-01'), new \Datetime('2017-02-02'), array());
		foreach ($result as $key => $googletrend) {
			$this->assertTrue(is_string($key));
			$this->assertTrue(is_numeric($googletrend));
		}
		
	}	
	public function testGetTrendsFutur()
	{
		$kernel = static::createKernel();
		$kernel->boot();
		$container = $kernel->getContainer();
		$ManagerManager= $container->get('managers_manager');
		$this->forceAuthenticate($container);
		
		$googleTrendManager= $container->get('seriel_trend.google_trends_manager');
		$TrendManager= $container->get('seriel_trend.manager');
		
		
		// test service exist
		$this->assertInstanceOf(GoogleTrendManager::class,$googleTrendManager);
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
		$result = $googleTrendManager->getTrendsFutur(new \Datetime('2100-01-01'),100 , array());
		$this->assertNotEquals(null, $result);
		foreach ($result as $key => $googletrend) {
			$this->assertTrue(is_string($key));
			$this->assertTrue(is_numeric($googletrend));
		}
		
		$ManagerManager= $container->get('managers_manager');
		$result = $googleTrendManager->getTrendsFutur(new \Datetime('2100-01-01'),90 , array());
		
		$this->assertNotEquals(null, $result);
		foreach ($result as $key => $googletrend) {
			$this->assertTrue(is_string($key));
			$this->assertTrue(is_numeric($googletrend));
		}
	}	

	private function forceAuthenticate($container) {
		$em = $container->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);
		
		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}
}
