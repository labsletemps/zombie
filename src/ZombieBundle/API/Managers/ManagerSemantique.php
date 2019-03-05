<?php

namespace ZombieBundle\API\Managers;

interface ManagerSemantique {
	public function getArticlebySearchSemantiqueWithSubjects($subject);
	public function getArticlebySearchSemantique($text);
	public function getSimilarity($text1,$text2,$depth);
}