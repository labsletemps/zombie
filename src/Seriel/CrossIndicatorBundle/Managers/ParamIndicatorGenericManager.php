<?php

namespace Seriel\CrossIndicatorBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;

class ParamIndicatorGenericManager extends SerielManager
{
	public function getObjectClass() {
		return 'Seriel\CrossIndicatorBundle\Entity\ParamIndicatorGeneric';
	}
	
	protected function addSecurityFilters($qb, $individu) {
		// NO SECURITY HERE.
		return;
	}
	
	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;
		
		$alias = $this->getAlias();
		
		return $hasParams;
	}
	
	/**
	 * @return ParamIndicatorGeneric
	 */
	public function getParamIndicatorGeneric($id) {
		return $this->get($id);
	}
	

	/**
	 * @return ParamIndicatorGeneric[]
	 */
	public function getAllParamIndicatorGeneric($options = array()) {
		return $this->getAll($options);
	}
	
	/**
	 * @return ParamIndicatorGeneric
	 */
	public function getParamIndicatorGenericDatabase() {
		return $this->query(array(), array('one' => true));
	}
}

?>
