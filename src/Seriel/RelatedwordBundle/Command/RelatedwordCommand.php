<?php

namespace Seriel\RelatedwordBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZombieBundle\Managers\News\ArticlesManager;


class RelatedwordCommand extends ContainerAwareCommand {

	protected $related_word_manager = null;

	// use user id=1 for connection security
	private function forceAuthenticate() {
		$em = $this->getContainer()->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);

		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}

    protected function configure() {
        $this->setName("zombie:relatedword")
                ->addArgument('method', InputArgument::REQUIRED, 'Method')
                ->addArgument('param1', InputArgument::OPTIONAL)
                ->addArgument('param2', InputArgument::OPTIONAL)
                ->addArgument('param3', InputArgument::OPTIONAL)
				->addArgument('param4', InputArgument::OPTIONAL)
				->addArgument('param5', InputArgument::OPTIONAL)
                ->setDescription("relatedword");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
        	$this->forceAuthenticate();

        	$this->related_word_manager = $this->getContainer()->get('seriel_related_word.manager');

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

	// import all relatedword and calcul link related word with articles
    private function import(InputInterface $input, OutputInterface $output, $day_article_release_from = null, $day_article_release_to = null , $MaxNbarticle = -1) {

		$articlesMgr = $this->getContainer()->get('articles_manager');
		if (false) $articlesMgr = new ArticlesManager();

		if ((!$day_article_release_from) && (!$day_article_release_to)) {
			$day_article_release_from= date('Y-m-d', time() - (24 * 3600)); // yesterday
	   		$day_article_release_to = date('Y-m-d', time() - (24 * 3600)); // yesterday
	  	} else if (!$day_article_release_to) {
	   		$day_article_release_to = $day_article_release_from;
	   	} else if (!$day_article_release_from) {
	   		// should probably never happen .... anyway
	   		$day_article_release_from = $day_article_release_to;
	   	}
	   	$output->writeln("Date : $day_article_release_from => $day_article_release_to");
	   	
		if ($MaxNbarticle > 0) {
			$output->writeln("MaxNbArticle $MaxNbarticle ");
			$articles = $articlesMgr->getAllArticlesForPeriode($day_article_release_from, $day_article_release_to, array('limit' => $MaxNbarticle));
		}
		else {
			$articles = $articlesMgr->getAllArticlesForPeriode($day_article_release_from, $day_article_release_to);
		}
		$nbarticle = count($articles);
		$output->writeln("NbArticle $nbarticle");

		$timeDebut = time();
		$output->writeln("--------------------------------------------");
		if ($nbarticle > 0) {
			$wordsPerTopics = $this->related_word_manager->calculRelatedword($articles,null);
		}
		$output->writeln("--------------------------------------------");
		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		$output->writeln("Time = $delay seconds");
	}
	
	// update calcul link related word and Number of word
	private function update(InputInterface $input, OutputInterface $output, $day_article_release_from = null, $day_article_release_to = null ) {
		
		$articlesMgr = $this->getContainer()->get('articles_manager');
		if (false) $articlesMgr = new ArticlesManager();
		$ArticlewordMgr = $this->getContainer()->get('seriel_related_word.article_word_manager');
		if (false) $ArticlewordMgr = new RelatedwordArticleWordManager();
		$linkwordMgr = $this->getContainer()->get('seriel_related_word.link_word_manager');
		if (false) $linkwordMgr = new RelatedwordLinkWordManager();
		$wordMgr = $this->getContainer()->get('seriel_related_word.word_manager');
		if (false) $wordMgr = new RelatedwordWordManager();
		
		if ((!$day_article_release_from) && (!$day_article_release_to)) {
			$day_article_release_from= date('Y-m-d', time() - (24 * 3600)); // yesterday
			$day_article_release_to = date('Y-m-d', time() - (24 * 3600)); // yesterday
		} else if (!$day_article_release_to) {
			$day_article_release_to = $day_article_release_from;
		} else if (!$day_article_release_from) {
			// should probably never happen .... anyway
			$day_article_release_from = $day_article_release_to;
		}
		$output->writeln("Date : $day_article_release_from => $day_article_release_to");
		

		$articles = $articlesMgr->getAllArticlesForPeriode($day_article_release_from, $day_article_release_to);

		$nbarticle = count($articles);
		$output->writeln("NbArticle $nbarticle");
		
		$timeDebut = time();
		$output->writeln("--------------------------------------------");
		if ($nbarticle > 0) {

			$ArraywordId = array();
			$AllArticleWords = $ArticlewordMgr->getAllWordIdByArticles($articles);
			foreach ($AllArticleWords as $articleWord) {
				$ArraywordId[$articleWord->getWord()->getId()] = $articleWord->getWord()->getId();
			}
			//calcul quantity total for word
			$wordMgr->generateAllWordQuantityByWord($ArraywordId);
			//calcul LinkWord
			$linkwordMgr->generateAllLinkWordByWord($ArraywordId);
			
		}
		$output->writeln("--------------------------------------------");
		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		$output->writeln("Time = $delay seconds");
	}
	
	// update link word
	private function updateLinkWord(InputInterface $input, OutputInterface $output,$nbwordbySql, $axe, $start =null) {

		$wordMgr = $this->getContainer()->get('seriel_related_word.word_manager');
		if (false) $wordMgr = new RelatedwordWordManager();
		$linkwordMgr = $this->getContainer()->get('seriel_related_word.link_word_manager');
		if (false) $linkwordMgr = new RelatedwordLinkWordManager();
		
		if (isset($start)) {
			$startID = $start;
		} else {
			$word = $wordMgr->getAllWord(array('limit' => 1, 'orderBy' => array('id' => 'asc') ));
			$startID = $word[0]->getId();
		}

		$output->writeln("start at $startID");
		$word = $wordMgr->getAllWord(array('limit' => 1, 'orderBy' => array('id' => 'desc') ));
		$finishID = $word[0]->getId();
		
		$output->writeln("finish at $finishID");
		$min = $startID;
		$max= $startID + $nbwordbySql;
		$timeDebut = time();
		$output->writeln("--------------------------------------------");
		while ($min <= $finishID) {
			$output->writeln("$min => $max");
			$linkwordMgr->generateAllLinkWordByGroup($min,$max,$axe);
			$min = $max;
			$max = $max + $nbwordbySql;
		}

		$output->writeln("--------------------------------------------");
		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		$output->writeln("Time = $delay seconds");
	}
	
	// return the best related word for one word
	private function getrelatedword(InputInterface $input, OutputInterface $output, $texte = '', $nbRelatedWordbyWord = 10, $weight = 1) {

		echo "text : $texte \n";
		echo "NbRelatedWord by word : $nbRelatedWordbyWord \n";
		echo "Weight defined for RelatedWord : $weight \n";
		$timeDebut = time();
		echo "-------------------------------------------- \n";
		$Relatedwords = $this->related_word_manager->getRelatedWord($texte,$nbRelatedWordbyWord,$weight,true);

		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		echo "Time = $delay seconds\n";
		echo "-------------------------------------------- \n";

		foreach ($Relatedwords as $key => $value) {
			echo "$key : $value \n";
		}
	}
	
	// return similarity between 2 words with related word
	private function getSimilarity(InputInterface $input, OutputInterface $output, $texte1 = '', $texte2 = '', $depth = 40) {
		
		echo "text1 : $texte1 \n";
		echo "text2 : $texte2 \n";
		echo "Depth : $depth \n";
		$timeDebut = time();
		echo "-------------------------------------------- \n";
		$percent = $this->related_word_manager->getSimilarity($texte1,$texte2,$depth);
		echo "Similarity : $percent\n";
		
		$timeFin = time();
		$delay = $timeFin - $timeDebut;
		echo "Time = $delay seconds\n";
		echo "-------------------------------------------- \n";
	
	}
	    
	// return the best related word for google trends of day
	private function getTrendsRelatedWord(InputInterface $input, OutputInterface $output) {
	    	
	   	$TrendMgr = $this->getContainer()->get('seriel_trend.google_trends_manager');
	   	if (false) $TrendMgr= new TrendManager();
	    	
	   	$timeDebut = time();
	   	echo "-------------------------------------------- \n";
	   	$trends = $TrendMgr->getLastSubject();
	   	foreach ($trends as $trend => $position) {
	   		echo 'Trend: '.$trend. PHP_EOL;
	   		$Relatedwords = $this->related_word_manager->getRelatedWord($trend,10,1,true);
	   		foreach ($Relatedwords as $key => $value) {
	   			echo "-------------$key : $value \n";
	   		}
	   		echo "-------------------------------------------- \n";
	   	}
	    	
	   	$timeFin = time();
	   	$delay = $timeFin - $timeDebut;
	   	echo "Time = $delay seconds\n";
	   	echo "-------------------------------------------- \n";
	    	
	 }

}
