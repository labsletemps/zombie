<?php

namespace ZombieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ZombieBundle\Entity\Individu\Utilisateur;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use ZombieBundle\Entity\Individu\IndividuEntite;
use ZombieBundle\Managers\Individu\UtilisateursManager;
use ZombieBundle\Entity\Securite\ZombieCredential;
use ZombieBundle\Entity\Securite\ZombieProfil;
use ZombieBundle\Managers\Securite\ZombieProfilManager;
use ZombieBundle\Entity\Securite\ZombieProfilCredential;
use ZombieBundle\Entity\Securite\IndividuProfil;
use ZombieBundle\API\Entity\StateImport;
use ZombieBundle\Entity\Gui\InterfaceParam;


class AdminController extends Controller
{
	//Main page
    public function indexAction()
    {
    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_admin')) {
    		throw $this->createAccessDeniedException();
    	}
        return $this->render('ZombieBundle:Admin:admin.html.twig', array());
    }
    
    //Information page
    public function societeInformationsAction() {
    	if (!$this->get('security.authorization_checker')->isGranted('nav_admin_info')) {
    		throw $this->createAccessDeniedException();
    	}
    	$societeMgr = $this->get('societe_manager');
    	if (false) $societeMgr = new SocieteManager();
    	
    	$societe = $societeMgr->getSociete();
    	    	    	

    	$datas = array('societe' => $societe
    	);

    	return $this->render('ZombieBundle:Admin:societe_infos.html.twig', $datas);
    }
    
    // page list users
    public function societeIntervenantsAction()
    {
    	if (!$this->get('security.authorization_checker')->isGranted('nav_admin_users')) {
    		throw $this->createAccessDeniedException();
    	}
    	$individusMgr = $this->get('utilisateurs_manager');
    	if (false) $individusMgr= new UtilisateursManager();
    	$individus = $individusMgr->getAllUtilisateurs(array('result_type' => 'search_object', 'orderBy' => array('nom' => 'asc')));
    	
    	return $this->render('ZombieBundle:Admin:individus.html.twig', array('individus' => $individus));
    }
    
    // edit user
    public function editIntervenantsAction($id)
    {
    	if (!$this->get('security.authorization_checker')->isGranted('nav_admin_users')) {
    		throw $this->createAccessDeniedException();
    	}
    	$crea = true;
    	if ($id != null) {
    		$crea = false;
    	}
    	
    	$individuMgr = $this->get('utilisateurs_manager');
    	if (false) $individuMgr= new UtilisateursManager();
    	
    	$profilsMgr = $this->get('zombieprofils_manager');
    	if (false) $profilsMgr = new ZombieProfilManager();
    	
    	$profils = $profilsMgr->getAllZombieProfils();
    	
    	$curr_profil_id = null;
    	
    	if ($crea != true) {
    		$individu= $individuMgr->getUtilisateur($id);
    	} else {
    		$individu= new Utilisateur();
    	}
    	
    	$form = $this->createFormBuilder($individu)
    	->add('nom', TextType::class, array('label' => 'Nom *', 'required' => true))
    	->add('prenom', TextType::class, array('label' => 'Prénom', 'required' => false))
   		->add('email', TextType::class, array('label' => 'Email *', 'required' => true))
    	->add('submit', SubmitType::class)
    	->getForm();
    	
    	
    	if ($crea != true) {
    		$user = $individu->getUser();
    		if ($user) {
    			$actif = $user->isEnabled();
    			$profilPrincipal=$individu->getProfilPrincipal();
    			if (isset($profilPrincipal)) $curr_profil_id = $profilPrincipal->getId();
    			
    		}
    		
    	}
    	
    	if ($crea !== true && isset($user)) {
    		return $this->render('ZombieBundle:Admin:individus_add.html.twig', array('actif' => $actif,'id' => $id, 'profils' => $profils, 'form' => $form->createView(), 'user' => $user,  'curr_profil_id' => $curr_profil_id));
    	}
    	if ($crea !== true && !isset($user)) {
    		return $this->render('ZombieBundle:Admin:individus_add.html.twig', array('id' => $id, 'profils' => $profils, 'form' => $form->createView(),  'curr_profil_id' => $curr_profil_id));
    	}
    	return $this->render('ZombieBundle:Admin:individus_add.html.twig', array( 'profils' => $profils , 'form' => $form->createView(),  'curr_profil_id' => $curr_profil_id));
    }
    
    // stape 2 for edit user
    public function  editedIntervenantsAction(Request $request, $id)
    {
    	if (!$this->get('security.authorization_checker')->isGranted('nav_admin_users')) {
    		throw $this->createAccessDeniedException();
    	}
    	// verify if is a create or a modify
    	$crea = true;
    	
    	if ($id != null) {
    		$crea = false;
    	}
    	
    	//create managers
    	$individuMgr = $this->get('utilisateurs_manager');
    	if (false) $individuMgr= new UtilisateursManager();
    	
    	$profilsMgr = $this->get('zombieprofils_manager');
    	if (false) $profilsMgr = new ZombieProfilManager();
    	
    	$profils = $profilsMgr->getAllZombieProfils();
    	
    	// get info fosuser
    	$login = $request->get('login');
    	$pwd = $request->get('pwd');
    	$actif = $request->get('actif');
    	$profil_id = $request->get('profil_id');
    	
    	
    	$individu = null;
    	// if is a create , create new user else get user in database
    	if ($crea == true) {
    		$individu= new Utilisateur();
    		$societeMgr = $this->get('societe_manager');
    		if (false) $societeMgr = new SocieteManager();
    		
    		$societe = $societeMgr->getSociete();
    		$individuEntite = new IndividuEntite();
    		$individuEntite->setEntite($societe);
    		$individuEntite->setIndividu($individu);
    		$individu->addEntite($individuEntite);
    	} else {
    		$individu= $individuMgr->getUtilisateur($id);
    	}

    	// create form and associate data.
    	$form = $this->createFormBuilder($individu)
    	->add('nom', TextType::class, array('label' => 'Nom *', 'required' => true))
    	->add('prenom', TextType::class, array('label' => 'Prénom', 'required' => false))
    	->add('email', TextType::class, array('label' => 'Email *', 'required' => true))
    	->add('submit', SubmitType::class)
    	->getForm();
    	$form->handleRequest($request);
    	
    	// verify form and return a good template
    	if ($form->isSubmitted() && $form->isValid()) {
    		$individu= $form->getData();
    		
    		if (false) $individu= new Utilisateur();
    		if (!$profil_id) {
    			$individu->setProfil(null);
    		} else {
    			$profil = $profilsMgr->getZombieProfil($profil_id);
    			$individuProfil = new IndividuProfil();
    			$individuProfil->setIndividu($individu);
    			$individuProfil->setProfil($profil);
    			$individu->setProfil($individuProfil);
    		}
    		$save = $individuMgr->save($individu, true);
    		if ($save) {
    			// fosuser
    			$userManager = $this->get('fos_user.user_manager');
    			$user = $individu->getUser();
    			
    			if (!$user) {
    				$user = $userManager->createUser();
    				
    				if ($login && $pwd) {
    					$individu->setUser($user);
    					
    				}
    				
    			}
    			$user->setUsername($login);
    			if ($pwd != 'm01_2_pa553__user') {
    				$user->setPlainPassword($pwd);
    			}
    			$user->setEmail($individu->getEmail());
    			
    			if ((!$login) || (!$pwd)) {
    				$user->setEnabled(false);
    			} else {
    				$user->setEnabled(true);
    			}
    			
    			if ($user) {
    				$user->setEnabled($actif);
    				if ($pwd && $login) {
    					$userManager->updateUser($user,true);
    				}
    				
    			}
    		}
    	
    		
    		if ($crea == true) {
    			return $this->render('ZombieBundle:Admin:create_individu.html.twig', array('individu' => $individu));
    		}
    		return $this->render('ZombieBundle:Admin:update_individu.html.twig', array('individu' => $individu));
    		// if error return form
    	}
    	return $this->render('ZombieBundle:Admin:individus_add.html.twig', array('profils' => $profils ,'form' => $form->createView()));
    	
    }
    // profil page 
    public function societeProfilsListeAction() {
    	if (!$this->get('security.authorization_checker')->isGranted('nav_admin_profils')) {
    		throw $this->createAccessDeniedException();
    	}
    	$profilsZombieMgr = $this->get('zombieprofils_manager');
    	if (false) $profilsZombieMgr= new ZombieProfilManager();
    	
    	$profils = $profilsZombieMgr->getAllZombieProfils(array('result_type' => 'search_object'));
    	
    	return $this->render('ZombieBundle:Admin:profilsSocieteListe.html.twig', array('profils' => $profils));
    }

    // add profil
    public function societeProfilsNewAction(Request $request) {
     	
    	if (!$this->get('security.authorization_checker')->isGranted('nav_admin_profils')) {
    		throw $this->createAccessDeniedException();
    	}
    	
    	$societeMgr = $this->get('societe_manager');
    	if (false) $societeMgr = new SocieteManager();
    	
    	$credsMgr = $this->get('credentials_manager');
    	if (false) $credsMgr = new CredentialsManager();
    	
    	$profilsMgr = $this->get('zombieprofils_manager');
    	if (false) $profilsMgr = new ZombieProfilManager();
    	
    	$societe = $societeMgr->getSociete();
    	// get credentials.
    	$credentialsByGroup = $credsMgr->getAllCredentialsByGroup();
    	$credentialsByEntity = $credsMgr->getAllCredentialsByEntity();
    	
    	$profil = new ZombieProfil();
    	
    	$form = $this->createFormBuilder($profil)
    	->add('nom', TextType::class, array('label' => 'Nom *', 'required' => true))
    	->add('profil_type', ChoiceType::class, array('label' => 'Type', 'choices' => array('Interne' => ZombieProfil::PROFIL_TYPE_REDACTEUR , 'Externe' => ZombieProfil::PROFIL_TYPE_EXTERIEUR )))
    	->add('submit', SubmitType::class)
    	->getForm();
    	
    	$form->handleRequest($request);
    	
    	// verify form and return a good template
    	if ($form->isSubmitted()) {
 
    		$creds = $request->get('creds');
    		
    		$creds_ids = array();
    		$creds_level_by_id = array();
    		$creds_choice_by_id = array();
    		
    		if (!$creds) $creds = array();
    		foreach ($creds as $cred) {
    			$cred_id = $cred['id'];
    			$creds_ids[$cred_id] = $cred_id;
    			if (isset($cred['level']) && $cred['level']) {
    				$level = $cred['level'];
    				$creds_level_by_id[$cred_id] = $level;
    			}
    			if (isset($cred['choice']) && $cred['choice']) {
    				$choice = $cred['choice'];
    				$creds_choice_by_id[$cred_id] = $choice;
    			}
    		}
    		
    		// get credentials.
    		$credentials = $creds_ids ? $credsMgr->getAllCredentialsForListIds(array_keys($creds_ids)) : array();
    		
    		$credentials_by_id = array();
    		foreach ($credentials as $cred) {
    			$credentials_by_id[$cred->getId()] = $cred;
    		}
    		
    		// clean the list.
    		$real_creds_ids = array();
    		foreach ($creds_ids as $cred_id) {
    			if (!isset($credentials_by_id[$cred_id])) continue;
    			
    			$credential = $credentials_by_id[$cred_id];
    			if (false) $credential = new ZombieCredential();
    			
    			$parent_cred = $credential->getParentCredential();
    			while ($parent_cred != null) {
    				if (!isset($creds_ids[$parent_cred->getId()])) {
    					break; // not add.
    				}
    				
    				$parent_cred = $parent_cred->getParentCredential();
    			}
    			if ($parent_cred == null) {
    				// if is a trunk credential or have all parents credentials of credential.
    				$real_creds_ids[$cred_id] = $cred_id;
    			}
    		}
    		
    		$real_creds_level_by_id = array();
    		$real_creds_choice_by_id = array();
    		foreach ($real_creds_ids as $cred_id) {
    			if (isset($creds_level_by_id[$cred_id])) $real_creds_level_by_id[$cred_id] = $creds_level_by_id[$cred_id];
    			if (isset($creds_choice_by_id[$cred_id])) $real_creds_choice_by_id[$cred_id] = $creds_choice_by_id[$cred_id];
    		}
    		
    		$profil->removeAllProfilsCredentials();
    		
    		$credsMultiChoiceMgr = $this->get('cred_multi_choice_manager');
    		if (false) $credsMultiChoiceMgr = new CredentialMultiChoiceManager();
    		
    		foreach ($real_creds_ids as $cred_id) {
    			$profilCred = new ZombieProfilCredential();
    			
    			$credential = $credentials_by_id[$cred_id];
    			if (false) $credential = new ZombieCredential();
    			
    			$profilCred->setCredential($credential);
    			
    			if ($credential->hasLevel()) {
    				$level = ZombieCredential::ACCESS_LEVEL_SELF;
    				if (isset($real_creds_level_by_id[$cred_id])) $level = $real_creds_level_by_id[$cred_id];
    				$profilCred->setAccessLevel($level);
    			}
    			if ($credential->hasChoices()) {
    				$choice = null;
    				$choice_id = null;
    				if (isset($real_creds_choice_by_id[$cred_id])) $choice_id = $real_creds_choice_by_id[$cred_id];
    				if ($choice_id) {
    					$choice = $credsMultiChoiceMgr->getCredentialMultiChoice($choice_id);
    				}
    				
    				$profilCred->setChoice($choice);
    			}
    			
    			$profil->addProfilsCredential($profilCred);
    		}
    		
    		$profilsMgr->save($profil, true);
    		return $this->render('ZombieBundle:Admin:profilsSocieteNew_success.html.twig', array('societe' => $societe, 'profil' => $profil));
    	}
    	
    	return $this->render('ZombieBundle:Admin:profilsSocieteNew.html.twig', array('societe' => $societe, 'form' => $form->createView(), 'credentialsByGroup' => $credentialsByGroup, 'credentialsByEntity' => $credentialsByEntity));
    }
    
    // edit profil
    public function societeProfilsEditAction(Request $request, $profilId) {

    	if (!$this->get('security.authorization_checker')->isGranted('nav_admin_profils')) {
    		throw $this->createAccessDeniedException();
    	}
    	
    	$credsMgr = $this->get('credentials_manager');
    	if (false) $credsMgr = new CredentialsManager();
    	
    	$profilsMgr = $this->get('zombieprofils_manager');
    	if (false) $profilsMgr = new ZombieProfilManager();
    	
    	$profil = $profilsMgr->getZombieProfil($profilId);
    	if (false) $pofil = new ZombieProfil();
    	
    	// Get credentials.
    	$credentialsByGroup = $credsMgr->getAllCredentialsByGroup();
    	$credentialsByEntity = $credsMgr->getAllCredentialsByEntity();
    	
    	$form = $this->createFormBuilder($profil)
    	->add('nom', TextType::class, array('label' => 'Nom *', 'required' => true))
    	->add('profil_type', ChoiceType::class, array('label' => 'Type', 'choices' => array('Interne' => ZombieProfil::PROFIL_TYPE_REDACTEUR , 'Externe' => ZombieProfil::PROFIL_TYPE_EXTERIEUR )))
    	->add('submit', SubmitType::class)
    	->getForm();
    	
    	$form->handleRequest($request);
    	
    	// verify form and return a good template
    	if ($form->isSubmitted()) {
    		$creds = $request->get('creds');
    		
    		$creds_ids = array();
    		$creds_level_by_id = array();
    		$creds_choice_by_id = array();
    		foreach ($creds as $cred) {
    			$cred_id = $cred['id'];
    			$creds_ids[$cred_id] = $cred_id;
    			if (isset($cred['level']) && $cred['level']) {
    				$level = $cred['level'];
    				$creds_level_by_id[$cred_id] = $level;
    			}
    			if (isset($cred['choice']) && $cred['choice']) {
    				$choice = $cred['choice'];
    				$creds_choice_by_id[$cred_id] = $choice;
    			}
    		}
    		
    		// get credentials.
    		$credentials = $creds_ids ? $credsMgr->getAllCredentialsForListIds(array_keys($creds_ids)) : array();
    		
    		$credentials_by_id = array();
    		foreach ($credentials as $cred) {
    			$credentials_by_id[$cred->getId()] = $cred;
    		}
    		
    		// clean the list.
    		$real_creds_ids = array();
    		foreach ($creds_ids as $cred_id) {
    			if (!isset($credentials_by_id[$cred_id])) continue;
    			
    			$credential = $credentials_by_id[$cred_id];
    			if (false) $credential = new ZombieCredential();
    			
    			$parent_cred = $credential->getParentCredential();
    			while ($parent_cred != null) {
    				if (!isset($creds_ids[$parent_cred->getId()])) {
    					break; // not add.
    				}
    				
    				$parent_cred = $parent_cred->getParentCredential();
    			}
    			if ($parent_cred == null) {
    				// if is a trunk credential or have all parents credentials of credential.
    				$real_creds_ids[$cred_id] = $cred_id;
    			}
    		}
    		
    		$real_creds_level_by_id = array();
    		$real_creds_choice_by_id = array();
    		foreach ($real_creds_ids as $cred_id) {
    			if (isset($creds_level_by_id[$cred_id])) $real_creds_level_by_id[$cred_id] = $creds_level_by_id[$cred_id];
    			if (isset($creds_choice_by_id[$cred_id])) $real_creds_choice_by_id[$cred_id] = $creds_choice_by_id[$cred_id];
    		}
    		
    		$profil->removeAllProfilsCredentials();
    		
    		$credsMultiChoiceMgr = $this->get('cred_multi_choice_manager');
    		if (false) $credsMultiChoiceMgr = new CredentialMultiChoiceManager();
    		
    		foreach ($real_creds_ids as $cred_id) {
    			$profilCred = new ZombieProfilCredential();
    			
    			$credential = $credentials_by_id[$cred_id];
    			if (false) $credential = new ZombieCredential();
    			
    			$profilCred->setCredential($credential);
    			
    			if ($credential->hasLevel()) {
    				$level = ZombieCredential::ACCESS_LEVEL_SELF;
    				if (isset($real_creds_level_by_id[$cred_id])) $level = $real_creds_level_by_id[$cred_id];
    				$profilCred->setAccessLevel($level);
    			}
    			
    			if ($credential->hasChoices()) {
    				$choice = null;
    				$choice_id = null;
    				if (isset($real_creds_choice_by_id[$cred_id])) $choice_id = $real_creds_choice_by_id[$cred_id];
    				if ($choice_id) {
    					$choice = $credsMultiChoiceMgr->getCredentialMultiChoice($choice_id);
    				}
    				
    				$profilCred->setChoice($choice);
    			}
    			
    			$profil->addProfilsCredential($profilCred);
    		}
    		
    		$profilsMgr->save($profil, true);
    		
    		return $this->render('ZombieBundle:Admin:profilsSocieteEdit_success.html.twig', array('profil' => $profil));
    	}
    	
    	// Let's build a map with all profCreds
    	$profCreds = $profil->getProfilsCredentials();
    	$profCredsByCredId = array();
    	if ($profCreds) {
    		foreach ($profCreds as $profCred) {
    			if (false) $profCred = new ProfilCredential();
    			
    			$profCredsByCredId[$profCred->getCredential()->getId()] = $profCred;
    		}
    	}
    	
    	return $this->render('ZombieBundle:Admin:profilsSocieteEdit.html.twig', array('profil' => $profil, 'form' => $form->createView(), 'credentialsByGroup' => $credentialsByGroup, 'credentialsByEntity' => $credentialsByEntity, 'profCredsByCredId' => $profCredsByCredId));
    }
    
    
    public function profilsAccueilAction(Request $request, $profilId)
    {
    	
    	$profilsMgr = $this->get('zombieprofils_manager');
    	if (false) $profilsMgr = new ZombieProfilManager();
    	
    	$rsMgr = $this->get('recherches_sauvegardes_manager');
    	if (false) $rsMgr = new RecherchesSauvegardesManager();
    	
    	$reportSaveMgr = $this->get('reportings_sauvegardes_manager');
    	if (false) $reportSaveMgr = new ReportingsSauvegardesManager();
    	
    	$ipMgr = $this->get('interface_params_manager');
    	if (false) $ipMgr = new InterfaceParamsManager();
    	
    	$securityMgr = $this->get('security_manager');
    	$individu = $securityMgr->getCurrentIndividu();
    	
    	$profil = $profilsMgr->getZombieProfil($profilId);
    	if (false) $pofil = new ZombieProfil();
    	
    	$datas = array('profil' => $profil);
    	
    	if ($request->get('submit')) {
    		// On est en sauvegarde.
    		$left = $request->get('left');
    		$right = $request->get('right');
    		
    		$interfaces_params = array();
    		
    		if ($left) {
    			for ($i = 0; $i < count($left); $i++) {
    				$elem = $left[$i];
    				$type = $elem['type'];
    				
    				if ($type == 'group') {
    					
    					$ip = new InterfaceParam();
    					$ip->setType(InterfaceParam::INTERFACE_TYPE_PAGE_ACCUEIL);
    					$ip->setPos($i);
    					$ip->setProfil($profil);
    					$ip->setOption1('left');
    					$ip->setOption2('group');
    					$ip->setOption3($elem['nom']);
    					
    					$icon = isset($elem['icon']) ? $elem['icon'] : null;
    					if ($icon) $ip->setOption4($icon);
    					
    					$interfaces_params[] = $ip;
    					
    				} else if ($type == 'list') {
    					
    					$ip = new InterfaceParam();
    					$ip->setType(InterfaceParam::INTERFACE_TYPE_PAGE_ACCUEIL);
    					$ip->setPos($i);
    					$ip->setProfil($profil);
    					$ip->setOption1('left');
    					$ip->setOption2('list');
    					
    					// On récupère la liste.
    					$rs_id = intval($elem['rs_id']);
    					$rs = $rsMgr->getRechercheSauvegarde($rs_id);
    					
    					if ($rs) $ip->setOption3($rs->getNom());
    					
    					$ip->setOption4('' . $rs_id);
    					
    					$interfaces_params[] = $ip;
    					
    				} else if ($type == 'report') {
    					
    					$ip = new InterfaceParam();
    					$ip->setType(InterfaceParam::INTERFACE_TYPE_PAGE_ACCUEIL);
    					$ip->setPos($i);
    					$ip->setProfil($profil);
    					$ip->setOption1('left');
    					$ip->setOption2('report');
    					
    					// On récupère le report.
    					$rs_id = intval($elem['rs_id']);
    					$rs = $reportSaveMgr->getReportingSauvegarde($rs_id);
    					
    					if ($rs) $ip->setOption3($rs->getNom());
    					
    					$ip->setOption4('' . $rs_id);
    					
    					$interfaces_params[] = $ip;
    				}
    			}
    		}
    		
    		if ($right) {
    			for ($i = 0; $i < count($right); $i++) {
    				$elem = $right[$i];
    				$type = $elem['type'];
    				
    				if ($type == 'group') {
    					
    					$ip = new InterfaceParam();
    					$ip->setType(InterfaceParam::INTERFACE_TYPE_PAGE_ACCUEIL);
    					$ip->setPos($i);
    					$ip->setProfil($profil);
    					$ip->setOption1('right');
    					$ip->setOption2('group');
    					$ip->setOption3($elem['nom']);
    					
    					$icon = isset($elem['icon']) ? $elem['icon'] : null;
    					if ($icon) $ip->setOption4($icon);
    					
    					$interfaces_params[] = $ip;
    					
    				} else if ($type == 'list') {
    					
    					$ip = new InterfaceParam();
    					$ip->setType(InterfaceParam::INTERFACE_TYPE_PAGE_ACCUEIL);
    					$ip->setPos($i);
    					$ip->setProfil($profil);
    					$ip->setOption1('right');
    					$ip->setOption2('list');
    					
    					// On récupère la liste.
    					$rs_id = intval($elem['rs_id']);
    					$rs = $rsMgr->getRechercheSauvegarde($rs_id);
    					
    					if ($rs) $ip->setOption3($rs->getNom());
    					
    					$ip->setOption4('' . $rs_id);
    					
    					$interfaces_params[] = $ip;
    				} else if ($type == 'report') {
    					
    					$ip = new InterfaceParam();
    					$ip->setType(InterfaceParam::INTERFACE_TYPE_PAGE_ACCUEIL);
    					$ip->setPos($i);
    					$ip->setProfil($profil);
    					$ip->setOption1('right');
    					$ip->setOption2('report');
    					
    					// On récupère le report.
    					$rs_id = intval($elem['rs_id']);
    					$rs = $reportSaveMgr->getReportingSauvegarde($rs_id);
    					
    					if ($rs) $ip->setOption3($rs->getNom());
    					
    					$ip->setOption4('' . $rs_id);
    					
    					$interfaces_params[] = $ip;
    				}
    			}
    		}
    		
    		// OK, on récupère tous les éléments de paramètres page d'accueil pour ce profil et on les supprime.
    		// On sauvegarde ensuite les nouveau paramètres.
    		
    		$oldParams = $ipMgr->getAllInterfaceParamsForTypeAndProfil(InterfaceParam::INTERFACE_TYPE_PAGE_ACCUEIL, $profil->getId());
    		if ($oldParams) {
    			foreach ($oldParams as $oldP) {
    				// On ne flush qu'à la fin.
    				$ipMgr->remove($oldP, false);
    			}
    		}
    		
    		// On sauvegarde desormais les nouveaux elements.
    		foreach ($interfaces_params as $ip) {
    			$ipMgr->save($ip, false);
    		}
    		
    		$ipMgr->getDoctrineEM()->flush();
    		
    		return $this->render('ZombieBundle:Admin:profilsAccueilSuccess.html.twig');
    	}
    	
    	$article_perso = $rsMgr->getAllRecherchesSauvegardesForTypeAndIndividu('article', $individu->getId());
    	usort($article_perso, array('ZombieBundle\Entity\Recherche\RechercheSauvegarde', 'sort_by_name'));
    	$datas['article_search'] = $article_perso;

    	
    	$article_report = $reportSaveMgr->getAllReportingsSauvegardesForTypeAndIndividu('article', $individu->getId());
    	usort($article_report, array('ZombieBundle\Entity\Reporting\ReportingSauvegarde', 'sort_by_name'));
    	$datas['article_report'] = $article_report;
    	
    	// On récupère tous les éléments actuels du profil.
    	$params = $ipMgr->getAllInterfaceParamsForTypeAndProfil(InterfaceParam::INTERFACE_TYPE_PAGE_ACCUEIL, $profil->getId());
    	
    	// On crée un menu de gauche et un menu de droite.
    	$menu_left = array();
    	$menu_right = array();
    	$used_rs_ids = array();
    	$used_reports_ids = array();
    	foreach ($params as $ip) {
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
    				//$rsById[$rs->getId()] = $rs;
    				$used_rs_ids[$rs->getId()] = $rs->getId();
    			}
    		} else if ($type == 'report') {
    			$report_id = intval($ip->getOption4());
    			$report =  $reportSaveMgr->getReportingSauvegarde($report_id);
    			if ($report) {
    				//$rsById[$rs->getId()] = $rs;
    				$used_reports_ids[$report->getId()] = $report->getId();
    			}
    		}
    	}
    	
    	$datas['menu_left'] = $menu_left;
    	$datas['menu_right'] = $menu_right;
    	$datas['used_rs_ids'] = $used_rs_ids;
    	$datas['used_reports_ids'] = $used_reports_ids;
    	
    	return $this->render('ZombieBundle:Admin:profilsAccueil.html.twig', $datas);
    }
    
    // Check list import data Page
    public function etatDonneesAction() {
    	
    	
    	if (!$this->get('security.authorization_checker')->isGranted('nav_admin_donnees')) {
    		throw $this->createAccessDeniedException();
    	}

    	$articlesManager = $this->get("articles_manager");
    	if (false) $articlesManager = new ArticlesManager();
    	
    	$lastImportArticle =  $articlesManager->getLastImport();
    	if (isset($lastImportArticle)) {
    		$StateImportArticle = array(New StateImport('Dernière Importation', $lastImportArticle));
    	} else {
    		$StateImportArticle = array();
    	}
    	$StateImportMetrics = array();
    	$StateImportSemantiques = array();
    	$StateImportTrends = array();
    	
    	//Search Modules
    	$modulesMgr = $this->container->get('zombie_modules_manager');
    	if (false) $modulesMgr = new ModulesManager();
    	$modules = $modulesMgr->getZombieModules();
    	if ($modules) {
    		foreach ($modules as $name => $paramsModule) {
    			//METRICS
    			if (isset($paramsModule['StateMetrics']) && $paramsModule['StateMetrics']) {
    				$mgr = $this->get($paramsModule['StateMetrics']);
    				$StateImportMetrics = array_merge($StateImportMetrics, $mgr->getStateImports());

    			}
    			//SEMANTIQUE
    			if (isset($paramsModule['StateSemantique']) && $paramsModule['StateSemantique']) {
    				$mgr = $this->get($paramsModule['StateSemantique']);
    				$StateImportSemantiques= array_merge($StateImportSemantiques, $mgr->getStateImports());
    				
    			}
    			//TREND
    			if (isset($paramsModule['StateTrends']) && $paramsModule['StateTrends']) {
    				$mgr = $this->get($paramsModule['StateTrends']);
    				$StateImportTrends= array_merge($StateImportTrends, $mgr->getStateImports());
    				
    			}
    		}
    	}	
    	$datas = array(
    			'article' => $StateImportArticle,
    			'metrics' => $StateImportMetrics,
    			'semantiques' => $StateImportSemantiques,
    			'trends' => $StateImportTrends,
    	);
    	
    	return $this->render('ZombieBundle:Admin:etat_donnees.html.twig', $datas);
    }
}
