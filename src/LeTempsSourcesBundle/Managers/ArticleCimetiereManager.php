<?php

namespace LeTempsSourcesBundle\Managers;

use function GuzzleHttp\json_decode;

class ArticleCimetiereManager {
	protected $container = null;
	protected $logger = null;
	
	protected $url = "https://www.example.com/api/content";
	
	public function __construct($logger, $container) {
		$this->container = $container;
		$this->logger = $logger;
	}
	
	public function getAllArts() {
		
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
		);
		
		$ch = curl_init($this->url);
		
		curl_setopt_array($ch, $options);
		
		$datas = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
				
		if (intval($httpcode) != 200) return false;
		
		$parsed = json_decode($datas);
		
		$res = array();
		foreach ($parsed as $dt) {
			$guid = trim($dt->guid);
			$titre = trim(str_replace('&#039;', "'", $dt->titre));
			$mot_cle = trim($dt->motcle);
			$chapeau = trim($dt->chapeau);
			$content = trim($dt->contenu);
			$date_parution = str_replace('T', ' ', trim(strip_tags($dt->dte_publication)));
			$section = trim($dt->section);
			$tags = trim($dt->tags);
			$auteur_ext = trim($dt->auteur);
			$auteur_int = trim($dt->auteur_int);
			$image = trim($dt->image);
			
			// remove part of GMT.
			$index_plus = strrpos($date_parution, '+');
			if ($index_plus) $date_parution = substr($date_parution, 0, $index_plus);
						
			if (trim(strtoupper($mot_cle)) == 'NULL') $mot_cle = null;
			if (trim(strtoupper($section)) == 'NULL') $section = null;
			if (trim(strtoupper($tags)) == 'NULL') $tags = null;
			
			if (trim(strtoupper($auteur_ext)) == 'NULL') $auteur_ext = null;
			if (trim(strtoupper($auteur_int)) == 'NULL') $auteur_int = null;
			if (trim(strtoupper($image)) == 'NULL') $image = null;
			
			$splitted = explode(' ', $date_parution);
			$day = $splitted[0];
				
			$date = \DateTime::createFromFormat("Y-m-d H:i:s", $date_parution);
			
			$art = array('guid' => $guid, 'titre' => $titre, 'chapeau' => $chapeau, 'content' => $content, 'mot_cle' => $mot_cle, 'tags' => $tags, 'date_parution' => $date, 'section' => $section, 'auteur' => $auteur_int, 'auteur_ext' => $auteur_ext, 'image' => $image);
			
			$res[] = $art;
		}

		return $res;
	}
}