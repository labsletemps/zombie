<?php

namespace Seriel\GoogleAnalyticsBundle\Tests\Entity;

use Seriel\GoogleAnalyticsBundle\Entity\GoogleAnalyticsArticleMetrics;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use ZombieBundle\Entity\News\Article;
use ZombieBundle\Utils\ZombieUtils;

class GoogleAnalyticsArticleMetricsTest extends WebTestCase{
	
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
	public function testPreCalcul()
	{
		
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
		
		// test no data
		$GoogleAnalyticsArticleMetrics = New GoogleAnalyticsArticleMetrics();
		$GoogleAnalyticsArticleMetrics->setArticle($article1);
		$GoogleAnalyticsArticleMetrics->preCalcul();
		$this->assertEquals(0, $GoogleAnalyticsArticleMetrics->getEntranceIndicator());
		$this->assertEquals(0, $GoogleAnalyticsArticleMetrics->getSubscriptionIndicator());
		$this->assertEquals(0, $GoogleAnalyticsArticleMetrics->getPageviewIndicator());
		$this->assertEquals(0, $GoogleAnalyticsArticleMetrics->getCompletionreadIndicator());
		$this->assertEquals(0, $GoogleAnalyticsArticleMetrics->getCompletionreadSubscriberIndicator());
		$this->assertEquals(0, $GoogleAnalyticsArticleMetrics->getCompletionreadVisitorIndicator());
		$this->assertEquals(0, $GoogleAnalyticsArticleMetrics->getAttentionIndicator());
		$this->assertEquals(0, $GoogleAnalyticsArticleMetrics->getAbonneLikeIndicator());
		$this->assertEquals(0, $GoogleAnalyticsArticleMetrics->getVisiteurLikeIndicator());
		$this->assertEquals(0, $GoogleAnalyticsArticleMetrics->getBounceIndicator());
		
		// test with data
		$GoogleAnalyticsArticleMetrics = New GoogleAnalyticsArticleMetrics();
		$GoogleAnalyticsArticleMetrics->setArticle($article1);
		$GoogleAnalyticsArticleMetrics->setPageviewMeasure(16);
		$GoogleAnalyticsArticleMetrics->setPageviewSubscriberMeasure(10);
		$GoogleAnalyticsArticleMetrics->setPageviewVisitorMeasure(10);
		$GoogleAnalyticsArticleMetrics->setEntranceMeasure(10);
		$GoogleAnalyticsArticleMetrics->setSubscriptionMeasure(5);
		$GoogleAnalyticsArticleMetrics->setUniquepageviewMeasure(10);
		$GoogleAnalyticsArticleMetrics->setReadtimeMeasure(10);
		$GoogleAnalyticsArticleMetrics->setReadtimeSubscriberMeasure(5);
		$GoogleAnalyticsArticleMetrics->setReadtimeVisitorMeasure(2);
		$GoogleAnalyticsArticleMetrics->setUniquepageviewSubscriberMeasure(10);
		$GoogleAnalyticsArticleMetrics->setUniquepageviewVisitorMeasure(10);
		$GoogleAnalyticsArticleMetrics->setExitpageMeasure(10);
		$GoogleAnalyticsArticleMetrics->preCalcul();
		$this->assertEquals(250, $GoogleAnalyticsArticleMetrics->getBounceIndicator());
		$this->assertEquals(250, $GoogleAnalyticsArticleMetrics->getEntranceIndicator());
		$this->assertEquals(125, $GoogleAnalyticsArticleMetrics->getSubscriptionIndicator());
		$this->assertEquals(62.5, $GoogleAnalyticsArticleMetrics->getPageviewIndicator());
		$this->assertEquals(62.5 * (1/$article1->getReadTime()), $GoogleAnalyticsArticleMetrics->getCompletionreadIndicator());
		$this->assertEquals(50 * (1/$article1->getReadTime()), $GoogleAnalyticsArticleMetrics->getCompletionreadSubscriberIndicator());
		$this->assertEquals(20 * (1/$article1->getReadTime()), $GoogleAnalyticsArticleMetrics->getCompletionreadVisitorIndicator());
		$this->assertEquals(62.5* (1/sqrt($article1->getReadTime())), $GoogleAnalyticsArticleMetrics->getAttentionIndicator());
		$this->assertEquals(50* (1/sqrt($article1->getReadTime())), $GoogleAnalyticsArticleMetrics->getAbonneLikeIndicator());
		$this->assertEquals(20* (1/sqrt($article1->getReadTime())), $GoogleAnalyticsArticleMetrics->getVisiteurLikeIndicator());
		
	}
	public function testCalculIndicator()
	{	
		$paramsMgr = $this->container->get('parameters_manager');
		
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
		
		// test no data
		$GoogleAnalyticsArticleMetrics = New GoogleAnalyticsArticleMetrics();
		$GoogleAnalyticsArticleMetrics->setArticle($article1);
		$GoogleAnalyticsArticleMetrics->preCalcul();
		$GoogleAnalyticsArticleMetrics->calculIndicator();
		$this->assertEquals(0, $GoogleAnalyticsArticleMetrics->getEntranceIndicator());
		$this->assertEquals(0, $GoogleAnalyticsArticleMetrics->getSubscriptionIndicator());
		$this->assertEquals(0, $GoogleAnalyticsArticleMetrics->getPageviewIndicator());
		$this->assertEquals(0, $GoogleAnalyticsArticleMetrics->getCompletionreadIndicator());
		$this->assertEquals(0, $GoogleAnalyticsArticleMetrics->getCompletionreadSubscriberIndicator());
		$this->assertEquals(0, $GoogleAnalyticsArticleMetrics->getCompletionreadVisitorIndicator());
		$this->assertEquals(0, $GoogleAnalyticsArticleMetrics->getAttentionIndicator());
		$this->assertEquals(0, $GoogleAnalyticsArticleMetrics->getAbonneLikeIndicator());
		$this->assertEquals(0, $GoogleAnalyticsArticleMetrics->getVisiteurLikeIndicator());
		$this->assertEquals(0, $GoogleAnalyticsArticleMetrics->getBounceIndicator());
		// test with data
		$GoogleAnalyticsArticleMetrics = New GoogleAnalyticsArticleMetrics();
		$GoogleAnalyticsArticleMetrics->setArticle($article1);
		$GoogleAnalyticsArticleMetrics->setPageviewMeasure(16);
		$GoogleAnalyticsArticleMetrics->setPageviewSubscriberMeasure(10);
		$GoogleAnalyticsArticleMetrics->setPageviewVisitorMeasure(10);
		$GoogleAnalyticsArticleMetrics->setEntranceMeasure(10);
		$GoogleAnalyticsArticleMetrics->setSubscriptionMeasure(5);
		$GoogleAnalyticsArticleMetrics->setUniquepageviewMeasure(10);
		$GoogleAnalyticsArticleMetrics->setUniquepageviewSubscriberMeasure(10);
		$GoogleAnalyticsArticleMetrics->setUniquepageviewVisitorMeasure(10);
		$GoogleAnalyticsArticleMetrics->setReadtimeMeasure(10);
		$GoogleAnalyticsArticleMetrics->setReadtimeSubscriberMeasure(5);
		$GoogleAnalyticsArticleMetrics->setReadtimeVisitorMeasure(2);
		$GoogleAnalyticsArticleMetrics->setExitpageMeasure(10);
		
		// change average
		$paramsMgr->setValue('googleanalytics.avg_pageview_measure', 16);
		$paramsMgr->setValue('googleanalytics.avg_pageview_subscriber_measure', 10);
		$paramsMgr->setValue('googleanalytics.avg_pageview_visitor_measure', 10);
		$paramsMgr->setValue('googleanalytics.avg_uniquepageview_measure', 10);
		$paramsMgr->setValue('googleanalytics.avg_uniquepageview_subscriber_measure', 10);
		$paramsMgr->setValue('googleanalytics.avg_uniquepageview_visitor_measure', 10);
		$paramsMgr->setValue('googleanalytics.avg_readtime_measure', 10);
		$paramsMgr->setValue('googleanalytics.avg_readtime_subscriber_measure', 5);
		$paramsMgr->setValue('googleanalytics.avg_readtime_visitor_measure', 2);
		$paramsMgr->setValue('googleanalytics.avg_entrance_measure', 10);
		$paramsMgr->setValue('googleanalytics.avg_entrance_subscriber_measure', 10);
		$paramsMgr->setValue('googleanalytics.avg_entrance_visitor_measure', 10);
		$paramsMgr->setValue('googleanalytics.avg_subscription_measure', 5);
		$paramsMgr->setValue('googleanalytics.avg_completionread_indicator', 62.5 * (1/$article1->getReadTime()));
		$paramsMgr->setValue('googleanalytics.avg_subscription_indicator', 125);
		$paramsMgr->setValue('googleanalytics.avg_entrance_indicator', 250);
		$paramsMgr->setValue('googleanalytics.avg_bounce_indicator', 250);
		$paramsMgr->setValue('googleanalytics.avg_pageview_indicator', 62.5);
		$paramsMgr->setValue('googleanalytics.avg_completionread_subscriber_indicator', 50 * (1/$article1->getReadTime()));
		$paramsMgr->setValue('googleanalytics.avg_completionread_visitor_indicator', 20 * (1/$article1->getReadTime()));
		$paramsMgr->setValue('googleanalytics.avg_attention_indicator', 62.5* (1/sqrt($article1->getReadTime())));
		$paramsMgr->setValue('googleanalytics.avg_abonne_like_indicator', 50* (1/sqrt($article1->getReadTime())));
		$paramsMgr->setValue('googleanalytics.avg_visitor_like_indicator', 20* (1/sqrt($article1->getReadTime())));	
		$paramsMgr->flush();
				
		$GoogleAnalyticsArticleMetrics->preCalcul();
		$GoogleAnalyticsArticleMetrics->calculIndicator();
		
		$this->assertEquals(50, $GoogleAnalyticsArticleMetrics->getEntranceIndicator());
		$this->assertEquals(50, $GoogleAnalyticsArticleMetrics->getSubscriptionIndicator());
		$this->assertEquals(50, $GoogleAnalyticsArticleMetrics->getBounceIndicator());
		$this->assertEquals(50, $GoogleAnalyticsArticleMetrics->getPageviewIndicator());
		$this->assertEquals(50, $GoogleAnalyticsArticleMetrics->getCompletionreadIndicator());
		$this->assertEquals(50 , $GoogleAnalyticsArticleMetrics->getCompletionreadSubscriberIndicator());
		$this->assertEquals(50, $GoogleAnalyticsArticleMetrics->getCompletionreadVisitorIndicator());
		
		$this->assertEquals(50, $GoogleAnalyticsArticleMetrics->getMonetisationIndicator());
		$this->assertEquals(50, $GoogleAnalyticsArticleMetrics->getAttentionIndicator());
		$this->assertEquals(50, $GoogleAnalyticsArticleMetrics->getAudienceIndicator());
		
		$this->assertEquals(50, $GoogleAnalyticsArticleMetrics->getAbonneLikeIndicator());
		$this->assertEquals(50, $GoogleAnalyticsArticleMetrics->getVisiteurLikeIndicator());

	}
	
	private function forceAuthenticate($container) {
		$em = $container->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);
		
		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}
}
