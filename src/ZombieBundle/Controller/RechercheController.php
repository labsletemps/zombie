<?php

namespace ZombieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Seriel\AppliToolboxBundle\Managers\ListeManager;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Seriel\AppliToolboxBundle\Entity\ConfigurationListe;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use ZombieBundle\Managers\News\ArticlesManager;
use ZombieBundle\Managers\Securite\ConnexionsManager;
use ZombieBundle\Entity\Recherche\RechercheSauvegarde;
use ZombieBundle\Managers\Recherche\RecherchesSauvegardesManager;
use ZombieBundle\Managers\Individu\IndividusManager;

class RechercheController extends Controller {

    private $user;
    private $em;

    public function preExecute() {

        $this->user = SymfonyUtils::getTokenUser();
        if (!is_object($this->user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $this->em = $this->get('doctrine')->getManager();
    }

    /**
     *	Main page search
     * @return type
     */
    public function indexAction() {

    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_recherche')) {
    		throw $this->createAccessDeniedException();
    	}
    	$authChecker = SymfonyUtils::getAuthorizationChecker();
    	$ModuleMetrics = array();
    	$LogoModuleMetrics = array();
    	$SearchSemantique= array();
    	//Search Modules
    	$modulesMgr = $this->container->get('zombie_modules_manager');
    	if (false) $modulesMgr = new ModulesManager();
    	$modules = $modulesMgr->getZombieModules();
    	if ($modules) {
    		foreach ($modules as $name => $paramsModule) {
    			//METRICS
    			if (isset($paramsModule['metrics_object_class']) && $paramsModule['metrics_object_class']) {
    				
    				//get indicator in object metrics    	
    				$minmaxMetrics = array();
    				$indicators = $paramsModule['metrics_object_class']::getAllIdIndicators();

    				foreach ($indicators as $indicator => $label ) {
    					$minmaxMetrics[$name.'_'. $indicator] = $label ;
    				}
    				
    				$ModuleMetrics[$name] = $minmaxMetrics;
    				
    				//get logoindicator in object metrics
    				$logoMetrics = array();
    				$indicators = $paramsModule['metrics_object_class']::getAllLogoIndicators();
    				foreach ($indicators as $indicator => $labellogo ) {
    					$logoMetrics[$name.'_'. $indicator] = $labellogo;
    				}
    				$LogoModuleMetrics[$name] = $logoMetrics;
    			}



  
    			//SEARCH SEMANTIQUE
    			if (isset($paramsModule['search_semantique']) && $paramsModule['search_semantique']) {

    				$SearchSemantique[] = $name;
    			}
    			//Trends
    			if (isset($paramsModule['service_trend']) && $paramsModule['service_trend']) {
    				$ServiceTrend[$name] = $paramsModule['service_trend'];
    			}
    		}
    	}	
    	return $this->render('ZombieBundle:Recherche:recherche.html.twig', array('moduleMetrics'=> $ModuleMetrics,'logoModuleMetrics'=> $LogoModuleMetrics,'searchSemantique' => $SearchSemantique, 'serviceTrend' => $ServiceTrend));
    }

    public function searchArticleAction(Request $request) {
    	
    	if (!$this->get('security.authorization_checker')->isGranted('nav_recherche_article')) {
    		throw $this->createAccessDeniedException();
    	}
    	$articlesManager = $this->get("articles_manager");
    	if (false) $articlesManager = new ArticlesManager();

    	$params = $articlesManager->getSearchParamsFromRequest($request);
    	$results = array();

    	$results = $articlesManager->query($params, array('limit' => 50, 'result_type' => 'search_object'));

    	$liste_manager = $this->get('liste_manager');
    	if (false) $liste_manager = new ListeManager();

    	// do you have the columns  of result in parameters ?
    	$cols = $request->get('cols');
    	
    	$options = null;
    	if ($cols) {
    		$columns = explode(';', $cols);
    		$options = array('columns' => $columns);
    	}   	
 
    	return new Response($liste_manager->render($results, null, 'ZombieBundle\\Entity\\News\\Article', $options));
    }
    
    // return article in json with parameters search article
    public function searchArticleJsonAction(Request $request, $parameters=null) {
    	
    	if (!$this->get('security.authorization_checker')->isGranted('nav_recherche_article')) {
    		throw $this->createAccessDeniedException();
    	}

    	$articlesManager = $this->get("articles_manager");
    	if (false) $articlesManager = new ArticlesManager();
    	
    	if ($request->isMethod('POST')) {    		
    		if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
    			$params= json_decode($request->getContent(), true);
    		} else {
    			$params = $articlesManager->getSearchParamsFromRequest($request);
    		}  		
    	} else {	
    		$params = $articlesManager->getSearchParamsFromUrl($parameters);
    	}
    	
    	
    	$results = $articlesManager->query($params, array('result_type' => 'search_object'));
    	$serializer = $this->get('serializer');
    	$json = $serializer->serialize($results, 'json');
    	
    	$response = new Response($json);
    	$response->headers->set('Content-Type', 'application/json');
    	
    	return $response;
	}

    public function searchConnexionAction(Request $request) {
    	
    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_recherche')) {
    		throw $this->createAccessDeniedException();
    	}
    	$connexionsMgr = $this->get("connexions_manager");
    	if (false) $connexionsMgr = new ConnexionsManager();

    	$params = $connexionsMgr->getSearchParamsFromRequest($request);

    	$results = array();
    	$results = $connexionsMgr->search($params, array('limit' => 50, 'result_type' => 'search_object'));

    	$liste_manager = $this->get('liste_manager');
    	if (false) $liste_manager = new ListeManager();

    	// do you have the columns  of result in parameters ?
    	$cols = $request->get('cols');

    	$options = null;
    	if ($cols) {
    		$columns = explode(';', $cols);
    		$options = array('columns' => $columns);
    	}

    	return new Response($liste_manager->render($results, null, 'ZombieBundle\\Entity\\Securite\\Connexion', $options));
    }


    /**
     *
     * @param Request $request
     * @return type
     */
    public function settingsAction(Request $request) {
    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_recherche')) {
    		throw $this->createAccessDeniedException();
    	}
        $type = $request->get('t');

        $manager = $this->get($type."s_manager");
        $fields = $manager->getFieldsName();
        // fields used by the current user
        $fields_user = $manager->getUserFieldsName($this->user);

        // we delete the existing fields
        foreach ($fields_user as $field) {
            $key = array_search($field, $fields);
            if ($key !== false) unset($fields[$key]);
        }

        return $this->render('ZombieBundle:Recherche:settings.html.twig', array("fields" => $fields, "fields_user" => $fields_user));
    }

    /**
     *
     * @param Request $request
     * @return type
     */
    public function saveSettingsAction(Request $request) {
    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_recherche')) {
    		throw $this->createAccessDeniedException();
    	}
        $type = $request->get('t');
        $params = $request->get("params");

        $manager = $this->get($type."s_manager");

        $entity = $manager->getUserList($this->user);

        if ($entity == null)
            $entity = new ConfigurationListe();
        $entity->setType($type);
        $entity->setContent($params);
        $entity->setUser($this->user);
        $this->em->persist($entity);
        $this->em->flush();

        return $this->render('ZombieBundle:Recherche:blank.html.twig');
    }

    public function saveAction(Request $request, $type) {
    	
    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_recherche')) {
    		throw $this->createAccessDeniedException();
    	}
    	$search = $request->get('search');
    	$colonnes = $request->get('colonnes');
    	$editables = $request->get('editables');

    	$securityMgr = $this->get('security_manager');
    	$individu = $securityMgr->getCurrentIndividu();

    	$saved_search = new RechercheSauvegarde();
    	$saved_search->setType($type);

    	$form = $this->createFormBuilder($saved_search)
    		->add('nom', TextType::class, array('label' => 'Nom *', 'required' => true))
    		->add('submit', SubmitType::class)
    		->getForm();

    	$rsMgr = $this->get('recherches_sauvegardes_manager');
    	if (false) $rsMgr = new RecherchesSauvegardesManager();

    	$form->handleRequest($request);

    	// verify forms and return the good template
    	if ($form->isSubmitted() && $form->isValid()) {
    		// get individu user 
    		$secuMgr = $this->get('security_manager');
    		$individu = $secuMgr->getCurrentIndividu();

    		// Attention, if exist, get search element.
    		$db_rs = $rsMgr->getRechercheSauvegardeForTypeNomAndIndividu($type, $saved_search->getNom(), $individu->getId());

    		if ($db_rs) {
    			//  update db_rs.
    			$saved_search = $db_rs;
    		}
    		$saved_search->setChaineRecherche($search);

    		$saved_search->setIndividu($individu);

    		// do you have the informations  of column stockage ?
    		$mode_colonnes = intval($request->get('mode_colonnes'));

    		if ($mode_colonnes) {
    			$saved_search->setModeColonnes(intval($mode_colonnes));
    			if ($mode_colonnes == 1) {
    				$saved_search->setChaineColonnes($colonnes);
    			} else {
    				$saved_search->setChaineColonnes(null);
    			}
    		} else {
    			$saved_search->setModeColonnes(null);
    		}

    		$saved_search->setChaineEditables($editables);

    		$rsMgr->save($saved_search, true);

    		return $this->render('ZombieBundle:Recherche:save_search_success.html.twig');
    	}
    	
    	//Search Modules
    	$authChecker = SymfonyUtils::getAuthorizationChecker();
    	$ModuleMetrics = array();
    	$LogoModuleMetrics = array();
    	$SearchSemantique= array();
    	
    	$modulesMgr = $this->container->get('zombie_modules_manager');
    	if (false) $modulesMgr = new ModulesManager();
    	$modules = $modulesMgr->getZombieModules();
    	if ($modules) {
    		foreach ($modules as $name => $paramsModule) {
    			//METRICS
    			if (isset($paramsModule['metrics_object_class']) && $paramsModule['metrics_object_class']) {
    				
    				//get indicator in object metrics
    				$minmaxMetrics = array();
    				$indicators = $paramsModule['metrics_object_class']::getAllIdIndicators();
    				
    				foreach ($indicators as $indicator => $label ) {
    					$minmaxMetrics[$name.'_'. $indicator] = $label ;
    				}
    				
    				$ModuleMetrics[$name] = $minmaxMetrics;
    				
    				//get logoindicator in object metrics
    				$logoMetrics = array();
    				$indicators = $paramsModule['metrics_object_class']::getAllLogoIndicators();
    				foreach ($indicators as $indicator => $labellogo ) {
    					$logoMetrics[$name.'_'. $indicator] = $labellogo;
    				}
    				$LogoModuleMetrics[$name] = $logoMetrics;
    			}
    			
    			
    			
    			
    			//SEARCH SEMANTIQUE
    			if (isset($paramsModule['search_semantique']) && $paramsModule['search_semantique']) {
    				
    				$SearchSemantique[] = $name;
    			}
    			//Trends
    			if (isset($paramsModule['service_trend']) && $paramsModule['service_trend']) {
    				$ServiceTrend[$name] = $paramsModule['service_trend'];
    			}
    		}
    	}	
    	
    	
    	// get All search of type
    	$list_recherches = $rsMgr->getAllRecherchesSauvegardesForTypeAndIndividu($type, $individu->getId());
    	usort($list_recherches, array('ZombieBundle\Entity\Recherche\RechercheSauvegarde', 'sort_by_name'));

    	return $this->render('ZombieBundle:Recherche:save_search.html.twig', array('type' => $type, 'search' => $search, 'form' => $form->createView(), 'list_recherches' => $list_recherches, 'colonnes' => $colonnes, 'moduleMetrics'=> $ModuleMetrics,'logoModuleMetrics'=> $LogoModuleMetrics,'searchSemantique' => $SearchSemantique, 'serviceTrend' => $ServiceTrend));
    }

    public function loadAction($type) {
    	
    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_recherche')) {
    		throw $this->createAccessDeniedException();
    	}
    	$rsMgr = $this->get('recherches_sauvegardes_manager');
    	if (false) $rsMgr = new RecherchesSauvegardesManager();

    	$securityMgr = $this->get('security_manager');
    	$individu = $securityMgr->getCurrentIndividu();

    	$authChecker = SymfonyUtils::getAuthorizationChecker();

    	$shared_search = $individu->getSharedSearchByType();

    	$datas = array();

    	$datas['type'] = $type;

    	if ($authChecker->isGranted('nav_recherche_article')) {
    		$article_perso = $rsMgr->getAllRecherchesSauvegardesForTypeAndIndividu('article', $individu->getId());
    		usort($article_perso, array('ZombieBundle\Entity\Recherche\RechercheSauvegarde', 'sort_by_name'));

    		$article_fourni = ($shared_search && isset($shared_search['article'])) ? $shared_search['article'] : array();
    		usort($article_fourni, array('ZombieBundle\Entity\Recherche\RechercheSauvegarde', 'sort_by_name'));

    		$datas['article_perso'] = $article_perso;
    		$datas['article_fourni'] = $article_fourni;
    	}


    	return $this->render('ZombieBundle:Recherche:load_search.html.twig', $datas);
    }

    public function configAction($type) {
    	
    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_recherche')) {
    		throw $this->createAccessDeniedException();
    	}
    	$rsMgr = $this->get('recherches_sauvegardes_manager');
    	if (false) $rsMgr = new RecherchesSauvegardesManager();

    	$securityMgr = $this->get('security_manager');
    	$individu = $securityMgr->getCurrentIndividu();

    	$authChecker = SymfonyUtils::getAuthorizationChecker();

    	$shared_search = $individu->getSharedSearchByType();

    	$datas = array();

    	$datas['type'] = $type;

    	if ($authChecker->isGranted('nav_recherche_article')) {
    		
    		$article_perso = $rsMgr->getAllRecherchesSauvegardesForTypeAndIndividu('article', $individu->getId());
    		usort($article_perso, array('ZombieBundle\Entity\Recherche\RechercheSauvegarde', 'sort_by_name'));
    		
    		$article_fourni = ($shared_search && isset($shared_search['article'])) ? $shared_search['article'] : array();
    		usort($article_fourni, array('ZombieBundle\Entity\Recherche\RechercheSauvegarde', 'sort_by_name'));
    		
    		$datas['article_perso'] = $article_perso;
    		$datas['article_fourni'] = $article_fourni;
    	}

    

    	return $this->render('ZombieBundle:Recherche:config_search.html.twig',$datas);
    }

    public function deleteAction($rs_id){
    	
    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_recherche')) {
    		throw $this->createAccessDeniedException();
    	}
    	$rsMgr = $this->get('recherches_sauvegardes_manager');
    	if (false) $rsMgr = new RecherchesSauvegardesManager();

   		$rs = $rsMgr->getRechercheSauvegarde($rs_id);
   		$rs->setDeleted(true);
   		$rs->setDateDelete(new \DateTime());

   		$securityMgr = $this->get('security_manager');
   		$individu = $securityMgr->getCurrentIndividu();

   		if ($individu) {
   			$rs->setIndividuDelete($individu);
   		}

   		$rsMgr->save($rs,true);
   		return $this->render('ZombieBundle:Recherche:delete_search.html.twig',array('rs_id'=>$rs_id));
    }

	public function deleteSharedAction($rs_id) {
		
		if (!$this->get('security.authorization_checker')->isGranted('nav_page_recherche')) {
			throw $this->createAccessDeniedException();
		}
    	$rsMgr = $this->get('recherches_sauvegardes_manager');
    	if (false) $rsMgr = new RecherchesSauvegardesManager();

    	$rs = $rsMgr->getRechercheSauvegarde($rs_id);

    	$securityMgr = $this->get('security_manager');
    	$individu = $securityMgr->getCurrentIndividu();

    	if ($individu) {
    		$rs->removeSharedWith($individu);
    	}

    	$rsMgr->save($rs,true);
    	return $this->render('ZombieBundle:Recherche:delete_search.html.twig',array('rs_id'=>$rs_id));
    }

    public function shareAction(Request $request, $rs_id) {
    	
    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_recherche')) {
    		throw $this->createAccessDeniedException();
    	}
    	$rsMgr = $this->get('recherches_sauvegardes_manager');
    	if (false) $rsMgr = new RecherchesSauvegardesManager();

    	$rs = $rsMgr->getRechercheSauvegarde($rs_id);

    	$submit = $request->get('submit');
    	if ($submit) {
    		// save
    		$individus_ids = $request->get('individus');

    		$individus = array();
    		if ($individus_ids) {
    			$individusMgr = $this->get('individus_manager');
    			if (false) $individusMgr = new IndividusManager();

    			$individus = $individusMgr->query(array('ids' => $individus_ids));
    		}

    		$rs->setSharedWith($individus);

    		$rsMgr->save($rs, true);

    		return $this->render('ZombieBundle:Recherche:share_search_success.html.twig',array('rs' => $rs));
    	}

    	$individusByEntite = array();
    	$entitesMap = array();

    	$datas = $rs->getSharedWithByEntite();
    	if ($datas) {
    		foreach ($datas as $entite_id => $data) {
    			$entite = $data['entite'];
    			$entitesMap[$entite_id] = $entite;

    			foreach ($data['individus'] as $individu) {
    				if (!isset($individusByEntite[$entite_id])) $individusByEntite[$entite_id] = array();
    				$individusByEntite[$entite_id][] = $individu;
    			}
    		}
    	}

    	return $this->render('ZombieBundle:Recherche:share_search.html.twig',array('rs' => $rs, 'individusByEntite' => $individusByEntite, 'entitesMap' => $entitesMap));
    }

    public function searchIndivAction(Request $request, $type) {
    	
    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_recherche')) {
    		throw $this->createAccessDeniedException();
    	}

    	$nom = $request->get('nom');
    	$results = array();
    	
    	$individusMgr = $this->get('individus_manager');
    	
    	$individus = $individusMgr->getIndividuForSearch($nom);
    	foreach ($individus as $ind) {
    		$results[] = array('entite' => $ind->getMainEntity(), 'individu' => $ind);
    	}

    	
    	return $this->render('ZombieBundle:Recherche:search_indiv.html.twig', array('results' => $results));
    }
 
}
