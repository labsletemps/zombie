<?php

namespace ZombieBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use LeTempsSourcesBundle\Managers\ArticleCsvManager;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use ZombieBundle\Entity\News\Article;
use ZombieBundle\Managers\News\ArticlesManager;
use LeTempsSourcesBundle\Managers\ArticleCimetiereManager;
use ZombieBundle\Utils\ZombieUtils;

class ImportCommand extends ContainerAwareCommand {
	
	// use user id=1 for connection security
	private function forceAuthenticate() {
		$em = $this->getContainer()->get("doctrine")->getManager();
		$user = $em->getRepository('Seriel\UserBundle\Entity\User')->find(1);
	
		$token = new AnonymousToken($user->getId(), $user);
		SymfonyUtils::getTokenStorage()->setToken($token);
	}

    protected function configure() {
        $this->setName("zombie:import")
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
    //re-save Article "Le temps" in Database Zombie
    private function resaveAllArticles() {
    	$articlesMgr = $this->getContainer()->get('articles_manager');
    	if (false) $articlesMgr = new ArticlesManager();
    	
    	$articles = $articlesMgr->getAllArticles();
    	
    	if ($articles) {
    		$counter = 0;
			$total = count($articles);
    		foreach ($articles as $article) {
    			if (false) $article = new Article();
    			$chapeau = $article->getChapeau();
    			$article->setChapeau($chapeau);
    			
    			$articlesMgr->save($article);
    			
    			$counter ++;
    			
    			if ($counter%100 == 0) {
    				echo "$counter / $total\n";
    			}
    			
    			if ($counter%1000 == 0) {
    				$articlesMgr->flush();
    			}
    		}
    		
    		$articlesMgr->flush();
    	}
    }
    // calculate metrics of articles with chartbeat data and average chartbeat data
    private function testart(InputInterface $input, OutputInterface $output) {
    	
    	$articlesMgr = $this->getContainer()->get('articles_manager');
    	if (false) $articlesMgr = new ArticlesManager();
    	$articles = $articlesMgr->getAllArticles();
    }
    //import Article "Le temps" in Database Zombie
    private function importArticles($input, $output, $date_debut, $date_fin) {
    	
    	$leTempsArtsMgr = $this->getContainer()->get('le_temps.arts_manager');
    	if (false) $leTempsArtsMgr = new ArticleCsvManager();
    	
    	$articlesMgr = $this->getContainer()->get('articles_manager');
    	if (false) $articlesMgr = new ArticlesManager();
    	

    	
    	if (substr($date_debut, 0, 6) == 'today-') {
    		$nb_days = intval(trim(substr($date_debut, 6)));
    		$date_debut= date('Y-m-d', time() - ($nb_days * 24 * 3600));
    	}
    	if (substr($date_fin, 0, 6) == 'today-') {
    		$nb_days = intval(trim(substr($date_fin, 6)));
    		$date_fin= date('Y-m-d', time() - ($nb_days * 24 * 3600));
    	}
    	$output->writeln("Calculate :  $date_debut >> $date_fin");
    	
    	$articlesMap = array();
    	
    	$articles = $articlesMgr->getAllArticlesForPeriode($date_debut, $date_fin);
    	
    	if ($articles) {
    		foreach ($articles as $article) {
    			if (false) $article = new Article();
    			$guid = $article->getGuid();
    			
    			$articlesMap[$guid] = $article;
    		}
    	}
    	echo "Number of articles dest : ".count($articlesMap)."\n";
    	
    	
    	$leTempsArtsMap = array();
    	$leTempsArts = $leTempsArtsMgr->getAllArtsForPeriode($date_debut, $date_fin);

    	if ($leTempsArts) {
    		foreach ($leTempsArts as $art) {
    			$guid = $art['guid'];
    			$leTempsArtsMap[$guid] = $art;
    		}
    	}
    	
    	echo "Number of articles src : ".count($leTempsArtsMap)."\n";


    	
    	$counter = 0;
    	
    	$counter_add = 0;
    	$counter_update = 0;
    	
    	// OK, let's do the real stuff.
    	foreach ($leTempsArtsMap as $guid => $art) {
    		$counter++;
    		
    		if (isset($articlesMap[$guid])) {
    			$article = $articlesMap[$guid];
    			$counter_update++;
    		} else {
    			$article = new Article();
    			$counter_add++;
    		}
    		
    		$article->setGuid($guid);
    		$article->setTitre($art['titre']);
    		$article->setChapeau($art['chapeau']);
    		$article->setContent($art['content']);
    		$article->setDateParution($art['date_parution']);
    		$article->setMotCle($art['mot_cle']);
    		$article->setSection($art['section']);
    		$article->setAuteur($art['auteur']);
    		$article->setAuteurExterne($art['auteur_ext']);
    		$article->setImageUrl($art['image']);
    		
    		$tags = trim($art['tags']);
    		if (strtoupper($tags) == 'NULL') $tags = null;
    		$article->setTags($tags);
    		
    		$articlesMgr->save($article);
    		if ($counter % 100 == 0) $articlesMgr->flush();
    	}
    	$articlesMgr->flush();
    	
    	// Delete article if is not exist
    	$counter_del = 0;
    	foreach ($articlesMap as $guid => $article) {
    		if (!isset($leTempsArtsMap[$guid])) {
    			// Delete element
    			$counter_del++;
    			
    			$article->setDeleted(true);
    			$articlesMgr->save($article);
    			if ($counter_del % 100 == 0) $articlesMgr->flush();
    		}
    	}
    	
    	$articlesMgr->flush();
    	
    	echo "\nRESUME : added[$counter_add] updated[$counter_update] removed[$counter_del]\n\n";
    }

    // import cimetiere "Le temps" in database Article Zombie
    private function importCimetiere() {
    	$cimetiere = $this->getContainer()->get('le_temps.arts_cimetiere_manager');
    	if (false) $cimetiere = new ArticleCimetiereManager();
    	
    	$cimetiere_arts = $cimetiere->getAllArts();
    	
    	$articlesMgr = $this->getContainer()->get('articles_manager');
    	if (false) $articlesMgr = new ArticlesManager();
    	
    	$guids = array();
    	
    	foreach ($cimetiere_arts as $art) {
    		$guid = $art['guid'];
    		$guids[] = $guid;
    	}
    	
    	$localArts = $articlesMgr->getAllArticleForListGuid($guids);
    	$localArtsByGuid = array();
    	
    	if ($localArts) {
    		foreach ($localArts as $localArt) {
    			if (false) $localArt = new Article();
    			$localArtsByGuid[$localArt->getGuid()] = $localArt;
    		}
    	}
    	
    	$counter = 0;
    	 
    	$counter_add = 0;
    	$counter_update = 0;
    	
    	foreach ($cimetiere_arts as $art) {
    		$counter++;
    		
    		$guid = $art['guid'];
    		
    		$article = null;
    		if (isset($localArtsByGuid[$guid])) {
    			$article = $localArtsByGuid[$guid];
    			$counter_update++;
    		} else {
    			$article = new Article();
    			$counter_add++;
    		}
    		
    		echo $art['titre']."\n";
    		
    		$article->setGuid($guid);
    		$article->setTitre($art['titre']);
    		$article->setChapeau($art['chapeau']);
    		$article->setContent($art['content']);
    		$article->setDateParution($art['date_parution']);
    		$article->setMotCle($art['mot_cle']);
    		$article->setSection($art['section']);
    		$article->setAuteur($art['auteur']);
    		$article->setAuteurExterne($art['auteur_ext']);
    		$article->setImageUrl($art['image']);
    		
    		$tags = trim($art['tags']);
    		if (strtoupper($tags) == 'NULL') $tags = null;
    		$article->setTags($tags);
    		
    		$articlesMgr->save($article);
    		if ($counter % 100 == 0) $articlesMgr->flush();
    	}
    	
    	$articlesMgr->flush();
    	
    	echo "\nRESUME : added[$counter_add] updated[$counter_update]\n\n";
    }
    
    
    // create Uri article
    private function createUriArticles(InputInterface $input, OutputInterface $output, $day_article_release_from = null, $day_article_release_to = null) {
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
    	
    	$output->writeln("Date :  $day_article_release_from => $day_article_release_to");
    	
    	$articlesMgr = $this->getContainer()->get('articles_manager');
    	if (false) $articlesMgr = new ArticlesManager();
    	
    	$articles = $articlesMgr->getAllArticlesForPeriode($day_article_release_from, $day_article_release_to);
    	
    	$counter = 0;
    	
    	if ($articles) {
    		$em = $this->getContainer()->get('doctrine')->getManager();
    		
    		foreach ($articles as $article) {
    			if (false) $article = new Article();
    			$real_path = array();
    			$article_url = ZombieUtils::build_drupal_url_from_article($article);
    			$real_path[$article_url] = $article_url;
    			$alternative_paths = ZombieUtils::build_drupal_url_alternatives_from_article($article);
    			foreach ($alternative_paths as $alt_path) {
    				$real_path[$alt_path] = $alt_path;
    			}
    	
    			if ($real_path) {
    				$article->setUris($real_path);
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
}
