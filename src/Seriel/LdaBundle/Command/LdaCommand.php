<?php

namespace Seriel\LdaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZombieBundle\Managers\News\ArticlesManager;
use Seriel\LdaBundle\Managers\LdaManager;

class LdaCommand extends ContainerAwareCommand {

	protected $lda_manager = null;

	// use user id=1 for connection security
	private function forceAuthenticate() {
		$em = $this->getContainer()->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);

		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}

    protected function configure() {
        $this->setName("zombie:lda")
                ->addArgument('method', InputArgument::REQUIRED, 'Method')
                ->addArgument('param1', InputArgument::OPTIONAL)
                ->addArgument('param2', InputArgument::OPTIONAL)
                ->addArgument('param3', InputArgument::OPTIONAL)
				->addArgument('param4', InputArgument::OPTIONAL)
				->addArgument('param5', InputArgument::OPTIONAL)
                ->setDescription("Lda");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
        	$this->forceAuthenticate();

        	$this->lda_manager = $this->getContainer()->get('seriel_lda.manager');

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

    //return result of algorithm LDA (Allocation de Dirichlet latente) 
	private function calcul(InputInterface $input, OutputInterface $output, $MaxNbarticle = -1, $day_article_release_from = null, $day_article_release_to = null, $nbsubject = 5, $nbword = 10) {

		$LDAMgr = $this->getContainer()->get('seriel_lda.manager');
		if (false) $LDAMgr = new LdaManager();
		$articlesMgr = $this->getContainer()->get('articles_manager');
		if (false) $articlesMgr = new ArticlesManager();

		if ((!$day_article_release_from) && (!$day_article_release_to)) {
    		$day_article_release_from = date('Y-m-d', time() - (10 * 24 * 3600)); // 10 days ago
    		$day_article_release_to = date('Y-m-d', time() - (24 * 3600)); // yesterday
    	} else if (!$day_article_release_to) {
    		$day_article_release_to = $day_article_release_from;
    	} else if (!$day_article_release_from) {
    		// should probably never happen .... anyway
    		$day_article_release_from = $day_article_release_to;
    	}
		echo "Date : $day_article_release_from => $day_article_release_to\n";

		if ($MaxNbarticle > 0) {
			echo "LDA : MaxNbArticle $MaxNbarticle \n";
			$articles = $articlesMgr->getAllArticlesForPeriode($day_article_release_from, $day_article_release_to, array('limit' => $MaxNbarticle));
		}
		else {
			$articles = $articlesMgr->getAllArticlesForPeriode($day_article_release_from, $day_article_release_to);
		}
		$nbarticle = count($articles);
		echo "LDA : NbArticle $nbarticle \n";

		echo "LDA : NbSubject $nbsubject \n";
		echo "LDA : NbWord $nbword \n";
		$lda = $LDAMgr->getDataLda($articles,$nbsubject,null);
		$wordsPerTopics = $lda->getWordsPerTopicsProbabilities($nbword);

		echo "-------------------------------------------- \n";
		foreach ($wordsPerTopics as $topic) {
			foreach ($topic as $key => $value) {
				echo "$key : $value \n";
			}
			echo "-------------------------------------------- \n";
		}

    }

    //return result of algorithm LDA (Allocation de Dirichlet latente) in database
	private function import(InputInterface $input, OutputInterface $output, $MaxNbarticle = -1, $day_article_release_from = null, $day_article_release_to = null, $nbsubject = 5, $purge = -1) {

		$LDAMgr = $this->getContainer()->get('seriel_lda.manager');
		if (false) $LDAMgr = new LdaManager();
		$articlesMgr = $this->getContainer()->get('articles_manager');
		if (false) $articlesMgr = new ArticlesManager();

		if ((!$day_article_release_from) && (!$day_article_release_to)) {
    		$day_article_release_from = date('Y-m-d', time() - (10 * 24 * 3600)); // 10 days ago
    		$day_article_release_to = date('Y-m-d', time() - (24 * 3600)); // yesterday
    	} else if (!$day_article_release_to) {
    		$day_article_release_to = $day_article_release_from;
    	} else if (!$day_article_release_from) {
    		// should probably never happen .... anyway
    		$day_article_release_from = $day_article_release_to;
    	}
		$datelimit = new \Datetime();
		echo "Date : $day_article_release_from => $day_article_release_to\n";

		if ($MaxNbarticle > 0) {
			echo "LDA : MaxNbArticle $MaxNbarticle \n";
			$articles = $articlesMgr->getAllArticlesForPeriode($day_article_release_from, $day_article_release_to, array('limit' => $MaxNbarticle));
		}
		else {
			$articles = $articlesMgr->getAllArticlesForPeriode($day_article_release_from, $day_article_release_to);
		}
		$nbarticle = count($articles);
		echo "LDA : NbArticle $nbarticle \n";
		echo "LDA : NbSubject $nbsubject \n";

		if ($purge == 1) {
			echo "LDA : Purge  \n";
		}else {
			echo "LDA : No Purge  \n";
		}

		$lda = $LDAMgr->getDataLda($articles,$nbsubject,null);

		$LDAMgr->recWordsPerTopicsProbabilities($lda);


		echo "-------------------Import OK----------------- \n";
		if ($purge == 1) {
			$LDAMgr->removeDataCalculate($datelimit);
		}
		echo "---------------PURGE OK------------- \n";
    }
}
