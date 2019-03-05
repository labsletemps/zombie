<?php

namespace ZombieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ZombieBundle\Managers\News\ArticlesManager;
use ZombieBundle\Managers\Modules\ModulesManager;

class ArticleController extends Controller {

	public function indexAction($id) {
		$articlesMgr = $this->get('articles_manager');
		if (false) $articlesMgr = new ArticlesManager();

		$article = $articlesMgr->getArticle($id);

        return $this->render('ZombieBundle:Article:article.html.twig', array('article' => $article));
    }

    public function infosAction($id) {
    	$articlesMgr = $this->get('articles_manager');
    	if (false) $articlesMgr = new ArticlesManager();

    	$article = $articlesMgr->getArticle($id);

    	return $this->render('ZombieBundle:Article:article_infos.html.twig', array('article' => $article));
    }

    public function historiqueAction($id) {
    	$modulesMgr = $this->get("zombie_modules_manager");
    	if (false) $modulesMgr = new ModulesManager();
    	$moduleHistory = $modulesMgr->getArticleHistoryModule();
    	
    	return $this->render('ZombieBundle:Article:article_historique.html.twig', array('article_id' => $id, 'module_history' => $moduleHistory));
    }

    public function historiqueResumeAction($id) {
    	return $this->render('ZombieBundle:Article:article_historique_resume.html.twig', array('article_id' => $id));
    }

    public function historiqueModeleAction($modele, $id) {
    	$articlesMgr = $this->get('articles_manager');
    	if (false) $articlesMgr = new ArticlesManager();

    	$article = $articlesMgr->getArticle($id);

    	$modele = strtolower($modele);

    	$modulesMgr = $this->get("zombie_modules_manager");
    	if (false) $modulesMgr = new ModulesManager();

    	$content = $modulesMgr->getArticleHistory($modele, $article);
    	if ($content === null) $content = '<div class="not_available">Not available</div>';


    	return $this->render('ZombieBundle:Article:article_historique_modele.html.twig', array('article_id' => $id, 'modele' => $modele, 'content' => $content));
    }

    public function semantiqueAction($id) {
    	$modulesMgr = $this->get("zombie_modules_manager");
    	if (false) $modulesMgr = new ModulesManager();
    	$moduleSemantics = $modulesMgr->getArticleSemanticsModule();
    	return $this->render('ZombieBundle:Article:article_semantique.html.twig', array('article_id' => $id, 'module_semantics' => $moduleSemantics));
    }

    public function semantiqueModuleAction($module, $id) {
    	$articlesMgr = $this->get('articles_manager');
    	if (false) $articlesMgr = new ArticlesManager();

    	$article = $articlesMgr->getArticle($id);

    	$module = strtolower($module);

    	$modulesMgr = $this->get("zombie_modules_manager");
    	if (false) $modulesMgr = new ModulesManager();

    	$content = $modulesMgr->getArticleSemantics($module, $article);
    	if ($content === null) $content = '<div class="not_available">Not available</div>';

    	return $this->render('ZombieBundle:Article:article_semantique_module.html.twig', array('article_id' => $id, 'module' => $module, 'content' => $content));
    }
    
    public function tmpArticleLinkAction($code) {
    	$content = "";
    	if ($code) {
    		$articlesMgr = $this->get('articles_manager');
    		if (false) $articlesMgr = new ArticlesManager();

    		$article = $articlesMgr->getArticleFromTmpCode($code);

    		if ($article) {
    			$content = $article->getContentStriped();
    		}
    	}

    	return $this->render('ZombieBundle:Article:article_txt.html.twig', array('content' => $content));
    }
}
