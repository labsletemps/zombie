<?php

namespace ZombieBundle\Managers\Securite;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Seriel\AppliToolboxBundle\Utils\SymfonyUtils;
use ZombieBundle\Managers\Individu\IndividusManager;
use ZombieBundle\Security\Utils\GestionnaireDeDroits;
use ZombieBundle\Entity\Securite\Connexion;

class SecurityManager {
	
	const ACCESS_NO_ACCESS = '0';
	const ACCESS_USER = '1';
	const ACCESS_INVITE = '2';
	
	const SESSION_CREDENTIAL_VAR = '__creds__';
	
	private $_cached_indiv = null;
	
	protected $container = null;
	protected $doctrine = null;
	protected $logger = null;
	protected $templating = null;
	
	public function __construct($container, $doctrine, $templating, $logger) {
		$this->container = $container;
		$this->doctrine = $doctrine;
		$this->templating = $templating;
		$this->logger = $logger;
	}
	
	public function getCurrentUser() {
		//error_log('DEBUG getCurrentUser()');
		if (false) $this->container = new ContainerInterface();
		
		// initialyse User data.
		$currentUser = SymfonyUtils::getTokenUser();
		
		if ($currentUser) {
			$session = $this->getSession();
			
			// get credentials.
			$creds = $session->get(self::SESSION_CREDENTIAL_VAR);
			
			if ($creds === null) {
				$this->initializeCredentialsForUser($currentUser);
			}
		} else {
			$this->clearCredsSession();
		}
		
		return $currentUser;
	}
	
	protected function initializeCredentialsForUser($user) {
		if (!$user) {
			$this->clearCredsSession();
			return;
		}
		if (is_string($user)) {
			error_log("DEBUG USER STRING : $user");
			$this->clearCredsSession();
			return;
		}
		
		// get individu.
		$individu = null;
		$individusMgr = $this->container->get('individus_manager');
		if (false) $individusMgr = new IndividusManager();
		$individu = $individusMgr->getIndividuForUserId($user->getId());
		
		if (!$individu) {
			$this->clearCredsSession();
			return;
		}
		
		$credsMgr = $this->container->get('credentials_manager');
		if (false) $credsMgr = new CredentialsManager();
		
		$credsMap = array();
		
		$gestionnaire_de_droits = new GestionnaireDeDroits($individu);
		
		// For log.
		foreach ($gestionnaire_de_droits->getDebugStrs() as $str) {
			//error_log($str);
			//$this->logger->error($str);
		}
		
		$session = $this->getSession();
		$session->set(self::SESSION_CREDENTIAL_VAR, $gestionnaire_de_droits);
		
		//row connexion
		$connexionsMrg = $this->container->get('connexions_manager');
		if(false){
			$connexionsMrg = new ConnexionsManager();
		}
		$connexion = new Connexion();
		$connexion->setIndividu($individu);
		$connexion->setIndividuNom($individu->getNiceName());
		$connexion->setIndividuProfil($individu->getProfilsStr());
		if ($_SERVER && isset($_SERVER['REMOTE_ADDR'])) $connexion->setIpSource($_SERVER['REMOTE_ADDR']);
		if ($_SERVER && isset($_SERVER['HTTP_USER_AGENT'])) $connexion->setIfosNav($_SERVER['HTTP_USER_AGENT']);
		$date = \DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
		$connexion->setDateConnexion($date);
		
		// get Entite.
		$entite = $individu->getEntitePrincipale();
		if ($entite && $entite instanceof Compte) {
			$connexion->setCompte($entite);
		}
		
		if ($connexion->getIpSource()) $connexionsMrg->save($connexion,true);
	}
	
	public function getCurrentCredentials() {
		$currentUser = $this->getCurrentUser(); 
		if (!$currentUser) {
			$this->clearCredsSession();
			$this->forceDisconnect();
			return null;
		}
		$session = $this->getSession();
		return $session->get(self::SESSION_CREDENTIAL_VAR);
	}
	
	public function getSession() {
		$session = $this->container->get('session');
		if (!$session->isStarted()) $session->start();
		
		return $session;
	}
	
	
	protected function clearCredsSession() {
		$session = $this->getSession();
		
		// remove credentials
		$session->remove(self::SESSION_CREDENTIAL_VAR);
	}
	
	public function getCurrentIndividu() {
		$user = $this->getCurrentUser();
		
		if ($user) {
			
			$individusMgr = $this->container->get('individus_manager');
			if (false) $individusMgr = new IndividusManager();
			$individu = $individusMgr->getIndividuForUserId($user->getId());
			
			if ($individu) return $individu;
		}
	}
	
	public function forceDisconnect() {
		$this->_cached_indiv = null;
		$this->container->get('security.token_storage')->setToken(null);
		$this->clearCredsSession();
	}

}