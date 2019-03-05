<?php

namespace Seriel\DandelionBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use Seriel\DandelionBundle\Entity\DandelionArticleSemantics;

class DandelionArticleSemanticsManager extends SerielManager
{
	public function getObjectClass() {
		return 'Seriel\DandelionBundle\Entity\DandelionArticleSemantics';
	}
	
	protected function addSecurityFilters($qb, $individu) {
		// NO SECURITY HERE.
		return;
	}
	
	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;
		
		$alias = $this->getAlias();
	
		if (isset($params['article']) && $params['article']) {
			if ($this->addWhereId($qb, $alias.'.article', $params['article'])) {
				$hasParams = true;
			}
		}
		
		return $hasParams;
	}
	
	/**
	 * @return DandelionArticleSemantics
	 */
	public function getDandelionArticleSemantics($id) {
		return $this->get($id);
	}
	
	/**
	 * @return DandelionArticleSemantics
	 */
	public function getDandelionArticleSemanticsForArticleId($article_id) {
		if (!$article_id) return null;
		if (is_array($article_id)) return null;
		
		return $this->query(array('article' => $article_id), array('one' => true));
	}
	
	/**
	 * @return DandelionArticleSemantics[]
	 */
	public function getAllDandelionArticleSemantics($options = array()) {
		return $this->getAll($options);
	}

	public function getLastDateCalcul() {
		$Last = $this->query(array(), array('orderBy' => array('date_calcul' => 'desc'), 'limit' => 1));
		if (count($Last) == 1){
			return $Last[0]->getDateCalcul();
		}
		else {
			return null;
		}
		
	}
}

?>
