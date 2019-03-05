<?php

namespace Seriel\DandelionBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;


class DandelionArticleSubjectManager extends SerielManager
{
	public function getObjectClass() {
		return 'Seriel\DandelionBundle\Entity\DandelionArticleSubject';
	}

	protected function addSecurityFilters($qb, $individu) {
		// NO SECURITY HERE.
		return;
	}

	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;

		$alias = $this->getAlias();

		if (isset($params['article']) && $params['article']) {
			$qb->andWhere($alias.'.article = :article')->setParameter('article', $params['article']);
			$hasParams = true;
		}

		return $hasParams;
	}

	/**
	 * @return DandelionArticleSubject
	 */
	public function getDandelionArticleSubject($id) {
		return $this->get($id);
	}

	/**
	 * @return DandelionArticleSubject
	 */
	public function getDandelionArticleSubjectByArticle($article) {
		if (!$article) return null;
		if (is_array($article)) return null;

		return $this->query(array('article' => $article), array());
	}

	/**
	 * @return DandelionArticleSubject[]
	 */
	public function getAllDandelionArticleSubject($options = array()) {
		return $this->getAll($options);
	}

}

?>
