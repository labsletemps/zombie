<?php

namespace Seriel\DandelionBundle\Managers;

use ZombieBundle\Entity\News\Article;
use Seriel\DandelionBundle\Entity\DandelionArticleSemantics;
use Doctrine\Common\Collections\ArrayCollection;
use ZombieBundle\API\Managers\ManagerStateData;
use ZombieBundle\API\Entity\StateImport;

class DandelionManager implements ManagerStateData{

	protected $baseurl = "https://api.dandelion.eu";

	const ENTITY_EXTRACTION = "/datatxt/nex/v1/";

	protected $container = null;
	protected $logger = null;
	protected $dandelion_params = null;

	public function __construct($container, $logger, $params) {
		$this->container = $container;
		$this->logger = $logger;

		$this->dandelion_params = $params;
	}

	protected function getToken() {
		return $this->dandelion_params['Token'];
	}

	public function entityExtraction($txt, $parameters = array()) {
		if (!$txt) return array();

		$url = $this->baseurl.self::ENTITY_EXTRACTION;

		$postfields = array("token" => $this->getToken(), "text" => $txt, "include" => "types,categories,lod,alternate_labels");

		echo "URL : ".$url."\n";

		$options = array(
				CURLOPT_RETURNTRANSFER => true,     // return web page
				CURLOPT_HEADER         => false,    // don't return headers
				CURLOPT_FOLLOWLOCATION => true,     // follow redirects
				CURLOPT_ENCODING       => "",       // handle all encodings
				CURLOPT_USERAGENT      => "letemps-ch-zombie", // who am i
				CURLOPT_AUTOREFERER    => true,     // set referer on redirect
				CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
				CURLOPT_TIMEOUT        => 120,      // timeout on response
				CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
				CURLOPT_POST           => 1,
				CURLOPT_POSTFIELDS     => http_build_query($postfields)
		);

		$ch = curl_init($url);

		curl_setopt_array($ch, $options);

		$datas = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		echo "Dandelion datas : \n$datas\n\n";

		if ($httpcode == 200) {
			return $datas;
		}

		return false;
	}

	public function entityExtractionFromArticle(Article $article, $parameters = array()) {
		if (!$article) return false;

		$semanticsMgr = $this->container->get('seriel_dandelion.article_semantics_manager');
		if (false) $semanticsMgr = new DandelionArticleSemanticsManager();

		$title = $article->getTitre();
		$chapeau = $article->getChapeauStriped();
		$content = $article->getContentStriped();

		$title_semantics = $this->entityExtraction($title);
		if ($title_semantics === false) return false;

		$chapeau_semantics = $this->entityExtraction($chapeau);
		if ($chapeau_semantics === false) return false;

		$content_semantics = $this->entityExtraction($content);
		if ($content_semantics === false) return false;

		$artSemantics = $semanticsMgr->getDandelionArticleSemanticsForArticleId($article->getId());

		if (!$artSemantics) $artSemantics = new DandelionArticleSemantics($article);
		$artSemantics->setDateCalcul(new \DateTime());
		$artSemantics->setTitre($title);
		$artSemantics->setChapeau($chapeau);
		$artSemantics->setContent($content);
		$artSemantics->setTitreSemantics($title_semantics);
		$artSemantics->setChapeauSemantics($chapeau_semantics);
		$artSemantics->setContentSemantics($content_semantics);

		return $artSemantics;
	}

	/*
	* return text with <span class=""> for semantic entities
	*/
	protected function insertEntitiesIntoText($text, $entities) {

		$textWork = $text;

		// sort by End for to have a correct position
		$entities = new ArrayCollection($entities);
		$iterator = $entities->getIterator();
		$iterator->uasort(function ($a, $b) {
			return ($a->getStart() < $b->getStart()) ? 1 : -1;
		});
		$entities = new ArrayCollection(iterator_to_array($iterator));

		$block_end = "";

		// build text
		foreach ($entities as $entitie) {

			// choice class for html
			$DandelionEntity = $entitie->getEntity();
			if ($DandelionEntity->isType('http://dbpedia.org/ontology/Person')) {
			    $classEntity ='EntitySemanticPerson';
			} elseif ($DandelionEntity->isType('http://dbpedia.org/ontology/Work')) {
			    $classEntity ='EntitySemanticWork';
			} elseif ($DandelionEntity->isType('http://dbpedia.org/ontology/Organisation')) {
			    $classEntity ='EntitySemanticOrganisation';
			}elseif ($DandelionEntity->isType('http://dbpedia.org/ontology/Place')) {
			    $classEntity ='EntitySemanticPlace';
			}elseif ($DandelionEntity->isType('http://dbpedia.org/ontology/Event')) {
			    $classEntity ='EntitySemanticEvent';
			}else {
			    $classEntity ='EntitySemanticConcept';
			}
			$block_end = '<span class="'.$classEntity.'">'.$entitie->getSpot().'</span>'.htmlentities(mb_substr($textWork, $entitie->getEnd())).$block_end;
			$textWork = mb_substr($textWork, 0, $entitie->getStart());

		}

		return htmlentities($textWork).$block_end;
	}
	/*
	* return array for count type Entitysemantic
	*/
	protected function getNumberEntitiesSemantic($entities) {
		$compteur = array('Person'=> 0,'Work'=> 0,'Organisation'=> 0,'Place'=> 0,'Event'=> 0,'Concept'=> 0);
		foreach ($entities as $entitie) {
			$DandelionEntity = $entitie->getEntity();
			if ($DandelionEntity->isType('http://dbpedia.org/ontology/Person')) {
			    $compteur['Person'] = $compteur['Person']+1;
			} elseif ($DandelionEntity->isType('http://dbpedia.org/ontology/Work')) {
			    $compteur['Work'] = $compteur['Work']+1;
			} elseif ($DandelionEntity->isType('http://dbpedia.org/ontology/Organisation')) {
			    $compteur['Organisation'] = $compteur['Organisation']+1;
			}elseif ($DandelionEntity->isType('http://dbpedia.org/ontology/Place')) {
			    $compteur['Place'] = $compteur['Place']+1;
			}elseif ($DandelionEntity->isType('http://dbpedia.org/ontology/Event')) {
			    $compteur['Event'] = $compteur['Event']+1;
			}else {
			    $compteur['Concept'] = $compteur['Concept']+1;
			}
		}
		return $compteur;
	}

	// Display page semantic dandelion of on subpage Article
	public function articleSemantics($article) {
		if (!$article) return '';
		if (false) $article = new Article();

		$templating = $this->container->get('templating');

		$semanticsMgr = $this->container->get('seriel_dandelion.article_semantics_manager');
		if (false) $semanticsMgr = new DandelionArticleSemanticsManager();

		$semantics = $semanticsMgr->getDandelionArticleSemanticsForArticleId($article->getId());
		if (false) $semantics = new DandelionArticleSemantics($article);
		
		if ($semantics != null ) {
			$titleWithEntities = $this->insertEntitiesIntoText($article->getTitre(), $semantics->getEntitiesTitle());
			$chapeauWithEntities = $this->insertEntitiesIntoText($article->getChapeauStriped(), $semantics->getEntitiesChapeau());
			$contentWithEntities = $this->insertEntitiesIntoText($article->getContentStriped(), $semantics->getEntitiesContent());

			$compteur = $this->getNumberEntitiesSemantic($semantics->getEntities());
		}


		if (!$semantics) return "";

		return $templating->render('SerielDandelionBundle:Article:article_semantics.html.twig', array(
			'article' => $article,
			'semantics' => $semantics,
			'titleWithEntities' => $titleWithEntities,
			'chapeauWithEntities' => $chapeauWithEntities,
			'contentWithEntities' => $contentWithEntities,
			'compteur' => $compteur,
		));
	}

	// return check list of import and calculate data for Dandelion
	public function getStateImports() {
		$stateInports = array();
		
		$semanticsMgr = $this->container->get('seriel_dandelion.article_semantics_manager');
		if (false) $semanticsMgr = new DandelionArticleSemanticsManager();
		
		$date= $semanticsMgr->getLastDateCalcul();
		if (isset($date)) {
			$stateInports[] = New StateImport('Dandelion - Dernier calcul', $date);
		}
		return $stateInports;
	}

}
