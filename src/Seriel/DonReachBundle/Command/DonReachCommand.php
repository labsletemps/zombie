<?php

namespace Seriel\DonReachBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZombieBundle\Managers\News\ArticlesManager;
use ZombieBundle\Entity\News\Article;
use Seriel\DonReachBundle\Managers\DonReachArticleMetricsManager;
use Seriel\DonReachBundle\Entity\DonReachArticleMetrics;
use Doctrine\Common\Collections\ArrayCollection;

class DonReachCommand extends ContainerAwareCommand {
	
	protected $donreach_manager = null;
	
	// use user id=1 for connection security
	private function forceAuthenticate() {
		$em = $this->getContainer()->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);
	
		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}

    protected function configure() {
        $this->setName("zombie:donreach")
                ->addArgument('method', InputArgument::REQUIRED, 'Method')
                ->addArgument('param1', InputArgument::OPTIONAL)
                ->addArgument('param2', InputArgument::OPTIONAL)
                ->addArgument('param3', InputArgument::OPTIONAL)
                ->setDescription("DonReach");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
        	$this->forceAuthenticate();
        	
        	$this->donreach_manager = $this->getContainer()->get('seriel_donreach.manager');
        	
        	$method = $input->getArgument('method');
        	$param = $input->getArgument('param1');
        	$param2 = $input->getArgument('param2');
        	$param3 = $input->getArgument('param3');
        	
        	if( $method ){ // ex: php app/console ae:replic replicateDevis
        		$output->writeln("$method **********************");
        		if ($param !== null) {
        			if ($param2 !== null) {
        				if ($param3 !== null) {
        					eval('$this->'.$method.'($input, $output, $param, $param2, $param3);');
        				} else {
        					eval('$this->'.$method.'($input, $output, $param, $param2);');
        				}
        					
        			}
        			else {
        				eval('$this->'.$method.'($input, $output, $param);');
        			}
        		}
        		else eval('$this->'.$method.'($input, $output);');
        		
        		exit(0);
        	}
        	
        } catch (Exception $ex) {
        	
        }
    }
    
    // Save data api Donreach in database on Article per day
    private function getMeasuresForDay(InputInterface $input, OutputInterface $output, $day = null) {
    	if (substr($day, 0, 6) == 'today-') {
    		$nb_days = intval(trim(substr($day, 6)));		
			$day = date('Y-m-d', time() - ($nb_days * 24 * 3600));
    	}
    	
    	if (!$day) $day = date('Y-m-d', time() - (24 * 3600)); // yesterday
    	
    	$output->writeln("getMeasures :  $day");
    	
    	$articlesMgr = $this->getContainer()->get('articles_manager');
    	if (false) $articlesMgr = new ArticlesManager();
    	 
    	$articles = $articlesMgr->getAllArticlesForPeriode($day, $day);
    	
    	$dramMgr = $this->getContainer()->get('seriel_donreach.article_metrics_manager');
    	if (false) $dramMgr = new DonReachArticleMetricsManager();
    	
    	if ($articles) {
    		foreach ($articles as $article) {
    			if (false) $article = new Article();
    			$data = $this->donreach_manager->getDatasForArticle($article);
    			
    			$dram = $dramMgr->getDonReachArticleMetricsForArticleId($article->getId());
    			$dram = $dram ? DonReachArticleMetrics::updateFromArray($dram, $data) : DonReachArticleMetrics::createFromArray($article, $data);
    			
    			$dramMgr->save($dram);
    		}
    	}
    	
    	$dramMgr->flush();
    }
    
    // Calulate metrics Donreach with data report
    private function calculate(InputInterface $input, OutputInterface $output, $day_article_release_from = null, $day_article_release_to = null) {
    	$dramMgr = $this->getContainer()->get('seriel_donreach.article_metrics_manager');
    	if (false) $dramMgr = new DonReachArticleMetricsManager();
    	 
    	$dramMgr->updateMoyennesGenerales();
    	
    	if ((!$day_article_release_from) && (!$day_article_release_to)) {
    		$day_article_release_from = '2000-01-01';
    		$day_article_release_to = date('Y-m-d', time() - (24 * 3600)); // yesterday
    	} else if (!$day_article_release_to) {
    		$day_article_release_to = $day_article_release_from;
    	} else if (!$day_article_release_from) {
    		// should probably never happen .... anyway
    		$day_article_release_from = $day_article_release_to;
    	}
    	if (substr($day_article_release_from, 0, 6) == 'today-') {
    		$nb_days = intval(trim(substr($day_article_release_from, 6)));
    		$day_article_release_from= date('Y-m-d', time() - ($nb_days * 24 * 3600));
    	}
    	if (substr($day_article_release_to, 0, 6) == 'today-') {
    		$nb_days = intval(trim(substr($day_article_release_to, 6)));
    		$day_article_release_to= date('Y-m-d', time() - ($nb_days * 24 * 3600));
    	}
    	 
    	$output->writeln("Calculate :  $day_article_release_from => $day_article_release_to");
    	$articlesMgr = $this->getContainer()->get('articles_manager');
    	if (false) $articlesMgr = new ArticlesManager();
    
    	$articles = $articlesMgr->getAllArticlesForPeriode($day_article_release_from, $day_article_release_to);
    
    	$counter = 0;
    
    	if ($articles) {
    		foreach ($articles as $article) {
    			if (false) $article = new Article();
    
    			$artMetrics = $dramMgr->getDonReachArticleMetricsForArticleId($article->getId());
    			if (!$artMetrics) $artMetrics = DonReachArticleMetrics::createFromArray($article, array('day' => new \DateTime()));
    
    			$artMetrics->calculate();
    
    			$dramMgr->save($artMetrics);
    
    			$counter++;
    		}
    
    		$dramMgr->flush();
    	}
    
    	$dramMgr->updateMoyennesGenerales();
    }
    
    // Calulate metrics Donreach with data report of google Analytics for article view on period
    private function calculateByView(InputInterface $input, OutputInterface $output, $day_article_release_from = null, $day_article_release_to = null) {

    	
    	if ((!$day_article_release_from) && (!$day_article_release_to)) {
    		$day_article_release_from = '2000-01-01';
    		$day_article_release_to = date('Y-m-d', time() - (24 * 3600)); // yesterday
    	} else if (!$day_article_release_to) {
    		$day_article_release_to = $day_article_release_from;
    	} else if (!$day_article_release_from) {
    		// should probably never happen .... anyway
    		$day_article_release_from = $day_article_release_to;
    	}
    	if (substr($day_article_release_from, 0, 6) == 'today-') {
    		$nb_days = intval(trim(substr($day_article_release_from, 6)));
    		$day_article_release_from= date('Y-m-d', time() - ($nb_days * 24 * 3600));
    	}
    	if (substr($day_article_release_to, 0, 6) == 'today-') {
    		$nb_days = intval(trim(substr($day_article_release_to, 6)));
    		$day_article_release_to= date('Y-m-d', time() - ($nb_days * 24 * 3600));
    	}
    	
    	$output->writeln("Calculate :  $day_article_release_from => $day_article_release_to");
    	
    	
    	$dramMgr = $this->getContainer()->get('seriel_donreach.article_metrics_manager');
    	if (false) $dramMgr = new DonReachArticleMetricsManager();
    	
    	$articlesMgr = $this->getContainer()->get('articles_manager');
    	if (false) $articlesMgr = new ArticlesManager();

    	$GADayReportMgr = $this->getContainer()->get('seriel_google_analytics.day_report_manager');
    	if (false) $GADayReportMgr= new GoogleAnalyticsDayReportManager();
    	//search articles by view google Analytics
    	$dayreportsGa = $GADayReportMgr->getGoogleAnalyticsDayReportByDateView($day_article_release_from,$day_article_release_to,3);
    	$uris = array();
    	foreach ($dayreportsGa as $dayreport) {
    		$uris[] = $dayreport->getPath();
    	}
  
    	$NBuris= count($uris);
    	if ($NBuris == 0) {
    		return false;
    	}
    	$output->writeln("NB paths : $NBuris");
 
    	$articles= $articlesMgr->getAllArticleForListUris($uris);
		$NBArticles = count($articles);
    	$output->writeln("NB articles : $NBArticles");
    	if ($articles) {
    		foreach ($articles as $article) {
    			if (false) $article = new Article();
    			$data = $this->donreach_manager->getDatasForArticle($article);
    			
    			$dram = $dramMgr->getDonReachArticleMetricsForArticleId($article->getId());
    			$dram = $dram ? DonReachArticleMetrics::updateFromArray($dram, $data) : DonReachArticleMetrics::createFromArray($article, $data);
    			
    			$dramMgr->save($dram);
    		}
    	}
    	
    	$dramMgr->flush();
    	
    	$dramMgr->updateMoyennesGenerales();
    	$counter = 0;
    	
    	if ($articles) {
    		foreach ($articles as $article) {
    			if (false) $article = new Article();
    			
    			$artMetrics = $dramMgr->getDonReachArticleMetricsForArticleId($article->getId());
    			if (!$artMetrics) $artMetrics = DonReachArticleMetrics::createFromArray($article, array('day' => new \DateTime()));
    			
    			$artMetrics->calculate();
    			
    			$dramMgr->save($artMetrics);
    			
    			$counter++;
    		}
    		
    		$dramMgr->flush();
    	}
    	
    	$dramMgr->updateMoyennesGenerales();
    }
    
}
