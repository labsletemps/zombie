<?php

namespace Seriel\LdaBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use Seriel\LdaBundle\Entity\Word;

class LdaWordManager extends SerielManager
{
	public function getObjectClass() {
		return 'Seriel\LdaBundle\Entity\Word';
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

		return $hasParams;
	}

	/**
	 * @return Word
	 */
	public function getWord($id) {
		return $this->get($id);
	}

	/**
	 * @return Word
	 */
	public function getWordByName($name) {
		if (!$name) return null;
		if (is_array($name)) return null;

		return $this->query(array('name' => $name), array('one' => true));
	}

	/**
	 * @return word[]
	 */
	public function getAllWord($options = array()) {
		return $this->getAll($options);
	}
}

?>
