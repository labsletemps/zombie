<?php

namespace Seriel\DandelionBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZombieBundle\Managers\News\ArticlesManager;
use ZombieBundle\Entity\News\Article;
use Seriel\DandelionBundle\Managers\DandelionArticleSemanticsManager;
use Seriel\DandelionBundle\Managers\DandelionArticleEntityManager;
use Seriel\DandelionBundle\Managers\DandelionArticleEntityLinkEntityManager;



class DandelionCommand extends ContainerAwareCommand {

	protected $dandelion_manager = null;

	// use user id=1 for connection security
	private function forceAuthenticate() {
		$em = $this->getContainer()->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);

		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}

    protected function configure() {
        $this->setName("zombie:dandelion")
                ->addArgument('method', InputArgument::REQUIRED, 'Method')
                ->addArgument('param1', InputArgument::OPTIONAL)
                ->addArgument('param2', InputArgument::OPTIONAL)
                ->addArgument('param3', InputArgument::OPTIONAL)
                ->setDescription("Dandelion");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
        	$this->forceAuthenticate();

        	$this->dandelion_manager = $this->getContainer()->get('seriel_dandelion.manager');

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

    // use api dandelion on Article and save data dandelion in database
    private function extractEntitiesFromArticle(InputInterface $input, OutputInterface $output, $day = null) {
    	if (substr($day, 0, 6) == 'today-') {
    		$nb_days = intval(trim(substr($day, 6)));
    		$day = date('Y-m-d', time() - ($nb_days * 24 * 3600));
    	}

    	if (!$day) $day = date('Y-m-d', time() - (24 * 3600)); // yesterday

    	$output->writeln("getMeasures :  $day");


    	$articlesMgr = $this->getContainer()->get('articles_manager');
    	if (false) $articlesMgr = new ArticlesManager();

    	$articles = $articlesMgr->getAllArticlesForPeriode($day, $day);

    	$semanticsMgr = $this->getContainer()->get('seriel_dandelion.article_semantics_manager');
    	if (false) $semanticsMgr = new DandelionArticleSemanticsManager();

    	if ($articles) {
    		foreach ($articles as $article) {
    			$artSemantics = $this->dandelion_manager->entityExtractionFromArticle($article);
    			if ($artSemantics) $semanticsMgr->save($artSemantics);
    		}

    		if ($artSemantics) $semanticsMgr->flush();
    	}
    }

    // calcul information with dandelion data 
    private function calculate(InputInterface $input, OutputInterface $output, $day_article_release_from = null, $day_article_release_to = null) {
    	$semanticsMgr = $this->getContainer()->get('seriel_dandelion.article_semantics_manager');
    	if (false) $semanticsMgr = new DandelionArticleSemanticsManager();

    	$EntityMgr= $this->getContainer()->get('seriel_dandelion.dandelion_entities_manager');
    	if (false) $EntityMgr= new DandelionEntitiesManager();
    	
    	$articleEntityMgr = $this->getContainer()->get('seriel_dandelion.dandelion_article_entity_manager');
    	if (false) $articleEntityMgr= new DandelionArticleEntityManager();
    	
    	$articleEntityLinkMgr= $this->getContainer()->get('seriel_dandelion.dandelion_article_entity_link_entity_manager');
    	if (false) $articleEntityLinkMgr= new DandelionArticleEntityLinkEntityManager();
    	
    	
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
    	//echo "Calculate : $day_article_release_from => $day_article_release_to\n";
    	$output->writeln("Calculate :  $day_article_release_from => $day_article_release_to");
    	$articlesMgr = $this->getContainer()->get('articles_manager');
    	if (false) $articlesMgr = new ArticlesManager();

    	$articles = $articlesMgr->getAllArticlesForPeriode($day_article_release_from, $day_article_release_to);

    	$counter = 0;
		$NBArticles = count($articles);
    	if ($articles) {
    		foreach ($articles as $article) {
    			if (false) $article = new Article();
    			$output->writeln("Progression :  $counter / $NBArticles");
    			
    			$counter++;
    			$artSemantics = $semanticsMgr->getDandelionArticleSemanticsForArticleId($article->getId());

    			if (!$artSemantics) continue;
    			
    			$artSemantics->calculate();	
    			$semanticsMgr->save($artSemantics);		   			
    			
    		}
    		$semanticsMgr->flush();
    		$EntityMgr->updateAllQuantityDandelionEntity();	
    	}
    }

}
