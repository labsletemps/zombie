<?php

namespace Seriel\CrossIndicatorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class CrossIndicatorCommand extends ContainerAwareCommand {

	protected $cross_indicator_manager = null;

	// use user id=1 for connection security
	private function forceAuthenticate() {
		$em = $this->getContainer()->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);

		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}

    protected function configure() {
        $this->setName("zombie:crossindicator")
                ->addArgument('method', InputArgument::REQUIRED, 'Method')
                ->addArgument('param1', InputArgument::OPTIONAL)
                ->addArgument('param2', InputArgument::OPTIONAL)
                ->addArgument('param3', InputArgument::OPTIONAL)
				->addArgument('param4', InputArgument::OPTIONAL)
				->addArgument('param5', InputArgument::OPTIONAL)
                ->setDescription("crossindicator");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
        	$this->forceAuthenticate();

        	$this->cross_indicator_manager= $this->getContainer()->get('seriel_cross_indicator.manager');

        	$method = $input->getArgument('method');
        	$param = $input->getArgument('param1');
        	$param2 = $input->getArgument('param2');
        	$param3 = $input->getArgument('param3');
        	$param4 = $input->getArgument('param4');
        	$param5 = $input->getArgument('param5');

        	if( $method ){ // ex: php app/console ae:replic replicateDevis
        		$output->writeln("$method **********************");
        		if ($param !== null) {
        			if ($param2 !== null) {
        				if ($param3 !== null) {
							if ($param4 !== null) {
								if ($param5 !== null) {
		        					eval('$this->'.$method.'($input, $output, $param, $param2, $param3,$param4, $param5);');
		        				} else {
		        					eval('$this->'.$method.'($input, $output, $param, $param2, $param3, $param4);');
		        				}
	        				} else {
	        					eval('$this->'.$method.'($input, $output, $param, $param2, $param3);');
	        				}
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
  	
	// calcul all cross indicator
	private function AllcalculateIndicators(InputInterface $input, OutputInterface $output) {
		
		$timeDebut = time();
		$output->writeln("-------------------------------------------- \n");

		$noerror = $this->cross_indicator_manager->calculateForAllArticle();

		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		$output->writeln("Time = $delay seconds\n");
		$output->writeln("-------------------------------------------- \n");
	}
	
	// precalcul indicator between 2 date parution for article
	private function precalculateIndicators(InputInterface $input, OutputInterface $output,$day_article_release_from = null, $day_article_release_to = null) {
	
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


		$ArticleMgr = $this->getContainer()->get('articles_manager');
		if (false) $ArticleMgr= new ArticleManager();
		$output->writeln("Pre-Calculate : $day_article_release_from => $day_article_release_to\n");
		$timeDebut = time();
		$output->writeln("-------------------------------------------- \n");
		if ((!isset($day_article_release_from)) and (!isset($day_article_release_to))) {
			$articles  = $ArticleMgr->getAllArticles();
		}
		else {
			$articles = $ArticleMgr->getAllArticlesForPeriode($day_article_release_from, $day_article_release_to);
		}
		$this->cross_indicator_manager->precalculateArticles($articles);
		
		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		$output->writeln("Time = $delay seconds\n");
		$output->writeln("-------------------------------------------- \n");
	}
	
	// calcul indicator (just after pre-calculate) between 2 date parution for article
	private function calculateIndicators(InputInterface $input, OutputInterface $output,$day_article_release_from = null, $day_article_release_to = null) {
		
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
		
		$output->writeln("Calculate : $day_article_release_from => $day_article_release_to\n");
		$CrossArticleMgr = $this->getContainer()->get('seriel_cross_indicator.article_metrics_manager');
		
		$timeDebut = time();
		$output->writeln("-------------------------------------------- \n");
		if ((!isset($day_article_release_from)) and (!isset($day_article_release_to))) {
			$crossIndicatorarticles = $CrossArticleMgr->getAllCrossIndicatorArticle();
		}
		else {
			$crossIndicatorarticles = $CrossArticleMgr->getAllCrossIndicatorArticleByDateArticle($day_article_release_from,$day_article_release_to);
		}
		$this->cross_indicator_manager->calculateArticles($crossIndicatorarticles);
		
		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		$output->writeln("Time = $delay seconds\n");
		$output->writeln("-------------------------------------------- \n");
	}
	
	// update parameters cross indicator (just after calculateIndicators)
	private function updateParameterIndicator(InputInterface $input, OutputInterface $output) {
		
		$timeDebut = time();
		$output->writeln("-------------------------------------------- \n");
		$this->cross_indicator_manager->updateParameterIndicator();
		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		$output->writeln("Time = $delay seconds\n");
		$output->writeln("-------------------------------------------- \n");
	}
	
	
	//calcul indicator for day (monday, tuesday,...)
	private function calculateIndicatorsForDay(InputInterface $input, OutputInterface $output) {
		
		$timeDebut = time();
		$output->writeln("-------------------------------------------- \n");
		
		$day = date('Y-m-d');
		$output->writeln("Calculate for :  $day");
		
		$ArticleMgr = $this->getContainer()->get('articles_manager');
		if (false) $ArticleMgr= new ArticleManager();

		$articles = $ArticleMgr->getAllArticlesForDay($day);
		
		$this->cross_indicator_manager->precalculateArticles($articles);
		
		$CrossArticleMgr = $this->getContainer()->get('seriel_cross_indicator.article_metrics_manager');
		$crossIndicatorarticles = $CrossArticleMgr->getAllCrossIndicatorArticleByDateArticleDay($day);
		$this->cross_indicator_manager->calculateArticles($crossIndicatorarticles);

		$this->cross_indicator_manager->updateParameterIndicator();
		
		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		$output->writeln("Time = $delay seconds\n");
		$output->writeln("-------------------------------------------- \n");
	}

	
}
