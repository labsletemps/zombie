<?php

namespace Seriel\GoogleAnalyticsBundle\Tests\Managers;

use Seriel\GoogleAnalyticsBundle\Managers\GoogleAnalyticsManager;
use Seriel\GoogleAnalyticsBundle\Managers\GoogleAnalyticsDayReportManager;
use Seriel\GoogleAnalyticsBundle\Managers\DayReportEntranceManager;
use Seriel\GoogleAnalyticsBundle\Managers\GoogleAnalyticsArticleMetricsManager;
use Seriel\GoogleAnalyticsBundle\Entity\GoogleAnalyticsDayReport;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use ZombieBundle\Entity\News\Article;
use ZombieBundle\Utils\ZombieUtils;
use ZombieBundle\Managers\News\ArticlesManager;
use Google_Service_Exception;

class GoogleAnalyticsManagerTest extends WebTestCase{
	
	private $container;
	private $googlAnalyticsManager;
	
	public function __construct()
	{
		$kernel = static::createKernel();
		$kernel->boot();
		$GLOBALS['kernel'] = $kernel;
		$this->container = $kernel->getContainer();
		$ManagerManager= $this->container->get('managers_manager');
		$this->forceAuthenticate($this->container);
		$this->googlAnalyticsManager = $this->container->get('seriel_google_analytics.manager');

	}

	public function testExeptionDateExecute()
	{
		// test service exist
		$this->assertInstanceOf(GoogleAnalyticsManager::class,$this->googlAnalyticsManager);
		$this->expectException(Google_Service_Exception::class);
		$analytics = $this->googlAnalyticsManager->getPageViews(new \DateTime('2200-01-01'), new \DateTime('2000-01-01'), '');
		
	}
	public function testExeptionFilterExecute()
	{
		// test service exist
		$this->assertInstanceOf(GoogleAnalyticsManager::class,$this->googlAnalyticsManager);
		$this->expectException(Google_Service_Exception::class);
		$analytics = $this->googlAnalyticsManager->getPageViews(null, null, '/rfhreohreirehier');
	}
	//for the test googleanalytics must have a data for the last day
	public function testGetPageViews()
	{
		// test service exist
		$this->assertInstanceOf(GoogleAnalyticsManager::class,$this->googlAnalyticsManager);
		
		//test limit
		$analytics = $this->googlAnalyticsManager->getPageViews(new \DateTime('2200-01-01'), new \DateTime('2200-01-01'), '');
		$this->assertEquals(0,count($analytics));

		$analytics = $this->googlAnalyticsManager->getPageViews(null, null, 'ga:pagePath==/rfhreohrsvgfsgsgsggeirehier');
		$this->assertEquals(0,count($analytics));
		
		//test value
		$analytics = $this->googlAnalyticsManager->getPageViews(null, null, '');
		$haveData= false;
		if (count($analytics)> 0) $haveData= true;
		$this->assertTrue($haveData);
		$this->assertEquals(2,count($analytics[0]));
	}

	//for the test googleanalytics must have a data for the last day
	public function testGetUniquePageViews()
	{
		//test value
		$analytics = $this->googlAnalyticsManager->getUniquePageViews(null, null, '');
		$haveData= false;
		if (count($analytics)> 0) $haveData= true;
		$this->assertTrue($haveData);
		$this->assertEquals(2,count($analytics[0]));
	}

	//for the test googleanalytics must have a data for the last day
	public function testGetTimeOnPage()
	{
		//test value
		$analytics = $this->googlAnalyticsManager->getTimeOnPage(null, null, '');
		$haveData= false;
		if (count($analytics)> 0) $haveData= true;
		$this->assertTrue($haveData);
		$this->assertEquals(2,count($analytics[0]));
	}

	//for the test googleanalytics must have a data for the last day
	public function testGetExitPage()
	{
		//test value
		$analytics = $this->googlAnalyticsManager->getExitpage(null, null, '');
		$haveData= false;
		if (count($analytics)> 0) $haveData= true;
		$this->assertTrue($haveData);
		$this->assertEquals(2,count($analytics[0]));
	}
	
	
	//for the test googleanalytics must have a data for the last day
	public function testGetEntranceonPage()
	{
		//test value
		$analytics = $this->googlAnalyticsManager->getEntranceonPage(null, null, '', '');
		$haveData= false;
		if (count($analytics)> 0) $haveData= true;
		$this->assertTrue($haveData);
		$this->assertEquals(3,count($analytics[0]));
		
	}

	//for the test googleanalytics must have a data for the last day
	public function testGetLinkNavigationPage()
	{
		//test value
		$analytics = $this->googlAnalyticsManager->getLinkNavigationPage(null, null, '', '');
		$haveData= false;
		if (count($analytics)> 0) $haveData= true;
		$this->assertTrue($haveData);
		$this->assertEquals(3,count($analytics[0]));
	}

	//for the test googleanalytics must have a data for 2017-01-01
	public function testCalculateDayReport()
	{
		//test value
		$startDate = new \DateTime('2017-01-01');
		$endDate = new \DateTime('2017-01-01');
		$GADayReportMgr = $this->container->get('seriel_google_analytics.day_report_manager');
		$this->assertInstanceOf(GoogleAnalyticsDayReportManager::class,$GADayReportMgr);
		$GADayReportEntranceMgr =$this->container->get('seriel_google_analytics.day_report_entrance_manager');
		$this->assertInstanceOf(DayReportEntranceManager::class,$GADayReportEntranceMgr);
		
		$GADayReportMgr->deleteGoogleAnalyticsDayReportByDate($startDate->format('Y-m-d'),$endDate->format('Y-m-d'));
		$GADayReportEntranceMgr->deleteDayReportEntranceByDate($startDate->format('Y-m-d'),$endDate->format('Y-m-d'));
		$return = $this->googlAnalyticsManager->calculateDayReport($startDate,$endDate);
		$this->assertTrue($return);
		
		//test dayentrance
		$dayEntrances = $GADayReportEntranceMgr->getAllDayReportEntranceByDate('2017-01-01','2017-01-01');
		$haveData= false;
		if (count($dayEntrances)> 0) $haveData= true;
		$this->assertTrue($haveData);
		
		//test dayreport
		$dayReports = $GADayReportMgr->getGoogleAnalyticsDayReportByDate('2017-01-01','2017-01-01');
		$haveData= false;
		if (count($dayReports)> 0) $haveData= true;
		$this->assertTrue($haveData);
	
	}
	

	//calculateMetrics
	//for the test googleanalytics must have a data for 2017-01-01
	public function testCalculateMetrics()
	{
		//test value
		$startDate = new \DateTime('2017-01-01');
		$endDate = new \DateTime('2017-01-02');
		$GADayReportMgr = $this->container->get('seriel_google_analytics.day_report_manager');
		$this->assertInstanceOf(GoogleAnalyticsDayReportManager::class,$GADayReportMgr);
		$GAMetricsMgr=$this->container->get('seriel_google_analytics.article_metrics_manager');
		$this->assertInstanceOf(GoogleAnalyticsArticleMetricsManager::class,$GAMetricsMgr);
		$articleManager = $this->container->get('articles_manager');
		$this->assertInstanceOf(ArticlesManager::class,$articleManager);
		
		//clear article
		$articles= $articleManager->getAllArticles();
		foreach ($articles as $article){
			$articleManager->remove($article);
		}
		//clear day report
		$GADayReportMgr->deleteGoogleAnalyticsDayReportByDate('1900-01-01','3000-01-01');
		// google analytics metrics
		$GAMetricsMgr->deleteGoogleAnalyticsOldArticleMetrics(new \Datetime());
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
		
		//Create Day report
		$dayreport1 = new GoogleAnalyticsDayReport();
		$dayreport1->setDay($startDate);
		$dayreport1->setPath('/article_test_phpunit_1/home');
		$dayreport1->setSubscription(2);
		$dayreport1->setEntrance(6);
		$dayreport1->setEntranceSubscriber(2);
		$dayreport1->calculEntranceVisitor();
		$dayreport1->setReadtime(60);
		$dayreport1->setReadtimeSubscriber(40);
		$dayreport1->calculReadtimeVisitor();
		$dayreport1->setUniquepageview(50);
		$dayreport1->setUniquepageviewSubscriber(10);
		$dayreport1->calculUniquepageviewVisitor();
		$dayreport1->setPageview(60);
		$dayreport1->setExitpage(2);
		$dayreport1->setPageviewSubscriber(15);
		$dayreport1->calculPageviewVisitor();
		$GADayReportMgr->save($dayreport1);
		
		$dayreport2 = new GoogleAnalyticsDayReport();
		$dayreport2->setDay($startDate);
		$dayreport2->setPath('/article_test_phpunit_1/ananas');
		$dayreport2->setSubscription(2);
		$dayreport2->setEntrance(6);
		$dayreport2->setEntranceSubscriber(2);
		$dayreport2->calculEntranceVisitor();
		$dayreport2->setReadtime(60);
		$dayreport2->setReadtimeSubscriber(40);
		$dayreport2->calculReadtimeVisitor();
		$dayreport2->setUniquepageview(50);
		$dayreport2->setUniquepageviewSubscriber(10);
		$dayreport2->calculUniquepageviewVisitor();
		$dayreport2->setPageview(60);
		$dayreport2->setPageviewSubscriber(15);
		$dayreport2->calculPageviewVisitor();
		$dayreport2->setExitpage(2);
		$GADayReportMgr->save($dayreport2);
		
		$dayreport3= new GoogleAnalyticsDayReport();
		$dayreport3->setDay($endDate);
		$dayreport3->setPath('/article_test_phpunit_1/ananas');
		$dayreport3->setSubscription(2);
		$dayreport3->setEntrance(6);
		$dayreport3->setEntranceSubscriber(2);
		$dayreport3->calculEntranceVisitor();
		$dayreport3->setReadtime(60);
		$dayreport3->setReadtimeSubscriber(40);
		$dayreport3->calculReadtimeVisitor();
		$dayreport3->setUniquepageview(50);
		$dayreport3->setUniquepageviewSubscriber(10);
		$dayreport3->calculUniquepageviewVisitor();
		$dayreport3->setPageview(60);
		$dayreport3->setPageviewSubscriber(15);
		$dayreport3->calculPageviewVisitor();
		$dayreport3->setExitpage(2);
		$GADayReportMgr->save($dayreport3);
		
		$dayreport4= new GoogleAnalyticsDayReport();
		$dayreport4->setDay($startDate);
		$dayreport4->setPath('/article_test_phpunit_2/ananas');
		$dayreport4->setSubscription(2);
		$dayreport4->setEntrance(6);
		$dayreport4->setEntranceSubscriber(2);
		$dayreport4->calculEntranceVisitor();
		$dayreport4->setReadtime(60);
		$dayreport4->setReadtimeSubscriber(40);
		$dayreport4->calculReadtimeVisitor();
		$dayreport4->setUniquepageview(50);
		$dayreport4->setUniquepageviewSubscriber(10);
		$dayreport4->calculUniquepageviewVisitor();
		$dayreport4->setPageview(60);
		$dayreport4->setPageviewSubscriber(15);
		$dayreport4->calculPageviewVisitor();
		$dayreport4->setExitpage(2);
		$GADayReportMgr->save($dayreport4);
		$GADayReportMgr->flush();
		
		$return = $this->googlAnalyticsManager->calculateMetrics();
		$this->assertTrue($return);
		
		//test value
		$googleAnalyticsArticleMetrics1 = $GAMetricsMgr->getGoogleAnalyticsArticleMetricsForArticleId($article1->getId());
		$googleAnalyticsArticleMetrics2 = $GAMetricsMgr->getGoogleAnalyticsArticleMetricsForArticleId($article2->getId());
		//test value article1
		// test Measures
		$this->assertEquals(180,$googleAnalyticsArticleMetrics1->getReadtimeMeasure());
		$this->assertEquals(120,$googleAnalyticsArticleMetrics1->getReadtimeSubscriberMeasure());
		$this->assertEquals(60,$googleAnalyticsArticleMetrics1->getReadtimeVisitorMeasure());
		$this->assertEquals(180,$googleAnalyticsArticleMetrics1->getPageviewMeasure());
		$this->assertEquals(45,$googleAnalyticsArticleMetrics1->getPageviewSubscriberMeasure());
		$this->assertEquals(135,$googleAnalyticsArticleMetrics1->getPageviewVisitorMeasure());
		$this->assertEquals(150,$googleAnalyticsArticleMetrics1->getUniquepageviewMeasure());
		$this->assertEquals(30,$googleAnalyticsArticleMetrics1->getUniquepageviewSubscriberMeasure());
		$this->assertEquals(120,$googleAnalyticsArticleMetrics1->getUniquepageviewVisitorMeasure());
		$this->assertEquals(6,$googleAnalyticsArticleMetrics1->getSubscriptionMeasure());
		$this->assertEquals(6,$googleAnalyticsArticleMetrics1->getExitpageMeasure());
		$this->assertEquals(18,$googleAnalyticsArticleMetrics1->getEntranceMeasure());
		$this->assertEquals(6,$googleAnalyticsArticleMetrics1->getEntranceSubscriberMeasure());
		$this->assertEquals(12,$googleAnalyticsArticleMetrics1->getEntranceVisitorMeasure());
		// test Indicators

		$this->assertEquals(56,$googleAnalyticsArticleMetrics1->getMonetisationIndicator());
		$this->assertEquals(50,$googleAnalyticsArticleMetrics1->getAttentionIndicator());
		$this->assertEquals(56,$googleAnalyticsArticleMetrics1->getSubscriptionIndicator());
		$this->assertEquals(56,$googleAnalyticsArticleMetrics1->getBounceIndicator());
		$this->assertEquals(50,$googleAnalyticsArticleMetrics1->getCompletionreadIndicator());
		$this->assertEquals(50,$googleAnalyticsArticleMetrics1->getCompletionreadSubscriberIndicator());
		$this->assertEquals(55,$googleAnalyticsArticleMetrics1->getAbonneLikeIndicator());
		$this->assertEquals(50,$googleAnalyticsArticleMetrics1->getCompletionreadVisitorIndicator());
		$this->assertEquals(55,$googleAnalyticsArticleMetrics1->getVisiteurLikeIndicator());
		$this->assertEquals(60,$googleAnalyticsArticleMetrics1->getAudienceIndicator());
		$this->assertEquals(56,$googleAnalyticsArticleMetrics1->getEntranceIndicator());

		
		//test value article2
		// test Measures
		$this->assertEquals(60,$googleAnalyticsArticleMetrics2->getReadtimeMeasure());
		$this->assertEquals(40,$googleAnalyticsArticleMetrics2->getReadtimeSubscriberMeasure());
		$this->assertEquals(20,$googleAnalyticsArticleMetrics2->getReadtimeVisitorMeasure());
		$this->assertEquals(60,$googleAnalyticsArticleMetrics2->getPageviewMeasure());
		$this->assertEquals(15,$googleAnalyticsArticleMetrics2->getPageviewSubscriberMeasure());
		$this->assertEquals(45,$googleAnalyticsArticleMetrics2->getPageviewVisitorMeasure());
		$this->assertEquals(50,$googleAnalyticsArticleMetrics2->getUniquepageviewMeasure());
		$this->assertEquals(10,$googleAnalyticsArticleMetrics2->getUniquepageviewSubscriberMeasure());
		$this->assertEquals(40,$googleAnalyticsArticleMetrics2->getUniquepageviewVisitorMeasure());
		$this->assertEquals(2,$googleAnalyticsArticleMetrics2->getSubscriptionMeasure());
		$this->assertEquals(6,$googleAnalyticsArticleMetrics2->getEntranceMeasure());
		$this->assertEquals(2,$googleAnalyticsArticleMetrics2->getEntranceSubscriberMeasure());
		$this->assertEquals(4,$googleAnalyticsArticleMetrics2->getEntranceVisitorMeasure());
		$this->assertEquals(2,$googleAnalyticsArticleMetrics2->getExitpageMeasure());
		// test Indicators
		$this->assertEquals(42,$googleAnalyticsArticleMetrics2->getMonetisationIndicator());
		$this->assertEquals(50,$googleAnalyticsArticleMetrics2->getAttentionIndicator());
		$this->assertEquals(42,$googleAnalyticsArticleMetrics2->getSubscriptionIndicator());
		$this->assertEquals(50,$googleAnalyticsArticleMetrics2->getCompletionreadIndicator());
		$this->assertEquals(50,$googleAnalyticsArticleMetrics2->getCompletionreadSubscriberIndicator());
		$this->assertEquals(41.5,$googleAnalyticsArticleMetrics2->getAbonneLikeIndicator());
		$this->assertEquals(50,$googleAnalyticsArticleMetrics2->getCompletionreadVisitorIndicator());
		$this->assertEquals(41.5,$googleAnalyticsArticleMetrics2->getVisiteurLikeIndicator());
		$this->assertEquals(33.0,$googleAnalyticsArticleMetrics2->getAudienceIndicator());
		$this->assertEquals(42.0,$googleAnalyticsArticleMetrics2->getEntranceIndicator());
		$this->assertEquals(42,$googleAnalyticsArticleMetrics2->getBounceIndicator());


	}

	private function forceAuthenticate($container) {
		$em = $container->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);
		
		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}

}
