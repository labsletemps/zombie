<?php

namespace Seriel\DandelionBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use Seriel\DandelionBundle\Entity\DandelionSubject;

class DandelionSubjectManager extends SerielManager
{
	public function getObjectClass() {
		return 'Seriel\DandelionBundle\Entity\DandelionSubject';
	}

	protected function addSecurityFilters($qb, $individu) {
		// NO SECURITY HERE.
		return;
	}

	protected function buildQuery($qb, $params, $options = null) {
		$hasParams = false;

		$alias = $this->getAlias();

		if (isset($params['name']) && $params['name']) {
			$qb->andWhere($alias.'.name = :name')->setParameter('name', $params['name']);
			$hasParams = true;
		}

		return $hasParams;
	}

	/**
	 * @return DandelionSubject
	 */
	public function getDandelionSubject($id) {
		return $this->get($id);
	}

	/**
	 * @return DandelionSubject
	 */
	public function getDandelionSubjectByName($name) {
		if (!$name) return null;
		if (is_array($name)) return null;

		return $this->query(array('name' => $name), array('one' => true));
	}

	/**
	 * @return DandelionSubject[]
	 */
	public function getAllDandelionSubject($options = array()) {
		return $this->getAll($options);
	}

}

?>
