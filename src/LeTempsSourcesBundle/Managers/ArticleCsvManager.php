<?php

namespace LeTempsSourcesBundle\Managers;

use Seriel\AppliToolboxBundle\Utils\DateUtils;

class ArticleCsvManager {
	
	protected static $source_csv_file = 'articles.csv';
	
	protected $container = null;
	protected $logger = null;
	
	protected $artsMap = array();
	protected $artsMapByDay = array();
	protected $inited = false;
	
	public function __construct($logger, $container) {
		$this->container = $container;
		$this->logger = $logger;
	}
	
	protected function checkInited() {
		if ($this->inited) return;
		
		$this->parseContent();
	}
	
	protected function parseContent() {
		$this->artsMap = array();
		
		// Let's read the file.
		$counter = 0;
		$file = fopen(self::$source_csv_file, "r");
		 
		$firstReaden = false;
		
		$currentReading = null;
		
		while(!feof($file)){
			$line = fgetcsv($file, 0, ',', '"', '\\');
			
			if (!$line) {
				break 1;
			}
			
			if (!$firstReaden) {
				$firstReaden = true;
				continue;
			}
			
			if ($currentReading) {
				$currentRSize = count($currentReading);
				$currentReading[$currentRSize-1] .= $line[0];
				for ($i = 1; $i < count($line); $i++) {
					$currentReading[] = $line[$i];
				}
				
				$line = $currentReading;
			}
			//var_dump($line);
			if (count($line) < 11) {
				echo "ERROR LINE  : ".print_r($line, true)."\n";
				$currentReading = $line;
				continue;
			}
			if (count($line) > 11) {
				echo "ERROR LINE > 11 : [".count($line)."] >> ".$line[0]."\n";
				$currentReading = null;
				continue;
			}
			
			$counter++;
				
			$currentReading = null;
			
			$guid = trim($line[0]);
			$titre = trim($line[1]);
			$mot_cle = trim($line[2]);
			$chapeau = trim($line[3]);
			$content = trim($line[4]);
			$date_parution = str_replace('T', ' ', trim($line[5]));
			$section = trim($line[6]);
			$tags = trim($line[7]);
			$auteur_ext = trim($line[8]);
			$auteur_int = trim($line[9]);
			$image = trim($line[10]);
			
			if (trim(strtoupper($mot_cle)) == 'NULL') $mot_cle = null;
			if (trim(strtoupper($section)) == 'NULL') $section = null;
			if (trim(strtoupper($tags)) == 'NULL') $tags = null;
			
			if (trim(strtoupper($auteur_ext)) == 'NULL') $auteur_ext = null;
			if (trim(strtoupper($auteur_int)) == 'NULL') $auteur_int = null;
			if (trim(strtoupper($image)) == 'NULL') $image = null;
						
			$splitted = explode(' ', $date_parution);
			$day = $splitted[0];
			
			$date = \DateTime::createFromFormat("Y-m-d H:i:s", $date_parution);
			
			
			// TODO : format date parution
			
			if ($counter % 50000 == 0) echo "OK $counter\n";
			
			$art = array('guid' => $guid, 'titre' => $titre, 'chapeau' => $chapeau, 'content' => $content, 'mot_cle' => $mot_cle, 'tags' => $tags, 'date_parution' => $date, 'section' => $section, 'auteur' => $auteur_int, 'auteur_ext' => $auteur_ext, 'image' => $image);
			
			$this->artsMap[$guid] = $art;
			if (!isset($this->artsMapByDay[$day])) $this->artsMapByDay[$day] = array();
			$this->artsMapByDay[$day][] = $art;
		}
		
		echo "DONE : $counter\n";
		
		$this->inited = true;
	}
	
	public function getArt($guid) {
		$this->checkInited();
		if (isset($this->artsMap[$guid])) return $this->artsMap[$guid];
		
		return null;
	}
	
	public function getAllArtsForPeriode($date_debut, $date_fin) {
		if ((!$date_debut) && (!$date_fin)) return array();
		
		$this->checkInited();
		
		// TODO ; changer la date de depart si nécessaire.
		$ts_start = $date_debut ? DateUtils::dateToTimestamp($date_debut) : DateUtils::dateToTimestamp('2010-01-01');
		$ts_end = $date_fin ? DateUtils::dateToTimestamp($date_fin) : time();
		
		$res = array();
		
		
		// get Data interesting, test for all day
		$parcours = $ts_start;
		while ($parcours <= $ts_end) {
			$day = date('Y-m-d', $parcours);
			
			if (isset($this->artsMapByDay[$day])) {
				foreach ($this->artsMapByDay[$day] as $art) {
					$res[] = $art;
				}
			}
			
			$parcours += (24 * 3600);
		}
		
		return $res;
	}
	
	public function getAllArts() {
		$this->checkInited();
		return $this->artsMap;
	}
}