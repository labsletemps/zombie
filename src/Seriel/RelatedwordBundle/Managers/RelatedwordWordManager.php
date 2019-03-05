<?php

namespace Seriel\RelatedwordBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use Seriel\RelatedwordBundle\Entity\Word;

class RelatedwordWordManager extends SerielManager
{
	public function getObjectClass() {
		return 'Seriel\RelatedwordBundle\Entity\Word';
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
		if ($name == null) return null;
		if (is_array($name)) return null;

		return $this->query(array('name' => $name), array('one' => true));
	}

	/**
	 * @return word[]
	 */
	public function getAllWord($options = array()) {
		return $this->getAll($options);
	}

	/**
	 * @return integer
	 */
	public function generateAllWordQuantity() {
		$conn = $this->getDoctrineEM()->getConnection();
		return $conn->executeQuery('UPDATE rw_word SET rw_word.quantity=(SELECT SUM(rw_article_word.quantity) FROM rw_article_word where rw_article_word.word_id  = rw_word.id GROUP BY rw_article_word.word_id)', array());
	}

	/**
	 * @return integer
	 */
	public function generateAllWordQuantityByWord($arrayIdword) {
		if (!$arrayIdword) return null;
		if (!is_array($arrayIdword)) return null;
		$arrayIdwordString = implode(",", $arrayIdword);
		$conn = $this->getDoctrineEM()->getConnection();
		return $conn->executeQuery('UPDATE rw_word SET rw_word.quantity=(SELECT SUM(rw_article_word.quantity) FROM rw_article_word where rw_article_word.word_id  = rw_word.id GROUP BY rw_article_word.word_id) WHERE rw_word.id in ('.$arrayIdwordString.')', array());
	}

}

?>
