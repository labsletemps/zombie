<?php

namespace Seriel\DandelionBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;


class DandelionEntitiesTypeManager extends SerielManager
{
	public function getObjectClass() {
		return 'Seriel\DandelionBundle\Entity\DandelionEntityType';
	}

	protected function addSecurityFilters($qb, $individu) {
		// NO SECURITY HERE.
		return;
	}

	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;

		$alias = $this->getAlias();

		if (isset($params['dbpediaId']) && $params['dbpediaId']) {
			$qb->andWhere($alias.'.dbpediaId = :dbpediaid')->setParameter('dbpediaid', $params['dbpediaId']);
			$hasParams = true;
		}

		return $hasParams;
	}

	/**
	 * @return DandelionEntityType
	 */
	public function getDandelionEntity($id) {
		return $this->get($id);
	}

	/**
	 * @return DandelionEntityType
	 */
	public function getDandelionEntityTypeForDbpediaId($dbpedia_id) {
		if (!$dbpedia_id) return null;
		if (is_array($dbpedia_id)) return null;

		return $this->query(array('dbpediaId' => $dbpedia_id), array('one' => true));
	}

	/**
	 * @return DandelionEntityType[]
	 */
	public function getAllDandelionEntitiesType($options = array()) {
		return $this->getAll($options);
	}

}

?>
