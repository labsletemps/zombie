<?php

namespace ZombieBundle\Managers\Params;

use ZombieBundle\Managers\ZombieManager;
use ZombieBundle\Entity\Params\Parameter;

class ParametersManager extends ZombieManager
{
	protected function addSecurityFilters($qb, $individu) {
		// No security here. Required for security !
	}
	protected function addSecurity($qb) {
		// No security here. Required for security !
		return;
	}
	
	public function getObjectClass() {
		return 'ZombieBundle\Entity\Params\Parameter';
	}
	
	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;
		
		$alias = $this->getAlias();

		if (isset($params['nom']) && $params['nom']) {
			$qb->andWhere($alias.'.nom = :nom')->setParameter('nom', $params['nom']);
			$hasParams = true;
		}
		
		return $hasParams;
	}

	/**
	 * @return Parameter
	 */
	public function getParameter($id) {
		return $this->get($id);
	}
	
	/**
	 * @return Parameter
	 */
	public function getParameterForNom($nom) {
		return $this->query(array('nom' => $nom), array('one' => true));
	}
	
	/**
	 * @return Parameter[]
	 */
	public function getAllParameters($options = null) {
		return $this->getAll($options);
	}
	
	public function getValue($nom) {
		if ($nom) {
			$param = $this->getParameterForNom($nom);
			if ($param) {
				return $param->getVal();
			}
		}
		
		return null;
	}
	
	protected function autoDetectType($val) {
		if ($val === null) null;
		if (is_bool($val)) return Parameter::TYPE_BOOLEAN;
		if (is_numeric($val)) {
			if (intval($val) == $val) {
				return Parameter::TYPE_INTEGER;
			} else {
				return Parameter::TYPE_FLOAT;
			}
		}
		if (is_string($val)) {
			return Parameter::TYPE_STRING;
		}
		
		return null;
	}
	
	public function setValue($nom, $val, $type = null) {
		if ($type === null) {
			$type = $this->autoDetectType($val);
			if ($type === null) $type = Parameter::TYPE_STRING;
		}
		
		if ($nom) {
			$param = $this->getParameterForNom($nom);
			if (!$param) {
				$param = new Parameter();
				$param->setNom($nom);
			}
			$param->setType($type);
			
			$param->setVal($val);
			$this->save($param);
		}
	}
}

?>
