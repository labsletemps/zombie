<?php

namespace ZombieBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\ManagersManager;

class ZombieManagersManager extends ManagersManager
{
	protected $_modules_params = null;
	
	public function getManagerForType($type) {
		
		$index = strrpos($type, "\\");
		if ($index !== false) $type = substr($type, $index+1);
		$type = strtolower($type);
		
		$modulesWidget = $this->getZombieModulesWidget();
		if ($modulesWidget) {
			foreach ($modulesWidget as $name => $params) {
				if (isset($params['service_list']) && $params['service_list']) {				
					if (strtolower($type) == $name) return $this->container->get($params['service_list']);
				}
			}
		}
		return parent::getManagerForType($type);
	}
	
	public function getZombieModules() {
		$modulesMgr = $this->container->get('zombie_modules_manager');
		return $modulesMgr->getZombieModules();
	}

	public function getZombieModulesWidget() {
		$modulesMgr = $this->container->get('zombie_modules_manager');
		return $modulesMgr->getZombieModulesWidget();
	}
}