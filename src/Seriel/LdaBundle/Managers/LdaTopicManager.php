<?php

namespace Seriel\LdaBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use Seriel\LdaBundle\Entity\Topic;

class LdaTopicManager extends SerielManager
{
	public function getObjectClass() {
		return 'Seriel\LdaBundle\Entity\Topic';
	}

	protected function addSecurityFilters($qb, $individu) {
		// NO SECURITY HERE.
		return;
	}

	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;

		$alias = $this->getAlias();

		if (isset($params['name']) && $params['name']) {
			$qb->andWhere($alias.'.name = :name')->setParameter('name', $params['name']);
			$hasParams = true;
		}
		if (isset($params['calculateAtLimit']) && $params['calculateAtLimit']) {
			$qb->andWhere($alias.'.calculateAt < :calculateAtLimit')->setParameter('calculateAtLimit', $params['calculateAtLimit']);
			$hasParams = true;
		}

		return $hasParams;
	}

	/**
	 * @return Topic
	 */
	public function getTopic($id) {
		return $this->get($id);
	}

	/**
	 * @return Topic
	 */
	public function getTopicByName($name) {
		if (!$name) return null;
		if (is_array($name)) return null;

		return $this->query(array('name' => $name), array('one' => true));
	}

	/**
	 * @return Topic[]
	 */
	public function getAllTopic($options = array()) {
		return $this->getAll($options);
	}

	/**
	 * @return boolean
	 */
	public function getTopicByDateLimit($datelimit) {
		if (!$datelimit) return null;
		if (is_array($datelimit)) return null;

		return $this->query(array('calculateAtLimit' => $datelimit));

	}
}

?>
