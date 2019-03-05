<?php

namespace ZombieBundle\Managers\Word;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use ZombieBundle\Entity\Word\StopWord;

class StopWordManager extends SerielManager
{
	protected function addSecurityFilters($qb, $individu) {
		// NO SECURITY HERE.
		return;
	}

	public function getObjectClass() {
		return 'ZombieBundle\Entity\Word\StopWord';
	}

	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;

		$alias = $this->getAlias();

		if (isset($params['language']) && $params['language']) {
			$qb->andWhere($alias.'.language = :language')->setParameter('language', $params['language']);
			$hasParams = true;
		}

		return $hasParams;
	}

	/**
	 * @return StopWord
	 */
	public function getStopWord($id) {
		return $this->get($id);
	}

	/**
	 * @return StopWord[]
	 */
	public function getStopWordByLanguage($language) {
		if (!$language) return null;
		if (is_array($language)) return null;

		return $this->query(array('language' => $language), array());
	}

	/**
	 * @return StopWord[]
	 */
	public function getAllStopWord($options = array()) {
		return $this->getAll($options);
	}

}

?>
