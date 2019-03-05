<?php

namespace ZombieBundle\Managers\Modules;

use Seriel\AppliToolboxBundle\Managers\ManagersManager;

class ModulesManager {

	protected $_modules_params = null;
	protected $_modules_widget_params = null;
	protected $_modules_history = null;
	protected $_modules_semantics = null;
	
	protected $container = null;
	protected $logger = null;

	public function __construct($logger, $container) {
		$this->logger = $logger;
		$this->container = $container;
	}

	public function getZombieModules() {
		if ($this->_modules_params === null) {
			$paramsBag = $this->container->getParameterBag();

			$params = $paramsBag->all();

			$modules = array();
			foreach ($params as $key => $value) {
				if (substr($key, 0, 15) == 'zombie.modules.') {
					$mod = substr($key, 15);
					$modules[$mod] = $value;
				}
			}

			$this->_modules_params = $modules;
		}
		return $this->_modules_params;
	}

	public function getArticleHistory($module, $article) {
		if ((!$module) || (!$article)) return null;

		$modules = $this->getZombieModules();
		if (isset($modules[$module])) {
			$module_params = $modules[$module];
			if ($module_params && isset($module_params['service'])) {
				// OK, get service.
				$service = $module_params['service'];
				if ($service) {
					$mgr = ManagersManager::getManager()->getContainer()->get($service);
					if ($mgr && method_exists($mgr, 'articleHistory')) {
						return $mgr->articleHistory($article);
					}
				}
			}
		}

		return null;
	}
	
	public function getArticleHistoryModule() {
		if ($this->_modules_history=== null) {
			$modules = $this->getZombieModules();
			$_modules_history= array();
			foreach ($modules as $name => $module) {
				if ($module && isset($module['service'])) {
					// OK, get service.
					$service = $module['service'];
					if ($service) {
						$mgr = ManagersManager::getManager()->getContainer()->get($service);
						if ($mgr && method_exists($mgr, 'articleHistory')) {
							$_modules_history[] = $name;
						}
					}
				}
			}
			$this->_modules_history = $_modules_history;
		}
		return $this->_modules_history;
	
	}

	public function getArticleSemantics($module, $article) {
		if ((!$module) || (!$article)) return null;

		$modules = $this->getZombieModules();
		if (isset($modules[$module])) {
			$module_params = $modules[$module];
			if ($module_params && isset($module_params['service'])) {
				// OK, get service.
				$service = $module_params['service'];
				if ($service) {
					$mgr = ManagersManager::getManager()->getContainer()->get($service);
					if ($mgr && method_exists($mgr, 'articleSemantics')) {
						return $mgr->articleSemantics($article);
					}
				}
			}
		}

		return null;
	}

	public function getArticleSemanticsModule() {
		if ($this->_modules_semantics=== null) {
			$modules = $this->getZombieModules();
			$_modules_semantics= array();
			foreach ($modules as $name => $module) {
				if ($module && isset($module['service'])) {
					// OK, get service.
					$service = $module['service'];
					if ($service) {
						$mgr = ManagersManager::getManager()->getContainer()->get($service);
						if ($mgr && method_exists($mgr, 'articleSemantics')) {
							$_modules_semantics[] = $name;
						}
					}
				}
			}
			$this->_modules_semantics= $_modules_semantics;
		}
		return $this->_modules_semantics;
		
	}
	public function getZombieModulesWidget() {
		if ($this->_modules_widget_params === null) {
			$paramsBag = $this->container->getParameterBag();
			
			$params = $paramsBag->all();
			
			$modulesWidget= array();
			foreach ($params as $key => $value) {
				if (substr($key, 0, 21) == 'zombie.moduleswidget.') {
					$mod = substr($key, 21);
					$modulesWidget[$mod] = $value;
				}
			}
			
			$this->_modules_widget_params = $modulesWidget;
		}
		return $this->_modules_widget_params ;
	}

}
