<?php

namespace Seriel\DandelionBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use Seriel\DandelionBundle\Entity\DandelionEntity;

class DandelionEntitiesManager extends SerielManager
{
	public function getObjectClass() {
		return 'Seriel\DandelionBundle\Entity\DandelionEntity';
	}
	
	protected function addSecurityFilters($qb, $individu) {
		// NO SECURITY HERE.
		return;
	}
	
	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;
		
		$alias = $this->getAlias();
		
		if (isset($params['dandelion_id']) && $params['dandelion_id']) {
			if ($this->addWhereId($qb, $alias.".dandelion_id", $params['dandelion_id'])) {
				$hasParams = true;
			}
		}
	
		return $hasParams;
	}
	
	/**
	 * @return DandelionEntity
	 */
	public function getDandelionEntity($id) {
		return $this->get($id);
	}
	
	/**
	 * @return DandelionEntity
	 */
	public function getDandelionEntityForDandelionId($dandelion_id) {
		if (!$dandelion_id) return null;
		if (is_array($dandelion_id)) return null;
		
		return $this->query(array('dandelion_id' => $dandelion_id), array('one' => true));
	}
	
	/**
	 * @return DandelionEntity[]
	 */
	public function getAllDandelionEntities($options = array()) {
		return $this->getAll($options);
	}
	
	/**
	 * @return int
	 */
	public function updateAllQuantityDandelionEntity() {

		$conn = $this->getDoctrineEM()->getConnection();
		
		return $conn->executeQuery('update dandelion_entity as tup left join (	select en.id, count(link.dandelion_entity_id) as quantity from dandelion_entity as en inner join dandelion_article_entity_link as link on link.dandelion_entity_id = en.id group by en.id 	) sub on sub.id = tup.id set tup.quantity = sub.quantity', array());
		
	}
	
	
}

?>
