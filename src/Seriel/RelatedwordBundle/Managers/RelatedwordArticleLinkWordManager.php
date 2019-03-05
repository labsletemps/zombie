<?php

namespace Seriel\RelatedwordBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use Seriel\RelatedwordBundle\Entity\ArticleLinkWord;

class RelatedwordArticleLinkWordManager extends SerielManager
{
	public function getObjectClass() {
		return 'Seriel\RelatedwordBundle\Entity\ArticleLinkWord';
	}

	protected function addSecurityFilters($qb, $individu) {
		// NO SECURITY HERE.
		return;
	}

	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;

		$alias = $this->getAlias();

		if (isset($params['wordSource']) && $params['wordSource']) {
			$qb->andWhere($alias.'.wordSource = :wordSource')->setParameter('wordSource', $params['wordSource']);
			$hasParams = true;
		}
		if (isset($params['wordTarget']) && $params['wordTarget']) {
			$qb->andWhere($alias.'.wordTarget = :wordTarget')->setParameter('wordTarget', $params['wordTarget']);
			$hasParams = true;
		}
		if (isset($params['article']) && $params['article']) {
			$qb->andWhere($alias.'.article = :article')->setParameter('article', $params['article']);
			$hasParams = true;
		}

		return $hasParams;
	}

	/**
	 * @return ArticleLinkWord
	 */
	public function getArticleLinkWord($id) {
		return $this->get($id);
	}

	/**
	 * @return ArticleLinkWord
	 */
	public function getArticleLinkWordByWordArticle($wordSource,$wordTarget,$article) {
		if (!$wordSource) return null;
		if (is_array($wordSource)) return null;
		if (!$wordTarget) return null;
		if (is_array($wordTarget)) return null;
		if (!$article) return null;
		if (is_array($article)) return null;
		return $this->query(array('wordSource' => $wordSource, 'wordTarget' => $wordTarget, 'article' => $article), array('one' => true));
	}

	/**
	 * @return ArticleLinkWord
	 */
	public function getArticleLinkWordByArticle($article) {
		if (!$article) return null;
		if (is_array($article)) return null;
		return $this->query(array('article' => $article), array());
	}

	/**
	 * @return ArticleLinkWords[]
	 */
	public function getAllArticleLinkWord($options = array()) {
		return $this->getAll($options);
	}

	/**
	 * @return int
	 */
	public function removeAllArticleLinkWordByArticle($article) {
		if (!$article) return null;
		if (is_array($article)) return null;
		$qb = $this->getQueryBuilder(array('article' => $article), array());
		$objClass = $this->class;
		$alias = $this->getAlias();
		$qb->delete($objClass, $alias);
		return $qb->getQuery()->execute();
	}

	/**
	 * @return int
	 */
	public function generateAllArticleLinkWordByArticle($article) {
		if (!$article) return null;
		if (is_array($article)) return null;
		$articleid = $article->getId();

		$conn = $this->getDoctrineEM()->getConnection();
		// (((t1.intitle * 1.5) + ( t2.intitle * 1.5 ) + t1.inchapeau + t2.inchapeau +1) * ( t1.quantity * t2.quantity )) weight is superior if a word is intitle and inchapeau
		return $conn->executeQuery('delete from rw_article_link_word where article_id = ?; insert into rw_article_link_word (article_id, word_source_id, word_target_id, weight) select ?, t1.word_id, t2.word_id, (((t1.intitle * 1.5) + ( t2.intitle * 1.5 ) + t1.inchapeau + t2.inchapeau +1) * ( t1.quantity * t2.quantity )) from (SELECT * FROM `rw_article_word` WHERE article_id = ?) as t1 inner join (SELECT * FROM `rw_article_word` WHERE article_id = ?) as t2 on t1.word_id < t2.word_id ', array($articleid,$articleid,$articleid,$articleid));

	}


}

?>
