<?php

namespace Seriel\ChartbeatBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Seriel\ChartbeatBundle\Entity\ChartbeatArticleDayReport;
use Seriel\ChartbeatBundle\Managers\ChartbeatArticleDayReportManager;
use ZombieBundle\Managers\News\ArticlesManager;
use ZombieBundle\Utils\ZombieUtils;
use ZombieBundle\Entity\News\Article;
use Seriel\ChartbeatBundle\Managers\ChartbeatArticleMetricsManager;
use Seriel\ChartbeatBundle\Entity\ChartbeatArticleMetrics;

class ChartbeatCommand extends ContainerAwareCommand {
	
	protected $charbeat_manager = null;
	
	// use user id=1 for connection security
	private function forceAuthenticate() {
		$em = $this->getContainer()->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);
	
		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}

    protected function configure() {
        $this->setName("zombie:chartbeat")
                ->addArgument('method', InputArgument::REQUIRED, 'Method')
                ->addArgument('param1', InputArgument::OPTIONAL)
                ->addArgument('param2', InputArgument::OPTIONAL)
                ->addArgument('param3', InputArgument::OPTIONAL)
                ->setDescription("Import api chartbeat");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
        	$this->forceAuthenticate();
        	
        	$this->charbeat_manager = $this->getContainer()->get('seriel_chartbeat.manager');
        	
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
    // test connexion api chatbeat
    private function test(InputInterface $input, OutputInterface $output) {
    	$this->charbeat_manager->test();
    }
    
    // save report data chartbeat per day in database
    private function getReportForDay(InputInterface $input, OutputInterface $output, $day = null, $only_if_empty = true) {
    	if (substr($day, 0, 6) == 'today-') {
    		$nb_days = intval(trim(substr($day, 6)));
			$day = date('Y-m-d', time() - ($nb_days * 24 * 3600));
    	}
    	if (!$day) $day = date('Y-m-d', time() - (24 * 3600)); // yesterday
    	
    	echo "getReport $day\n";
    	
    	$cabdrMgr = $this->getContainer()->get('seriel_chartbeat.cbadr_manager');
    	if (false) $cabdrMgr = new ChartbeatArticleDayReportManager();
    
    	if ($only_if_empty) {
    		$cbadrs = $cabdrMgr->getAllChartbeatArticleDayReportForDay($day);
    		$qte = 0;
    		if ($cbadrs) {
    			foreach ($cbadrs as $cbadr) {
    				$qte++;
    			}
    		}
    		
    		if ($qte) {
    			echo "FOUND $qte ELEMS. NOT PROCEEDING\n\n";
    			exit(0);
    		}
    	}
    	
    	$csv_datas = $this->charbeat_manager->getReportForDay($day);
    	$counter = 0;
    	
    	if ($csv_datas) {
    		$lines = explode("\n", $csv_datas);
    		
    		$first_line = null;
    		
    		$result = array();
    		
    		foreach ($lines as $line) {
    			if (!$line) continue;
    			
    			$row = str_getcsv($line, ",", '"', "\\");
    			
    			if (!$first_line) {
    				$first_line = $row;
    				continue;
    			}
    			
    			$res_row = array('day' => $day);
    			
    			for ($i = 0; $i < count($row); $i++) {
    				$key = $first_line[$i];
    				$value = $row[$i];
    				
    				$res_row[$key] = $value;
    			}
    			
    			$result[] = $res_row;
    			
    			$counter++;
    		}
    	
    		// OK create elements.
    		foreach ($result as $data) {
    			$data_day = $data['day'];
				$data_path = $data['path'];
				
				
				if (strpos($data_path, '/user/') === 0) continue;
				
				$cbadr = $cabdrMgr->getChartbeatArticleDayReportForPathAndDay($data_path, $data_day);
				$cbadr = $cbadr ? ChartbeatArticleDayReport::updateFromArray($cbadr, $data) : ChartbeatArticleDayReport::createFromArray($data);
    			
    			$cabdrMgr->save($cbadr);
    		}
    		
    		$cabdrMgr->flush();
    	} else {
    		echo "ERROR !\n";
    	}
    	
    	echo "\nDONE\n\n";
    }
/*
    // connect reprt chartbeat with article
    private function matchArticlesWithChartbeatReports(InputInterface $input, OutputInterface $output, $day_article_release_from = null, $day_article_release_to = null) {
    	if ((!$day_article_release_from) && (!$day_article_release_to)) {
    		$day_article_release_from = date('Y-m-d', time() - (10 * 24 * 3600)); // 10 days ago
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
    	
    	$output->writeln("Pre-Calculate :  $day_article_release_from => $day_article_release_to");
    	
    	$articlesMgr = $this->getContainer()->get('articles_manager');
    	if (false) $articlesMgr = new ArticlesManager();
    	
    	$articles = $articlesMgr->getAllArticlesForPeriode($day_article_release_from, $day_article_release_to);
    	
    	$counter = 0;
    	
    	if ($articles) {
    		$em = $this->getContainer()->get('doctrine')->getManager();
    		
    		foreach ($articles as $article) {
    			if (false) $article = new Article();
    			$article_url = ZombieUtils::build_drupal_url_from_article($article);
    			
    			// Let's try to find this URL in chartbeat's reports.
    			$sql = "SELECT distinct path FROM chartbeat_article_day_report WHERE path like '$article_url'";
    			
    			$stmt = $em->getConnection()->prepare($sql);
    			$stmt->execute();
    			$rows = $stmt->fetchAll();
    			
    			$real_path = null;
    			
    			if ($rows) {
    				foreach ($rows as $row) {
    					$real_path = $row['path'];
    				}
    			}
    			
    			if (!$real_path) {
    				$alternative_paths = ZombieUtils::build_drupal_url_alternatives_from_article($article);
    				
    				if ($alternative_paths) {
    					foreach ($alternative_paths as $alt_path) {
    						$sql = "SELECT distinct path FROM chartbeat_article_day_report WHERE path = '$alt_path'";
    							
    						$stmt = $em->getConnection()->prepare($sql);
    						$stmt->execute();
    						$rows = $stmt->fetchAll();
    						
    						if ($rows) {
    							foreach ($rows as $row) {
    								$real_path = $row['path'];
    							}
    						}

    						if ($real_path) {    							
    							break;
    						}
    					}
    				}
    			}
    			
    			if ($real_path) {
    				$article->setUris(array($real_path));
    			} else {
    				$article->setUris(array());
    				echo "$article_url ... not found\n";
    			}
    			 
    			$articlesMgr->save($article);
    			 
    			$counter++;
    			if ($counter%10 == 0) $articlesMgr->flush();
    		}
    	}
    	
    	$articlesMgr->flush();
    }
    */
    // pre calculate metrics of articles with chartbeat data
    private function pre_calculate(InputInterface $input, OutputInterface $output, $day_article_release_from = null, $day_article_release_to = null) {
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
    	$output->writeln("Pre-Calculate :  $day_article_release_from => $day_article_release_to");
    	
    	$artMetricsMgr = $this->getContainer()->get('seriel_chartbeat.article_metrics_manager');
    	if (false) $artMetricsMgr = new ChartbeatArticleMetricsManager();
    	
    	$articlesMgr = $this->getContainer()->get('articles_manager');
    	if (false) $articlesMgr = new ArticlesManager();
    	 
    	$articles = $articlesMgr->getAllArticlesForPeriode($day_article_release_from, $day_article_release_to);
    	 
    	$counter = 0;
    	 
    	if ($articles) {
    		foreach ($articles as $article) {
    			if (false) $article = new Article();
    			
    			$artMetrics = $artMetricsMgr->getChartbeatArticleMetricsForArticleId($article->getId());
    			if (!$artMetrics) $artMetrics = new ChartbeatArticleMetrics($article);
    			
    			$artMetrics->preCalculate();
    			
    			$artMetricsMgr->save($artMetrics);
    			$counter++;
    		}
    		
    		$artMetricsMgr->flush();
    	}
    	
    	$artMetricsMgr->updateMoyennesGenerales();
    }
    
    // calculate metrics of articles with chartbeat data and average chartbeat data
    private function calculate(InputInterface $input, OutputInterface $output, $day_article_release_from = null, $day_article_release_to = null) {
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
    	
    	$artMetricsMgr = $this->getContainer()->get('seriel_chartbeat.article_metrics_manager');
    	if (false) $artMetricsMgr = new ChartbeatArticleMetricsManager();
    	 
    	$articlesMgr = $this->getContainer()->get('articles_manager');
    	if (false) $articlesMgr = new ArticlesManager();
    
    	$articles = $articlesMgr->getAllArticlesForPeriode($day_article_release_from, $day_article_release_to);
    
    	$counter = 0;
    
    	if ($articles) {
    		foreach ($articles as $article) {
    			if (false) $article = new Article();
    			 
    			$artMetrics = $artMetricsMgr->getChartbeatArticleMetricsForArticleId($article->getId());
    			if (!$artMetrics) $artMetrics = new ChartbeatArticleMetrics($article);
    			 
    			$artMetrics->calculate();
    			 
    			$artMetricsMgr->save($artMetrics);
    			 
    			$counter++;
    		}
    
    		$artMetricsMgr->flush();
    	}
    	 
    	$artMetricsMgr->updateMoyennesGenerales();
    }

    // calculate metrics of articles for day (monday,tuesday,...)
    private function calculateForDay(InputInterface $input, OutputInterface $output) {

    	$day = date('Y-m-d');
    	$output->writeln("Calculate for :  $day");
    	
    	
    	$artMetricsMgr = $this->getContainer()->get('seriel_chartbeat.article_metrics_manager');
    	if (false) $artMetricsMgr = new ChartbeatArticleMetricsManager();
    	
    	$articlesMgr = $this->getContainer()->get('articles_manager');
    	if (false) $articlesMgr = new ArticlesManager();
    	
    	$articles = $articlesMgr->getAllArticlesForDay($day);

    	
    	$counter = 0;
    	
    	if ($articles) {
    		foreach ($articles as $article) {
    			if (false) $article = new Article();
    			
    			$artMetrics = $artMetricsMgr->getChartbeatArticleMetricsForArticleId($article->getId());
    			if (!$artMetrics) $artMetrics = new ChartbeatArticleMetrics($article);
    			
    			$artMetrics->preCalculate();
    			
    			$artMetricsMgr->save($artMetrics);
    			$counter++;
    		}
    		
    		$artMetricsMgr->flush();
    	}
    	
    	$artMetricsMgr->updateMoyennesGenerales();
    	
    	
    	$counter = 0;
    	
    	if ($articles) {
    		foreach ($articles as $article) {
    			if (false) $article = new Article();
    			
    			$artMetrics = $artMetricsMgr->getChartbeatArticleMetricsForArticleId($article->getId());
    			if (!$artMetrics) $artMetrics = new ChartbeatArticleMetrics($article);
    			
    			$artMetrics->calculate();
    			
    			$artMetricsMgr->save($artMetrics);
    			
    			$counter++;
    		}
    		
    		$artMetricsMgr->flush();
    	}
    	
    	$artMetricsMgr->updateMoyennesGenerales();
    	
    }
    

}
