<?php

namespace Seriel\GoogleAnalyticsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class GoogleAnalyticsCommand extends ContainerAwareCommand {

	protected $google_analytics_manager = null;

	// use user id=1 for connection security
	private function forceAuthenticate() {
		$em = $this->getContainer()->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);

		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}

    protected function configure() {
        $this->setName("zombie:googleanalytics")
                ->addArgument('method', InputArgument::REQUIRED, 'Method')
                ->addArgument('param1', InputArgument::OPTIONAL)
                ->addArgument('param2', InputArgument::OPTIONAL)
                ->addArgument('param3', InputArgument::OPTIONAL)
				->addArgument('param4', InputArgument::OPTIONAL)
				->addArgument('param5', InputArgument::OPTIONAL)
                ->setDescription("googleanalytics");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
        	$this->forceAuthenticate();

        	$this->google_analytics_manager= $this->getContainer()->get('seriel_google_analytics.manager');

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
    
    // get all session between 2 date with api GoogleAnalytics ( use account GoogleAnalytics in parameters )
    private function getSession(InputInterface $input, OutputInterface $output, $day_from = null, $day_to = null) {
	    		    
	    $timeDebut = time();
	    
	    echo "-------------------------------------------- \n";
	    echo "Date : $day_from=> $day_to". PHP_EOL;
	    $format = 'Y-m-d';
	    if ($day_from != null) $day_from = \DateTime::createFromFormat($format, $day_from);
	    if ($day_to != null) $day_to= \DateTime::createFromFormat($format, $day_to);
	    
	    $sessions = $this->google_analytics_manager->getSession($day_from, $day_to);
	    
	    echo 'Sessions : '.$sessions. PHP_EOL;
	    $timeFin = time();
	    $delay = $timeFin - $timeDebut;
	    echo "Time = $delay seconds\n";
	    echo "-------------------------------------------- \n";
	    	
	}

	// get all pagesViews between 2 date and filter with api GoogleAnalytics ( use account GoogleAnalytics in parameters )
	private function getPageViews(InputInterface $input, OutputInterface $output, $day_from = null, $day_to = null, $filterPage = null) {
		
		$timeDebut = time();
		
		echo "-------------------------------------------- \n";
		echo "Date : $day_from=> $day_to". PHP_EOL;
		echo "Filter : $filterPage". PHP_EOL;
		
		$format = 'Y-m-d';
		if ($day_from != null) $day_from = \DateTime::createFromFormat($format, $day_from);
		if ($day_to != null) $day_to= \DateTime::createFromFormat($format, $day_to);
		
		$Arraypage= $this->google_analytics_manager->getPageViews($day_from, $day_to, $filterPage);
		foreach ($Arraypage as $page){
			echo 'Page : '.$page[0].' => '. $page[1]. PHP_EOL;
		}
		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		echo "Time = $delay seconds\n";
		echo "-------------------------------------------- \n";
		
	}
	
	// get all timeOnpage between 2 date and filter with api GoogleAnalytics ( use account GoogleAnalytics in parameters )
	private function getTimeOnPage(InputInterface $input, OutputInterface $output, $day_from = null, $day_to = null, $filterPage = null) {
		
		$timeDebut = time();
		
		echo "-------------------------------------------- \n";
		echo "Date : $day_from=> $day_to". PHP_EOL;
		echo "Filter : $filterPage". PHP_EOL;
		
		$format = 'Y-m-d';
		if ($day_from != null) $day_from = \DateTime::createFromFormat($format, $day_from);
		if ($day_to != null) $day_to= \DateTime::createFromFormat($format, $day_to);
		
		$Arraypage= $this->google_analytics_manager->getTimeOnPage($day_from, $day_to, $filterPage);
		foreach ($Arraypage as $page){
			echo 'Page : '.$page[0].' => '. $page[1]. PHP_EOL;
		}
		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		echo "Time = $delay seconds\n";
		echo "-------------------------------------------- \n";
		
	}
	
	// get all entrance between 2 date and filter with api GoogleAnalytics ( use account GoogleAnalytics in parameters )
	private function getEntranceonPage(InputInterface $input, OutputInterface $output, $day_from = null, $day_to = null, $filterPage = null) {
		
		$timeDebut = time();
		
		echo "-------------------------------------------- \n";
		echo "Date : $day_from=> $day_to". PHP_EOL;
		echo "Filter : $filterPage". PHP_EOL;
		
		$format = 'Y-m-d';
		if ($day_from != null) $day_from = \DateTime::createFromFormat($format, $day_from);
		if ($day_to != null) $day_to= \DateTime::createFromFormat($format, $day_to);
		
		$filterSource = 'ga:source!@news.google.fr;ga:source!=192.168.1.8:5001';
		$Arraypage= $this->google_analytics_manager->getEntranceonPage($day_from, $day_to, $filterPage, $filterSource);
		
		foreach ($Arraypage as $page){
			echo 'Page : '.$page[0].'-'.$page[1].' => '. $page[2]. PHP_EOL;
		}
		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		echo "Time = $delay seconds\n";
		echo "-------------------------------------------- \n";
		
	}
	
	// get all exit page between 2 date and filter with api GoogleAnalytics ( use account GoogleAnalytics in parameters )
	private function getExitPage(InputInterface $input, OutputInterface $output, $day_from = null, $day_to = null, $filterPage = null) {
		
		$timeDebut = time();
		
		echo "-------------------------------------------- \n";
		echo "Date : $day_from=> $day_to". PHP_EOL;
		echo "Filter : $filterPage". PHP_EOL;
		
		$format = 'Y-m-d';
		if ($day_from != null) $day_from = \DateTime::createFromFormat($format, $day_from);
		if ($day_to != null) $day_to= \DateTime::createFromFormat($format, $day_to);
		
		$Arraypage= $this->google_analytics_manager->getExitPage($day_from, $day_to, $filterPage);
		
		foreach ($Arraypage as $page){
			echo 'Page : '.$page[0].' => '. $page[1]. PHP_EOL;
		}
		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		echo "Time = $delay seconds\n";
		echo "-------------------------------------------- \n";
		
	}
	

	// get all pagesViews between 2 date and 2 filter pages with api GoogleAnalytics ( use account GoogleAnalytics in parameters )
	private function getLinkNavigationPage(InputInterface $input, OutputInterface $output, $day_from = null, $day_to = null, $filterPageSource= null, $filterPageTarget= null) {
		
		$timeDebut = time();
		
		echo "-------------------------------------------- \n";
		echo "Date : $day_from=> $day_to". PHP_EOL;
		echo "Filter source : $filterPageSource". PHP_EOL;
		echo "Filter target : $filterPageTarget". PHP_EOL;
		$format = 'Y-m-d';
		if ($day_from != null) $day_from = \DateTime::createFromFormat($format, $day_from);
		if ($day_to != null) $day_to= \DateTime::createFromFormat($format, $day_to);
		
		$Arraypage= $this->google_analytics_manager->getLinkNavigationPage($day_from, $day_to, $filterPageSource, $filterPageTarget);
		
		foreach ($Arraypage as $page){
			echo 'Page : '.$page[0].' => '. $page[1].' : '. $page[2]. PHP_EOL;
		}
		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		echo "Time = $delay seconds\n";
		echo "-------------------------------------------- \n";		
	}

	// calcul googleAnalyticsDayReport and save in database
	private function calculateMeasure(InputInterface $input, OutputInterface $output, $day_from = null, $day_to = null) {

		$timeDebut = time();
		echo "-------------------------------------------- \n";

		echo "Date : $day_from=> $day_to". PHP_EOL;
		

		$format = 'Y-m-d';
		if (substr($day_from, 0, 6) == 'today-') {
			$nb_days = intval(trim(substr($day_from, 6)));
			$day_from= date('Y-m-d', time() - ($nb_days * 24 * 3600));
		}
		if ($day_from != null){
			$day_from = \DateTime::createFromFormat($format, $day_from);
		}
	
		if (substr($day_to, 0, 6) == 'today-') {
			$nb_days = intval(trim(substr($day_to, 6)));
			$day_to= date('Y-m-d', time() - ($nb_days * 24 * 3600));
		}
		if ($day_to != null){
			$day_to= \DateTime::createFromFormat($format, $day_to);
		}
	
		$noerror = $this->google_analytics_manager->calculateDayReport($day_from, $day_to);
		if(!$noerror) {
			echo "Error detected". PHP_EOL;
		}

		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		$output->writeln("Time = $delay seconds");
		$output->writeln("--------------------------------------------");
	}
	
	// calcul all googleAnalyticsArticleMetrics by value of googleAnalyticsDayReport in Database
	private function calculateMetrics(InputInterface $input, OutputInterface $output, $day_from = null, $day_to = null) {
		
		$timeDebut = time();
		$output->writeln("--------------------------------------------");

		$format = 'Y-m-d';
		if (substr($day_from, 0, 6) == 'today-') {
			$nb_days = intval(trim(substr($day_from, 6)));
			$day_from= date('Y-m-d', time() - ($nb_days * 24 * 3600));
		}
		if ($day_from != null){
			$day_from = \DateTime::createFromFormat($format, $day_from);
		}
		
		if (substr($day_to, 0, 6) == 'today-') {
			$nb_days = intval(trim(substr($day_to, 6)));
			$day_to= date('Y-m-d', time() - ($nb_days * 24 * 3600));
		}
		if ($day_to != null){
			$day_to= \DateTime::createFromFormat($format, $day_to);
		}
		
		$noerror = $this->google_analytics_manager->calculateMetrics($day_from,$day_to);
		if(!$noerror) {
			echo "Error detected". PHP_EOL;
		}
		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		$output->writeln("Time = $delay seconds");
		$output->writeln("--------------------------------------------");
	}
	
	// calcul  googleAnalyticsArticleMetrics  for day (monday,tuesday,...)
	private function calculateMetricsForDay(InputInterface $input, OutputInterface $output) {
		
		$timeDebut = time();
		$output->writeln("--------------------------------------------");
		
		$day = date('Y-m-d');
		$output->writeln("Calculate for :  $day");
	
		
		$noerror = $this->google_analytics_manager->calculateMetrics($$day);
		if(!$noerror) {
			echo "Error detected". PHP_EOL;
		}
		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		$output->writeln("Time = $delay seconds");
		$output->writeln("--------------------------------------------");
	}
	
}
