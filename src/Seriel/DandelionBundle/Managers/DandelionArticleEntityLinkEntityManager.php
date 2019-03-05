<?php

namespace Seriel\DandelionBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use Seriel\DandelionBundle\Entity\DandelionArticleEntityLinkEntity;

class DandelionArticleEntityLinkEntityManager extends SerielManager
{
	public function getObjectClass() {
		return 'Seriel\DandelionBundle\Entity\DandelionArticleEntityLinkEntity';
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
	 * @return DandelionArticleEntityLinkEntity
	 */
	public function getDandelionArticleEntityLinkEntity($id) {
		return $this->get($id);
	}

	/**
	 * @return DandelionArticleEntityLinkEntity
	 */
	public function getDandelionArticleEntityLinkEntityByArticle($article) {
		if (!$article) return null;
		if (is_array($article)) return null;

		return $this->query(array('article' => $article), array());
	}

	/**
	 * @return DandelionArticleEntityLinkEntity[]
	 */
	public function getAllDandelionArticleEntityLinkEntity($options = array()) {
		return $this->getAll($options);
	}

	/**
	 * @return int
	 */
	public function generateDandelionArticleEntityLinkEntityByArticle($article) {
		if (!$article) return null;
		if (is_array($article)) return null;
		$articleid = $article->getId();
		
		$conn = $this->getDoctrineEM()->getConnection();
		// weight : ( ( t1.quantity * t2.quantity ) * ( t1.weight * t2.weight ))  
		
		return $conn->executeQuery('delete from dandelion_article_entity_link_entity where article_id = ?; insert into dandelion_article_entity_link_entity (article_id, spotSource, spotTarget, weight) select ?, t1.spot_racin, t2.spot_racin, ( ( t1.quantity * t2.quantity ) * ( t1.weight * t2.weight )) from (SELECT * FROM dandelion_article_entity WHERE article_id = ?) as t1 left join (SELECT * FROM dandelion_article_entity WHERE article_id = ?) as t2 on t1.spot_racin < t2.spot_racin  ', array($articleid,$articleid,$articleid,$articleid));
		
	}
	
}

?>
