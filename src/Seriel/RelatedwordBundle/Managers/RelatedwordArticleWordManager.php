<?php

namespace Seriel\RelatedwordBundle\Managers;

use Seriel\AppliToolboxBundle\Managers\SerielManager;
use Seriel\RelatedwordBundle\Entity\ArticleWord;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\From;
use Doctrine\ORM\Query\ResultSetMapping;
use Seriel\AppliToolboxBundle\Managers\ManagersManager;

class RelatedwordArticleWordManager extends SerielManager
{
	public function getObjectClass() {
		return 'Seriel\RelatedwordBundle\Entity\ArticleWord';
	}

	protected function addSecurityFilters($qb, $individu) {
		// NO SECURITY HERE.
		return;
	}

	protected function buildQuery($qb, $params, $options = null, &$execvars = null) {
		$hasParams = false;

		$alias = $this->getAlias();

		if (isset($params['word']) && $params['word']) {
			$qb->andWhere($alias.'.word = :word')->setParameter('word', $params['word']);
			$hasParams = true;
		}
		if (isset($params['article']) && $params['article']) {
			$qb->andWhere($alias.'.article = :article')->setParameter('article', $params['article']);
			$hasParams = true;
		}
		if (isset($params['articles']) && $params['articles']) {
			$qb->andWhere($alias.'.article in (:articles)')->setParameter('articles', $params['articles']);
			$hasParams = true;
		}

		return $hasParams;
	}

	/**
	 * @return ArticlWord
	 */
	public function getArticleWord($id) {
		return $this->get($id);
	}

	/**
	 * @return ArticlWord
	 */
	public function getArticleWordByWordArticle($word,$article) {
		if (!$word) return null;
		if (is_array($word)) return null;
		if (!$article) return null;
		if (is_array($article)) return null;
		return $this->query(array('word' => $word, 'article' => $article), array('one' => true));
	}

	/**
	 * @return ArticlWord
	 */
	public function getArticleWordByArticle($article) {
		if (!$article) return null;
		if (is_array($article)) return null;
		return $this->query(array('article' => $article), array());
	}

	/**
	 * @return ArticlWord[]
	 */
	public function getAllArticleWord($options = array()) {
		return $this->getAll($options);
	}

	/**
	 * @return integer[]
	 */
	public function getAllWordIdByArticles($articles) {
		if (!$articles) return null;
		if (!is_array($articles)) return null;
		
		return $this->query(array('articles' => $articles), array());
	}
	
	//subquery Related word return list article_id order by Weight with multiple wordsearch or one wordsearch
	public function getResultSubQuerySearchSemantiquebyMultipleWord($relatedwordByWordSearch,$module_mot_cle, $module_dandelion) {
		
		$RelatedwordMgr = ManagersManager::getManager()->getContainer()->get('seriel_related_word.manager');
		if (false) $RelatedwordMgr = new RelatedwordManager();
		$NBWordSearch = count($relatedwordByWordSearch);
				
		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('id', 'id');
		$rsm->addScalarResult('totalweight', 'totalweight');
		
		//construct sql stape 1, example :  select id,  weight1 * weight2 as totalweight from
		$sql =' select id, ';
		for ($iterator = 1;$iterator<= $NBWordSearch; $iterator++) {
			if ($iterator != 1) {
				$sql = $sql. ' * ';
			}
			$sql = $sql. 'weight'.$iterator;
		}
		$sql = $sql. ' as totalweight from (';
		
		//construct sql stape 2, example :  select article.id, t2.name as name1, ifnull(t2.weight, -1)+1.2 as weight1, p2.name as name2, ifnull(p2.weight, -1)+1.2 as weight2 from article
		// if article don't have a word for each word cloud, reduce weight else increase weight
		$sql = $sql. ' select article.id, ';
		for ($iterator = 1;$iterator<= $NBWordSearch; $iterator++) {
			if ($iterator != 1) {
				$sql = $sql. ' , ';
			}
			// get weight for article without match related word  / 0.2 by default
			if ($this->container->hasParameter('relatedword.weight_for_whitout_match_word')) {
				$weightWhitoutMatchWord= $this->container->getParameter('relatedword.weight_for_whitout_match_word');
			}
			else {
				$weightWhitoutMatchWord = 0.2;
			}
			
			$sql = $sql. 'ifnull(table'.$iterator.'.weight, -1)+ 1  + '.$weightWhitoutMatchWord.' as weight'.$iterator;
		}
		$sql = $sql. ' from article ';
		
		$i = 1;
		foreach ($relatedwordByWordSearch as $relatedwordList) {
			//construct sql stape 3, example :
			/*
			 * 	left join (
			 *  select article_id, 'toyota' as name, sum(weight) as weight from (
			 *   	SELECT artword.article_id, word.name, ((( ( artword.intitle * 1.5 ) + artword.inchapeau +1) * artword.quantity * 1)/ SQRT(word.quantity)) as weight FROM rw_word word inner join rw_article_word artword on word.id = artword.word_id WHERE word.name = 'toyot'
			 * 		union
			 * 		SELECT artword.article_id, word.name, ((( ( artword.intitle * 1.5 ) + artword.inchapeau +1) * artword.quantity * 0.75)/ SQRT(word.quantity)) as weight FROM rw_word word inner join rw_article_word artword on word.id = artword.word_id WHERE word.name = 'voitur'
			 *  ) as t group by article_id
			 *  ) t2 on article.id = t2.article_id
			 */
			$sql = $sql. ' left join ( ';
			$sql = $sql. ' select article_id, sum(weight) as weight from ( ';
			
			$y = 1;
			foreach ($relatedwordList as $relatedword) {
				if( $y != 1) {
					$sql = $sql. ' union ';
				}
				// search module_mot_cle
				if ($module_mot_cle) {
					$sql = $sql. 'SELECT artword.article_id, ((( ( artword.intitle * 1.5 ) + artword.inchapeau +1) * artword.quantity * ?)/ word.quantity) as weight FROM rw_word word inner join rw_article_word artword on word.id = artword.word_id WHERE word.name = ? ';
				}
				// module_mot_cle + module_dandelion
				if ( $module_dandelion and $module_mot_cle) {
					$sql = $sql. ' union ';
				}
				// module_dandelion
				if ( $module_dandelion ) {
					//Dandelion weight (add link word with related word )  / * 4 - dandelion_article_entity_link.type => weight is more important for title ( * 3) and chapeau (* 2)
					// related word math with Dandelion if relatedword = dandelion or dandelion is composed by relatedword
					$sql = $sql. "SELECT dlink.article_id, ((dlink.confidence * ( 4 - dlink.type ) * ?)) as weight  FROM dandelion_article_entity_link dlink INNER JOIN dandelion_entity dand ON dand.id = dlink.dandelion_entity_id WHERE dlink.spot_racin = ? OR dlink.spot_racin REGEXP ? OR  dlink.spot_racin REGEXP ?  ";
				}
				$y++;
			}
			$sql = $sql. ' ) as subtable'.$i.' group by article_id ';
			$sql = $sql. ' ) table'.$i.' on article.id = table'.$i.'.article_id ';
			$i++;
		}
		
		//construct sql stape 4, example : where t2.article_id is not null or p2.article_id is not null) i
		$sql = $sql. ' where ';
		for ($iterator = 1;$iterator<= $NBWordSearch; $iterator++) {
			if ($iterator != 1) {
				$sql = $sql. ' or ';
			}
			$sql = $sql. ' table'.$iterator.'.article_id is not null ';
		}
		$sql = $sql. ' ) i order by totalweight desc LIMIT 500';
		
		$query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
		
		// -----------------PARAMETER SUBQUERY---------------------
		$i = 1;
		foreach ($relatedwordByWordSearch as $relatedwordList) {
			foreach ($relatedwordList as $key => $value) {
				
				// module_mot_cle
				if ($module_mot_cle) {
					$query->setParameter($i, $value);
					$i++;
					$query->setParameter($i, $key);
					$i++;
				}
				// module_dandelion
				if ( $module_dandelion ) {
					$query->setParameter($i, $value);
					$i++;
					//for =
					$query->setParameter($i, $key);
					$i++;
					//For REGEXP
					$query->setParameter($i, '^'.$key.' ');
					$i++;
					//For REGEXP
					$query->setParameter($i, ' '.$key.'$');
					$i++;
				}
				
			}
		}
		return $query->getResult();
	}
	
}

?>
