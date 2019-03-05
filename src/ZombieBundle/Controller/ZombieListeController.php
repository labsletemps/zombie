<?php

namespace ZombieBundle\Controller;

use Seriel\AppliToolboxBundle\Controller\ListeController;
use ZombieBundle\Managers\Liste\ZombieListeManager;

class ZombieListeController extends ListeController {
	
	protected function dealWithRefreshInfos($type, $refresh_infos, $search_params = null, $search_options = null, $get_total_qte = false) {

		if ($search_params !== null) {
			// OK, let's deal with that.
	
			$listeMgr = $this->container->get('liste_manager');
			if (false) $listeMgr = new ZombieListeManager();
			
			$res = $listeMgr->getElemsForListSearch($type, $search_params, $search_options, $refresh_infos);
			
			return array('elems' => $res);
		}
		
		$title = $refresh_infos['title'];
		
    	if ($title) {
 
   		}
		return null;
	}
	
}