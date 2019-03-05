<?php

namespace Seriel\TrendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Seriel\TrendBundle\Entity\Trend;

class CrossTrendCommand extends ContainerAwareCommand {

	protected $trend_manager = null;

	// use user id=1 for connection security
	private function forceAuthenticate() {
		$em = $this->getContainer()->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);

		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}

    protected function configure() {
        $this->setName("zombie:crosstrend")
                ->addArgument('method', InputArgument::REQUIRED, 'Method')
                ->addArgument('param1', InputArgument::OPTIONAL)
                ->addArgument('param2', InputArgument::OPTIONAL)
                ->addArgument('param3', InputArgument::OPTIONAL)
				->addArgument('param4', InputArgument::OPTIONAL)
				->addArgument('param5', InputArgument::OPTIONAL)
                ->setDescription("crosstrend");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
        	$this->forceAuthenticate();

        	$this->trend_manager = $this->getContainer()->get('seriel_trend.cross_trends_manager');

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
	    	

    	echo "Count : $count". PHP_EOL;
	    echo "Date : $day_from=> $day_to". PHP_EOL;
	    $format = 'Y-m-d';
	    if ($day_from != null) $day_from = \DateTime::createFromFormat($format, $day_from);
	    if ($day_to != null) $day_to= \DateTime::createFromFormat($format, $day_to);
	    
	    $timeDebut = time();
	    
	    echo "-------------------------------------------- \n";
	    $trends = $this->trend_manager->getTrends($count,$day_from,$day_to, array());
	    foreach ($trends as $key => $trend) {
	    	echo 'Trend: ['.$key.']'.$trend. PHP_EOL;
	    	echo "-------------------------------------------- \n";
	    }
	    $timeFin = time();
	    $delay = $timeFin - $timeDebut;
	    echo "Time = $delay seconds\n";
	    echo "-------------------------------------------- \n";
	    	
	}
	
	// return google trends futurs by day
	private function getTrendsFutur(InputInterface $input, OutputInterface $output, $day, $precision) {
			
		echo "Date : $day". PHP_EOL;
		echo "Precision : $precision". PHP_EOL;
		$format = 'Y-m-d';
		if ($day!= null) $day= \DateTime::createFromFormat($format, $day);		
		$timeDebut = time();
		
		echo "-------------------------------------------- \n";
		$trends= $this->trend_manager->getTrendsFutur($day,$precision,array());
		foreach ($trends as $key => $trend) {
			echo 'Trend: ['.$key.']'.$trend. PHP_EOL;
			echo "-------------------------------------------- \n";
		}
		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		echo "Time = $delay seconds\n";
		echo "-------------------------------------------- \n";
		
	}
	
	

}
