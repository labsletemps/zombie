<?php

namespace ZombieBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use ZombieBundle\Entity\News\Article;
use ZombieBundle\Managers\News\ArticlesManager;

class ZombieCommand extends ContainerAwareCommand {
	
	// use user id=1 for connection security
	private function forceAuthenticate() {
		$em = $this->getContainer()->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);
	
		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}

    protected function configure() {
        $this->setName("zombie:cmd")
                ->addArgument('method', InputArgument::REQUIRED, 'Method')
                ->addArgument('param1', InputArgument::OPTIONAL)
                ->addArgument('param2', InputArgument::OPTIONAL)
                ->addArgument('param3', InputArgument::OPTIONAL)
                ->setDescription("Import des prestataires");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
        	$this->forceAuthenticate();
        	
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
    // delete alla data temp table ArticleTmpLink
    private function cleanTmpLinks(InputInterface $input, OutputInterface $output) {
    	$output->writeln("Cleaning article temporary links table.");
    	
    	$articlesMgr = $this->getContainer()->get('articles_manager');
    	if (false) $articlesMgr = new ArticlesManager();
    	
    	$articlesMgr->cleanTmpLinkDB();
    }
    
    // delete alla data temp table SearchHelper
    private function cleanSearchHelper(InputInterface $input, OutputInterface $output) {
    	$output->writeln("Cleaning Search Helper table.");
    	
    	$searchHelperMgr= $this->getContainer()->get('search_helper_manager');
    	if (false) $searchHelperMgr = new SearchHelperManager();
    	
    	$searchHelperMgr->deleteOldSearchHelpers(new \DateTime());
    }
}
