<?php

namespace Seriel\DandelionBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use Seriel\DandelionBundle\Entity\DandelionArticleEntity;

class DandelionArticleEntityManager extends SerielManager
{
	public function getObjectClass() {
		return 'Seriel\DandelionBundle\Entity\DandelionArticleEntity';
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
	 * @return DandelionArticleEntity
	 */
	public function getDandelionArticleEntity($id) {
		return $this->get($id);
	}

	/**
	 * @return DandelionArticleEntity
	 */
	public function getDandelionArticleEntityByArticle($article) {
		if (!$article) return null;
		if (is_array($article)) return null;

		return $this->query(array('article' => $article), array());
	}

	/**
	 * @return DandelionArticleEntity[]
	 */
	public function getAllDandelionArticleEntity($options = array()) {
		return $this->getAll($options);
	}

	/**
	 * @return int
	 */
	public function generateDandelionArticleEntityByArticle($article) {
		if (!$article) return null;
		if (is_array($article)) return null;
		$articleid = $article->getId();
		
		$conn = $this->getDoctrineEM()->getConnection();
		// RAZ row for $article and rebuild calcul DandedlionArticleEntity
		return $conn->executeQuery('delete from dandelion_article_entity where article_id = ?;insert into dandelion_article_entity (article_id, spot_racin, quantity, weight) select t1.article_id, t1.spot_racin , count(t1.spot_racin), AVG(t1.confidence) from dandelion_article_entity_link as t1	where t1.article_id = ? group by t1.spot_racin ', array($articleid,$articleid));
		
	}
	
	
}

?>
