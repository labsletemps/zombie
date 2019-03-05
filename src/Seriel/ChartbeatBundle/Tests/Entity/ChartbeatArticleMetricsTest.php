<?php

namespace Seriel\ChartbeatBundle\Tests\Entity;

use Seriel\ChartbeatBundle\Entity\ChartbeatArticleMetrics;
use Seriel\ChartbeatBundle\Entity\ChartbeatArticleDayReport;
use Seriel\ChartbeatBundle\Managers\ChartbeatArticleDayReportManager;
use Seriel\ChartbeatBundle\Managers\ChartbeatArticleMetricsManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use ZombieBundle\Entity\News\Article;
use ZombieBundle\Managers\News\ArticlesManager;

class ChartbeatArticleMetricsTest extends WebTestCase{
	
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
	public function testPreCalculate()
	{

		$dayReportManager = $this->container->get('seriel_chartbeat.cbadr_manager');
		$this->assertInstanceOf(ChartbeatArticleDayReportManager::class,$dayReportManager);
		//clear Day report
		$dayReports = $dayReportManager->getAllChartbeatArticleDayReport();
		foreach ($dayReports as $dayReport) {
			$dayReportManager->remove($dayReport);
		}
		$dayReportManager->flush();
		
		$article1 = new Article();
		$article1->setGuid('article_test_phpunit_1');
		$article1->setDateParution(new \DateTime('2017-01-01'));
		$article1->setTitre('super titre');
		$article1->setUris(array('/article_test_phpunit_1/home','/article_test_phpunit_1/ananas'));
		$article1->setChapeauStriped('super chapeau moyen');
		$article1->setChapeau('super chapeau moyen');
		$article1->setContent('super contenu trop long ');
		$article1->setContentStriped('super contenu trop long ');

		$ChartbeatArticleMetrics= new ChartbeatArticleMetrics($article1);
		$ChartbeatArticleMetrics->preCalculate();
		$this->assertInstanceOf(ChartbeatArticleMetrics::class, $ChartbeatArticleMetrics);
		
		$this->assertEquals(new \DateTime('2017-01-01'), $ChartbeatArticleMetrics->getDateParution());
		$this->assertEquals(new \DateTime('2017-01-04'), $ChartbeatArticleMetrics->getDateEvergreen());
		$now = new \DateTime();
		$this->assertEquals($now->format('ymd'), $ChartbeatArticleMetrics->getDateCalcul()->format('ymd'));
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageAvgScrollSinceEvergreen());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageAvgScrollSinceParution());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageViewsTotal());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageTimeTotal());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageViewsPerDaySinceParution());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageTimePerDaySinceParution());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageViewsPerDaySinceEvergreen());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageTimePerDaySinceEvergreen());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageViewsPerDayBetweenParutionAndEvergreen());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageViewsRatioBeforeAndAfterEvergreen());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageTimeAvg());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageTimeAvgOnWords());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageAverageTimeSinceParution());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageAverageTimeSinceEvergreen());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getIndicatorAttention());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getMetricEvergreen());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getMetricPageViews());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getMetricReadingTime());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getMetricReadPercent());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getIndicatorDurabilite());
		
		//add chartbeat day metric
		$data['day'] = new \DateTime('2017-01-05');
		$data['path']= '/article_test_phpunit_1/ananas';
		$data['page_avg_scroll']= 2;
		$data['page_scroll_starts']= 2;
		$data['page_avg_time']= 2;
		$data['page_total_time']= 2;
		$data['page_uniques']= 2;
		$data['page_views']= 2;
		$data['page_views_loyal']= 2;
		$data['page_views_quality']= 2;
		
		$ChartbeatArticleDayReport1 = ChartbeatArticleDayReport::createFromArray($data);
		$data['day'] = new \DateTime('2017-01-06');
		$ChartbeatArticleDayReport2 = ChartbeatArticleDayReport::createFromArray($data);
		$dayReportManager->save($ChartbeatArticleDayReport1);
		$dayReportManager->save($ChartbeatArticleDayReport2);
		$dayReportManager->flush();
		
		$ChartbeatArticleMetrics->preCalculate();
		$this->assertInstanceOf(ChartbeatArticleMetrics::class, $ChartbeatArticleMetrics);
		
		
		$this->assertEquals(new \DateTime('2017-01-01'), $ChartbeatArticleMetrics->getDateParution());
		$this->assertEquals(new \DateTime('2017-01-04'), $ChartbeatArticleMetrics->getDateEvergreen());
		$now = new \DateTime();
		$this->assertEquals($now->format('ymd'), $ChartbeatArticleMetrics->getDateCalcul()->format('ymd'));
		$this->assertEquals(2, $ChartbeatArticleMetrics->getPageAvgScrollSinceEvergreen());
		$this->assertEquals(2, $ChartbeatArticleMetrics->getPageAvgScrollSinceParution());
		$this->assertEquals(4, $ChartbeatArticleMetrics->getPageViewsTotal());
		$this->assertEquals(4, $ChartbeatArticleMetrics->getPageTimeTotal());
		$nbDaySinceParution = ($now->getTimestamp() - $ChartbeatArticleMetrics->getDateParution()->getTimestamp()) / (24 * 3600);
		$nbDaySinceEvergreen = ($now->getTimestamp() - $ChartbeatArticleMetrics->getDateEvergreen()->getTimestamp()) / (24 * 3600);
		$this->assertEquals((4/$nbDaySinceParution), $ChartbeatArticleMetrics->getPageViewsPerDaySinceParution());
		$this->assertEquals((4/$nbDaySinceParution), $ChartbeatArticleMetrics->getPageTimePerDaySinceParution());
		$this->assertEquals((4/$nbDaySinceEvergreen), $ChartbeatArticleMetrics->getPageViewsPerDaySinceEvergreen());
		$this->assertEquals((4/$nbDaySinceEvergreen), $ChartbeatArticleMetrics->getPageTimePerDaySinceEvergreen());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageViewsPerDayBetweenParutionAndEvergreen());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageViewsRatioBeforeAndAfterEvergreen());
		$this->assertEquals(1, $ChartbeatArticleMetrics->getPageTimeAvg());
		$this->assertEquals(0.5, $ChartbeatArticleMetrics->getPageTimeAvgOnWords());
		$this->assertEquals(1, $ChartbeatArticleMetrics->getPageAverageTimeSinceParution());
		$this->assertEquals(1, $ChartbeatArticleMetrics->getPageAverageTimeSinceEvergreen());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getIndicatorAttention());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getMetricEvergreen());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getMetricPageViews());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getMetricReadingTime());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getMetricReadPercent());
		$this->assertEquals(0, floor($ChartbeatArticleMetrics->getIndicatorDurabilite()));
		$this->assertNotEquals(0, $ChartbeatArticleMetrics->getIndicatorDurabilite());
	}
	public function testCalculate()
	{
		$dayReportManager = $this->container->get('seriel_chartbeat.cbadr_manager');
		$this->assertInstanceOf(ChartbeatArticleDayReportManager::class,$dayReportManager);
		$artMetricsMgr = $this->container->get('seriel_chartbeat.article_metrics_manager');
		$this->assertInstanceOf(ChartbeatArticleMetricsManager::class,$artMetricsMgr);
		$artMgr = $this->container->get('articles_manager');
		$this->assertInstanceOf(ArticlesManager::class,$artMgr);
		//clear Day report
		$dayReports = $dayReportManager->getAllChartbeatArticleDayReport();
		foreach ($dayReports as $dayReport) {
			$dayReportManager->remove($dayReport);
		}
		$dayReportManager->flush();
		
		$article1 = new Article();
		$article1->setGuid('article_test_phpunit_1');
		$article1->setDateParution(new \DateTime('2017-01-01'));
		$article1->setTitre('super titre');
		$article1->setUris(array('/article_test_phpunit_1/home','/article_test_phpunit_1/ananas'));
		$article1->setChapeauStriped('super chapeau moyen');
		$article1->setChapeau('super chapeau moyen');
		$article1->setContent('super contenu trop long ');
		$article1->setContentStriped('super contenu trop long ');
		$artMgr->save($article1);
		$artMgr->flush();
		
		$ChartbeatArticleMetrics= new ChartbeatArticleMetrics($article1);
		$ChartbeatArticleMetrics->preCalculate();
		$ChartbeatArticleMetrics->calculate();
		$this->assertInstanceOf(ChartbeatArticleMetrics::class, $ChartbeatArticleMetrics);
		
		$this->assertEquals(new \DateTime('2017-01-01'), $ChartbeatArticleMetrics->getDateParution());
		$now = new \DateTime();
		$this->assertEquals($now->format('ymd'), $ChartbeatArticleMetrics->getDateCalcul()->format('ymd'));
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageAvgScrollSinceEvergreen());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageAvgScrollSinceParution());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageViewsTotal());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageTimeTotal());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageViewsPerDaySinceParution());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageTimePerDaySinceParution());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageViewsPerDaySinceEvergreen());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageTimePerDaySinceEvergreen());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageViewsPerDayBetweenParutionAndEvergreen());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageViewsRatioBeforeAndAfterEvergreen());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageTimeAvg());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageTimeAvgOnWords());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageAverageTimeSinceParution());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageAverageTimeSinceEvergreen());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getIndicatorAttention());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getMetricEvergreen());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getMetricPageViews());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getMetricReadingTime());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getMetricReadPercent());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getIndicatorDurabilite());
		
		//add chartbeat day metric
		$data['day'] = new \DateTime('2017-01-05');
		$data['path']= '/article_test_phpunit_1/ananas';
		$data['page_avg_scroll']= 2;
		$data['page_scroll_starts']= 2;
		$data['page_avg_time']= 2;
		$data['page_total_time']= 2;
		$data['page_uniques']= 2;
		$data['page_views']= 2;
		$data['page_views_loyal']= 2;
		$data['page_views_quality']= 2;
		/*
		$ChartbeatArticleDayReport1 = ChartbeatArticleDayReport::createFromArray($data);
		$data['day'] = new \DateTime('2017-01-06');
		$ChartbeatArticleDayReport2 = ChartbeatArticleDayReport::createFromArray($data);
		$dayReportManager->save($ChartbeatArticleDayReport1);
		$dayReportManager->save($ChartbeatArticleDayReport2);
		$dayReportManager->flush();
		
		$ChartbeatArticleMetrics->preCalculate();
		$artMetricsMgr->save($ChartbeatArticleMetrics);
		$artMetricsMgr->flush();
		
		$artMetricsMgr->updateMoyennesGenerales();
		$ChartbeatArticleMetrics->calculate();
		$this->assertInstanceOf(ChartbeatArticleMetrics::class, $ChartbeatArticleMetrics);
		
		
		$this->assertEquals(new \DateTime('2017-01-01'), $ChartbeatArticleMetrics->getDateParution());
		$this->assertEquals(new \DateTime('2017-01-04'), $ChartbeatArticleMetrics->getDateEvergreen());
		$now = new \DateTime();
		$this->assertEquals($now->format('ymd'), $ChartbeatArticleMetrics->getDateCalcul()->format('ymd'));
		$this->assertEquals(2, $ChartbeatArticleMetrics->getPageAvgScrollSinceEvergreen());
		$this->assertEquals(2, $ChartbeatArticleMetrics->getPageAvgScrollSinceParution());
		$this->assertEquals(4, $ChartbeatArticleMetrics->getPageViewsTotal());
		$this->assertEquals(4, $ChartbeatArticleMetrics->getPageTimeTotal());
		$nbDaySinceParution = ($now->getTimestamp() - $ChartbeatArticleMetrics->getDateParution()->getTimestamp()) / (24 * 3600);
		$nbDaySinceEvergreen = ($now->getTimestamp() - $ChartbeatArticleMetrics->getDateEvergreen()->getTimestamp()) / (24 * 3600);
		$this->assertEquals((4/$nbDaySinceParution), $ChartbeatArticleMetrics->getPageViewsPerDaySinceParution());
		$this->assertEquals((4/$nbDaySinceParution), $ChartbeatArticleMetrics->getPageTimePerDaySinceParution());
		$this->assertEquals((4/$nbDaySinceEvergreen), $ChartbeatArticleMetrics->getPageViewsPerDaySinceEvergreen());
		$this->assertEquals((4/$nbDaySinceEvergreen), $ChartbeatArticleMetrics->getPageTimePerDaySinceEvergreen());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageViewsPerDayBetweenParutionAndEvergreen());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getPageViewsRatioBeforeAndAfterEvergreen());
		$this->assertEquals(1, $ChartbeatArticleMetrics->getPageTimeAvg());
		$this->assertEquals(0.5, $ChartbeatArticleMetrics->getPageTimeAvgOnWords());
		$this->assertEquals(1, $ChartbeatArticleMetrics->getPageAverageTimeSinceParution());
		$this->assertEquals(1, $ChartbeatArticleMetrics->getPageAverageTimeSinceEvergreen());
		$this->assertEquals(50, $ChartbeatArticleMetrics->getIndicatorAttention());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getMetricEvergreen());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getMetricPageViews());
		$this->assertEquals(50, $ChartbeatArticleMetrics->getMetricReadingTime());
		$this->assertEquals(0, $ChartbeatArticleMetrics->getMetricReadPercent());
		$ChartbeatArticleMetrics->getIndicatorDurabilite() <= 50 ? $valueIndicatorDurabilite = true : $valueIndicatorDurabilite = false ;
		$this->assertEquals(true,$valueIndicatorDurabilite);
*/
	}
	
	private function forceAuthenticate($container) {
		$em = $container->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);
		
		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}
}
