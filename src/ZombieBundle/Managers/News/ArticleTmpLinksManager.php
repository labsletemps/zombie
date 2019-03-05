<?php

namespace ZombieBundle\Managers\News;

use ZombieBundle\Managers\ZombieManager;
use ZombieBundle\Entity\News\Article;
use ZombieBundle\Entity\News\ArticleTmpLink;

class ArticleTmpLinksManager extends ZombieManager
{
	protected function addSecurityFilters($qb, $individu) {
		// No security here. Required for security !
	}
	protected function addSecurity($qb) {
		// No security here. Required for security !
		return;
	}
	
	public function getObjectClass() {
		return 'ZombieBundle\Entity\News\ArticleTmpLink';
	}
	
	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;
		
		$alias = $this->getAlias();
		
		$now = date('Y-m-d H:i:s');
		
		$qb->andWhere($alias.".date_deadline > '$now'");
		
		if (isset($params['code']) && $params['code']) {
			$qb->andWhere($alias.'.code = :code')->setParameter('code', $params['code']);
			$hasParams = true;
		}
	
		return $hasParams;
	}
	
	public function cleanDatabase() {
		$em = $this->doctrine->getManager();
		
		$sql = "delete from article_tmp_link where date_deadline < now()";
		
		$stmt = $em->getConnection()->prepare($sql);
		$stmt->execute();
	}

	/**
	 * @return ArticleTmpLink
	 */
	public function getArticleArticleTmpLink($code) {
		return $this->query(array('code' => $code), array('one' => true));
	}

	public function createArticleTmpLink($article, $duration_in_seconds) {
		$tmp_link = new ArticleTmpLink();
		$tmp_link->setArticle($article);
		$tmp_link->setDateCreation(new \DateTime());
		$tmp_link->setDurationInSeconds($duration_in_seconds);
		
		$code = md5("".time().rand(10000, 99999));
		
		$tmp_link->setCode($code);
		
		$this->save($tmp_link, true);
		
		return $code;
	}
}

?>
