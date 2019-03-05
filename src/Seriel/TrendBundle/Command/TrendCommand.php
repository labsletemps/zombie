<?php

namespace Seriel\TrendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Seriel\TrendBundle\Managers\GoogleTrendManager;
use Seriel\TrendBundle\Entity\Trend;

class TrendCommand extends ContainerAwareCommand {

	protected $trend_manager = null;

	// use user id=1 for connection security
	private function forceAuthenticate() {
		$em = $this->getContainer()->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);

		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}

    protected function configure() {
        $this->setName("zombie:trend")
                ->addArgument('method', InputArgument::REQUIRED, 'Method')
                ->addArgument('param1', InputArgument::OPTIONAL)
                ->addArgument('param2', InputArgument::OPTIONAL)
                ->addArgument('param3', InputArgument::OPTIONAL)
				->addArgument('param4', InputArgument::OPTIONAL)
				->addArgument('param5', InputArgument::OPTIONAL)
                ->setDescription("trend");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
        	$this->forceAuthenticate();

        	$this->trend_manager = $this->getContainer()->get('seriel_trend.manager');

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
    
    // return google trends with date parameters
    private function getTrends(InputInterface $input, OutputInterface $output,$count, $day_from = null, $day_to = null) {
	    	
	    $TrendMgr = $this->getContainer()->get('seriel_trend.google_trends_manager');
	    if (false) $TrendMgr= new GoogleTrendManager();	    
	    
	    echo "Count : $count". PHP_EOL;
	    echo "Date : $day_from=> $day_to". PHP_EOL;
	    $format = 'Y-m-d';
	    if ($day_from != null) $day_from = \DateTime::createFromFormat($format, $day_from);
	    if ($day_to != null) $day_to= \DateTime::createFromFormat($format, $day_to);
	    
	    $timeDebut = time();
	    
	    echo "-------------------------------------------- \n";
	    $googletrends = $TrendMgr->getTrends($count,$day_from,$day_to, array());
	    foreach ($googletrends as $key => $googletrend) {
	    	echo 'Trend: ['.$key.']'.$googletrend. PHP_EOL;
	    	echo "-------------------------------------------- \n";
	    }
	    $timeFin = time();
	    $delay = $timeFin - $timeDebut;
	    echo "Time = $delay seconds\n";
	    echo "-------------------------------------------- \n";
	    	
	}
	
	// return google trends futurs by day
	private function getTrendsFutur(InputInterface $input, OutputInterface $output, $day, $precision) {
		
		$TrendMgr = $this->getContainer()->get('seriel_trend.google_trends_manager');
		if (false) $TrendMgr= new GoogleTrendManager();
		
		echo "Date : $day". PHP_EOL;
		echo "Precision : $precision". PHP_EOL;
		$format = 'Y-m-d';
		if ($day!= null) $day= \DateTime::createFromFormat($format, $day);		
		$timeDebut = time();
		
		echo "-------------------------------------------- \n";
		$googletrends = $TrendMgr->getTrendsFutur($day,$precision,array());
		foreach ($googletrends as $key => $googletrend) {
			echo 'Trend: ['.$key.']'.$googletrend. PHP_EOL;
			echo "-------------------------------------------- \n";
		}
		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		echo "Time = $delay seconds\n";
		echo "-------------------------------------------- \n";
		
	}
	
	// return google trends by subject(experimental)
	private function getTrendsBySubject(InputInterface $input, OutputInterface $output,$subject) {
		
		$TrendMgr = $this->getContainer()->get('seriel_trend.google_trends_manager');
		if (false) $TrendMgr= new GoogleTrendManager();
		
		echo "Subject : $subject". PHP_EOL;
		$timeDebut = time();
		
		echo "-------------------------------------------- \n";
		$googletrends = $TrendMgr->getTrendsBySubject($subject);
		/*
		foreach ($googletrends as $key => $googletrend) {
			echo 'Trend: ['.$key.']'.$googletrend. PHP_EOL;
			echo "-------------------------------------------- \n";
		}
		*/
		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		echo "Time = $delay seconds\n";
		echo "-------------------------------------------- \n";
		
	}
	
	
	
	// Save google trends in database
	private function importTrendsGoogle(InputInterface $input, OutputInterface $output, $day_from = null, $day_to = null) {
		
		$TrendMgr = $this->getContainer()->get('seriel_trend.google_trends_manager');
		if (false) $TrendMgr= new GoogleTrendManager();
		
		if ((!$day_from) && (!$day_to)) {
			$day_from= date('Y-m-d', time() - (10 * 24 * 3600)); // 10 days ago
			$day_to= date('Y-m-d', time() - (24 * 3600)); // yesterday
		} else if (!$day_to) {
			$day_to= $day_from;
		} else if (!$day_from) {
			// should probably never happen .... anyway
			$day_from= $day_to;
		}
		
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
		
		$timeDebut = time();
		echo "-------------------------------------------- \n";
		$intervalDay = $day_from->diff($day_to)->format('%a') +1;
		$dateCurrent = $day_from;
		
		for ($iterator = 1;$iterator<= $intervalDay; $iterator++) {
			echo 'hottrends :'.$dateCurrent->format('Y-m-d'). PHP_EOL;
			$position = 1;
			$googletrends = $TrendMgr->getTrendsDirect(0,$dateCurrent,null, array());
			foreach ($googletrends as $googleTrend => $weight) {
				//Create Object Trend
				$trend = new Trend();
				$dateClone = clone $dateCurrent;
				$trend->setDate($dateClone);
				$trend->setName($googleTrend);
				$trend->setPosition($position);
				$trend->setModule($TrendMgr->getName());
				$this->trend_manager->save($trend);
				$position ++;
			}
			//delete row in database with the same datetime and module
			$this->trend_manager->removeAllTrendByDateModule($dateCurrent, $TrendMgr->getName());			
			$dateCurrent = $dateCurrent->add(new \DateInterval('P1D'));
		}
		$this->trend_manager->flush();
		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		echo "Time = $delay seconds\n";
		echo "-------------------------------------------- \n";
		
	}

	// List articles suggest for google trends 
	private function getTrendsWithArticleSuggest(InputInterface $input, OutputInterface $output,$count, $day_from = null, $day_to = null) {
		
		$TrendMgr = $this->getContainer()->get('seriel_trend.google_trends_manager');
		if (false) $TrendMgr= new GoogleTrendManager();
		$articlesManager = $this->getContainer()->get("articles_manager");
		if (false) $articlesManager = new ArticlesManager();
		
		echo "Count : $count". PHP_EOL;
		echo "Date : $day_from=> $day_to". PHP_EOL;
		$format = 'Y-m-d';
		if ($day_from != null) $day_from = \DateTime::createFromFormat($format, $day_from);
		if ($day_to != null) $day_to= \DateTime::createFromFormat($format, $day_to);
		
		$timeDebut = time();
		
		echo "-------------------------------------------- \n";
		$googletrends = $TrendMgr->getTrendsDirect($count,$day_from,$day_to, array());
		foreach ($googletrends as $googletrend => $key) {
			echo 'Trend: ['.$key.']'.$googletrend. PHP_EOL;
			echo "-------------------------------------------- ". PHP_EOL;
			// get 5 best Article suggest by trend
	
			$params = array('semantique_relatedword' => $googletrend);
			$articleSuggest = array();
			$articleSuggest= $articlesManager->query($params, array('limit' => 5));
			foreach ($articleSuggest as $article) {
				echo 'Article : ['.$article->getId().']'.$article->getTitre(). PHP_EOL;
			}
			echo "-------------------------------------------- ". PHP_EOL. PHP_EOL;
		}
		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		echo "Time = $delay seconds\n";
		echo "-------------------------------------------- \n";
		
	}

}
