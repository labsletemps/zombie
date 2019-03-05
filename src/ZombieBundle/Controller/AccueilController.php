<?php

namespace ZombieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ZombieBundle\Managers\Securite\SecurityManager;
use ZombieBundle\Managers\Recherche\RecherchesSauvegardesManager;
use ZombieBundle\Managers\Reporting\ReportingsSauvegardesManager;
use ZombieBundle\Managers\Gui\InterfaceParamsManager;
use ZombieBundle\Entity\Gui\InterfaceParam;
use ZombieBundle\Entity\Recherche\RechercheSauvegarde;

class AccueilController extends Controller {
	
	// Main page of application
	public function indexAction() {
		if (!$this->get('security.authorization_checker')->isGranted('nav_page_accueil')) {
			throw $this->createAccessDeniedException();
		}
    	$vars = array();
    	
    	$securityMgr = $this->get('security_manager');
    	if (false) $securityMgr = new SecurityManager();
    	
    	$ipMgr = $this->get('interface_params_manager');
    	if (false) $ipMgr = new InterfaceParamsManager();
    	
    	$rsMgr = $this->get('recherches_sauvegardes_manager');
    	if (false) $rsMgr = new RecherchesSauvegardesManager();
    	
    	$reportSaveMgr = $this->get('reportings_sauvegardes_manager');
    	if (false) $reportSaveMgr = new ReportingsSauvegardesManager();
    	
    	$interface_params = null;
    	
    	$rsById = array();
    	$reportSaveById = array();
    	
    	// get all interface element of the user
    	$currentIndividu = $securityMgr->getCurrentIndividu();
    	if ($currentIndividu) { // Should always be true, otherwise we shouldn't be here.
    		$profils = $currentIndividu->getProfils();
    		if ($profils) {
    			// TODO : order by priority.
    			foreach ($profils as $individu_profil) {
    				$profil = $individu_profil->getProfil();
    				$params = $ipMgr->getAllInterfaceParamsForTypeAndProfil(InterfaceParam::INTERFACE_TYPE_PAGE_ACCUEIL, $profil->getId());
    				
    				if ($params && count($params) > 0) {
    					$interface_params = $params;
    					break;
    				}
    			}
    		}
    	}
    	
    	$menu_left = array();
    	$menu_right = array();
    	
    	if ($interface_params) {
    		// Create a left menu and right menu
    		foreach ($interface_params as $ip) {
    			if (false) $ip = new InterfaceParam();
    			$pos_in_page = $ip->getOption1();
    			$pos = $ip->getPos();
    			
    			if ($pos_in_page == 'left') {
    				$menu_left[$pos] = $ip;
    			} else if ($pos_in_page == 'right') {
    				$menu_right[$pos] = $ip;
    			}
    			
    			$type = $ip->getOption2();
    			if ($type == 'list') {
    				$rs_id = intval($ip->getOption4());
    				
    				$rs = $rsMgr->getRechercheSauvegarde($rs_id);
    				if ($rs) {
    					$rsById[$rs->getId()] = $rs;
    				}
    			} else if ($type == 'report') {
    				$rs_id = intval($ip->getOption4());
    				$rs = $reportSaveMgr->getReportingSauvegarde($rs_id);
    				if ($rs) {
    					$reportSaveById[$rs->getId()] = $rs;
    				}
    			}
    		}
    	}
    	$vars['reportSaveById'] = $reportSaveById;
    	
    	$listesByRsId = array();

    	foreach ($rsById as $id => $rs) {
    		if (false) $rs = new RechercheSauvegarde();
    		$entiteClassName = $rs->getEntiteClassName();
    		$options = array('limit' => 50, 'result_type' => 'search_object', 'force_empty' => true);
    		$datas = $rs->executeSearch($options);
    		
    		$tmp_datas = array('type' => $entiteClassName, 'elems' => $datas);
    		if ($rs->getChaineColonnes()) $tmp_datas['columns'] = $rs->getChaineColonnes();
    		if ($rs->getChaineEditables()) $tmp_datas['filters'] = $rs->getChaineEditables();
    		$listesByRsId[$rs->getId()] = $tmp_datas;
    		
    	}
    	$vars['listesByRsId'] = $listesByRsId;
    	
    	if ($interface_params) {
    		$vars['menu_left'] = $menu_left;
    		$vars['menu_right'] = $menu_right;
    		
    		$vars['menu_left_empty'] = count($menu_left) == 0;
    		$vars['menu_right_empty'] = count($menu_right) == 0;
    	} else {
    		$vars['menu_left'] = $menu_left;
    		$vars['menu_right'] = $menu_right;
    		
    		$vars['menu_left_empty'] = false;
    		$vars['menu_right_empty'] = true;
    	}
    	
        return $this->render('ZombieBundle:Accueil:accueil.html.twig', $vars);
    }
}
