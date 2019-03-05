<?php

namespace ZombieBundle\Managers\Liste;

use Seriel\AppliToolboxBundle\Managers\ListeManager;
use Seriel\AppliToolboxBundle\Entity\ListeModele;
use Seriel\AppliToolboxBundle\Managers\ListeModelesManager;
use ZombieBundle\Managers\Individu\IndividusManager;
use ZombieBundle\Entity\Securite\IndividuProfil;

class ZombieListeManager extends ListeManager {
	
	protected function initFormatters() {
		parent::initFormatters();
	}
	
	protected function initSorters() {
		parent::initSorters();
	}
	
	public function getFields($manager, $user, $context = null) {
		$fields = null;
		$fields_user = $manager->getUserFieldsName($user, $context);
		if ($fields_user) {
			$fields = $fields_user;
		} else {
			$entite = null;
			$profil = null;
			$profil_type = null;
			if ($user) {
				$individusMgr = $this->container->get('individus_manager');
				if (false) $individusMgr = new IndividusManager();
				
				$individu = $individusMgr->getIndividuForUserId($user->getId());
				
				if ($individu) {
					$entite = $individu->getMainEntity();
					if ($entite && $entite instanceof Compte) $entite = $entite->getStructure();
					
					$profils = $individu->getProfils();
					
					// Todo : do it better.
					foreach ($profils as $prof_link) {
						if ($prof_link) {
							if (false) $prof_link = new IndividuProfil();
							$prof = $prof_link->getProfil();
							
							if ($prof) {
								$profil = $prof;
								$profil_type = $prof->getProfilType();
								break;
							}
						}
					}					
				}
			}
			
			// Context processing
			if ($context) {
				if (is_string($context)) {
					$context = $this->getListeContextForCode($context);
				} else if (is_int($context)) {
					$context = $this->getListeContextForId($context);
				}
			}

			$listeModelesMgr = $this->container->get('liste_modeles_manager');
			if (false) $listeModelesMgr = new ListeModelesManager();

			$class = $manager->getObjectClass();
			do {
				$listeModel = $listeModelesMgr->getBestListeModele($entite, $class, $profil_type, $profil, $context);
				if ($listeModel) {
					if (false) $listeModel = new ListeModele();
					$fields = $listeModel->getFieldsName();
				}
			} while (($class = get_parent_class($class)) != false);
			
			if (!$fields) $fields = $manager->getDefaultFields();
		}
	
		return $fields;
	}
	
}