<?php

namespace Seriel\ChartbeatBundle\Tests\Managers;

use Seriel\ChartbeatBundle\Managers\ChartbeatManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use ZombieBundle\Entity\News\Article;
use ZombieBundle\Managers\News\ArticlesManager;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;


class ChartbeatManagerTest extends WebTestCase{
	
	private $container;
	private $ChartbeatManager;
	
	public function __construct()
	{
		$kernel = static::createKernel();
		$kernel->boot();
		$GLOBALS['kernel'] = $kernel;
		$this->container = $kernel->getContainer();
		$ManagerManager= $this->container->get('managers_manager');
		$this->forceAuthenticate($this->container);
		$this->ChartbeatManager= $this->container->get('seriel_chartbeat.manager');
	}
	public function testGetReportForDay() {
		$this->assertInstanceOf(ChartbeatManager::class,$this->ChartbeatManager);
		
		//limit
		$result = $this->ChartbeatManager->getReportForDay(null);
		$this->assertEquals(null, $result);
		
		$result = $this->ChartbeatManager->getReportForDay('2017-13-01');
		$this->assertEquals(null, $result);
	
		$result = $this->ChartbeatManager->getReportForDay('1000-01-01');
		$this->assertEquals(null, $result);
		
		$result = $this->ChartbeatManager->getReportForDay('3000-01-01');
		$data = false;
		if (count(explode("\n", $result)) > 0 ) $data = true;
		$this->assertTrue($data);
		
		//test value
		$result = $this->ChartbeatManager->getReportForDay('2017-01-01');
		$data = false;
		if (count(explode("\n", $result)) > 0 ) $data = true;
		$this->assertTrue($data);
		
	}

	private function forceAuthenticate($container) {
		$em = $container->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);
		
		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}

}
