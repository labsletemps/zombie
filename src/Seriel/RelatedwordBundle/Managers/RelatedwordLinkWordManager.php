<?php

namespace Seriel\RelatedwordBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use Seriel\RelatedwordBundle\Entity\LinkWord;

class RelatedwordLinkWordManager extends SerielManager
{
	public function getObjectClass() {
		return 'Seriel\RelatedwordBundle\Entity\LinkWord';
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

		return $hasParams;
	}

	/**
	 * @return LinkWord
	 */
	public function getLinkWord($id) {
		return $this->get($id);
	}

	/**
	 * @return LinkWord
	 */
	public function getLinkWordBySourceTarget($wordSource,$wordTarget) {
		if (!$wordSource) return null;
		if (is_array($wordSource)) return null;
		if (!$wordTarget) return null;
		if (is_array($wordTarget)) return null;
		return $this->query(array('wordSource' => $wordSource, 'wordTarget' => $wordTarget), array('one' => true));
	}

	/**
	 * @return LinkWord
	 */
	public function getBestLinkWordBySource($wordSource,$limit) {
		if (!$wordSource) return null;
		if (is_array($wordSource)) return null;
		if (!$limit) return null;
		if (is_array($limit)) return null;

		return $this->query(array('wordSource' => $wordSource), array('limit' => $limit, 'orderBy' => array('weight' => 'desc')));
	}

	/**
	 * @return LinkWord[]
	 */
	public function getAllLinkWord($options = array()) {
		return $this->getAll($options);
	}

	/**
	 * @return int
	 */
	public function generateAllLinkWordByArticle($article) {
		if (!$article) return null;
		if (is_array($article)) return null;
		$articleid = $article->getId();

		$conn = $this->getDoctrineEM()->getConnection();
		return $conn->executeQuery('INSERT INTO rw_link_word  (word_source_id, word_target_id, weight,updated_at) select rw1.word_source_id, rw1.word_target_id, rw1.weight, CURDATE() from rw_article_link_word as rw1 where article_id = ? ON DUPLICATE KEY UPDATE weight= rw_link_word.weight + rw1.weight;INSERT INTO rw_link_word  (word_source_id, word_target_id, weight) select rw1.word_target_id, rw1.word_source_id,  rw1.weight from rw_article_link_word as rw1 where article_id = ? ON DUPLICATE KEY UPDATE weight= rw_link_word.weight + rw1.weight;', array($articleid,$articleid));

	}

	/**
	 * @return int
	 */
	public function generateAllLinkWordByWord($arrayIdword) {
		if (!$arrayIdword) return null;
		if (!is_array($arrayIdword)) return null;

		$arrayIdwordString = implode(",", $arrayIdword);
		$conn = $this->getDoctrineEM()->getConnection();
		return $conn->executeQuery('INSERT INTO rw_link_word (word_source_id, word_target_id, weight,updated_at) select word_source_id, word_target_id, poid,  CURDATE() from (select rw1.word_source_id, rw1.word_target_id, sum(rw1.weight)/ sqrt(rw_word.quantity) poid from rw_article_link_word as rw1 inner join rw_word on rw1.word_target_id = rw_word.id where rw1.word_source_id in ('.$arrayIdwordString.') group by rw1.word_source_id, rw1.word_target_id) as t ON DUPLICATE KEY UPDATE weight= poid;INSERT INTO rw_link_word (word_source_id, word_target_id, weight,updated_at) select word_target_id, word_source_id, poid , CURDATE()  from (select rw1.word_source_id, rw1.word_target_id, sum(rw1.weight)/sqrt(rw_word.quantity) poid from rw_article_link_word as rw1 inner join rw_word on rw1.word_source_id = rw_word.id where rw1.word_source_id in ('.$arrayIdwordString.') group by rw1.word_source_id, rw1.word_target_id) as t ON DUPLICATE KEY UPDATE weight= poid;', array());
	}
	
	/**
	 * @return int
	 */
	public function generateAllLinkWordByGroup($Idmin,$Idmax,$axe) {
		if (!$Idmin) return null;
		if (!$Idmax) return null;
		
		$conn = $this->getDoctrineEM()->getConnection();
		if ($axe) {
			return $conn->executeQuery('INSERT INTO rw_link_word (word_source_id, word_target_id, weight,updated_at) select word_source_id, word_target_id, poid,  CURDATE() from (select rw1.word_source_id, rw1.word_target_id, sum(rw1.weight)/ sqrt(rw_word.quantity) poid from rw_article_link_word as rw1 inner join rw_word on rw1.word_target_id = rw_word.id and rw_word.id >= '.$Idmin.' AND rw_word.id < '.$Idmax.'  group by rw1.word_source_id, rw1.word_target_id) as t ON DUPLICATE KEY UPDATE weight= poid;', array());
		} else {
			return $conn->executeQuery('INSERT INTO rw_link_word (word_source_id, word_target_id, weight,updated_at) select word_target_id, word_source_id, poid,  CURDATE() from (select rw1.word_source_id, rw1.word_target_id, sum(rw1.weight)/sqrt(rw_word.quantity) poid from rw_article_link_word as rw1 inner join rw_word on rw1.word_source_id = rw_word.id and rw_word.id >= '.$Idmin.' AND rw_word.id < '.$Idmax.' group by rw1.word_source_id, rw1.word_target_id) as t ON DUPLICATE KEY UPDATE weight= poid;', array());
		}
		
	}
	

	public function getLastUpdateAt() {
		$Last = $this->query(array(), array('orderBy' => array('updated_at' => 'desc'), 'limit' => 1));
		if (count($Last) == 1){
			return $Last[0]->getUpdatedAt();
		}
		else {
			return null;
		}
		
	}
}

?>
