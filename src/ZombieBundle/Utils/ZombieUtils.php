<?php

namespace ZombieBundle\Utils;

use ZombieBundle\Entity\News\Article;

class ZombieUtils {

    public static function drupal_pathauto_cleanstring($string)
    {

    	
    	$mots_interdits = array('alors', 'au', 'aucuns', 'aussi', 'autre', 'avant', 'avec', 'avoir', 'bon', 'car', 'ce', 'cela', 'ces'
    			, 'ceux', 'chaque', 'ci', 'comme', 'comment', 'dans', 'des', 'du', 'dedans', 'dehors', 'depuis', 'devrait', 'doit', 'donc'
    			, 'dos', 'début', 'elle', 'elles', 'en', 'encore', 'essai', 'est', 'et', 'eu', 'fait', 'faites', 'fois', 'font', 'hors'
    			, 'ici', 'il', 'ils', 'je', 'juste', 'la', 'le', 'les', 'leur', 'là', 'ma', 'maintenant', 'mais', 'mes', 'mine', 'moins'
    			, 'mon', 'mot', 'même', 'ni', 'nommés', 'nommes', 'notre', 'nous', 'ou', 'où', 'par', 'parce', 'pas', 'peut', 'peu', 'plupart'
    			, 'pour', 'pourquoi', 'quand', 'que', 'quel', 'quelle', 'quelles', 'quels', 'qui', 'sa', 'sans', 'ses', 'seulement', 'si'
    			, 'sien', 'son', 'sont', 'sous', 'soyez', 'sujet', 'sur', 'ta', 'tandis', 'tellement', 'tels', 'tes', 'ton', 'tous', 'tout'
    			, 'trop', 'très', 'tu', 'voient', 'vont', 'votre', 'vous', 'vu', 'ça', 'étaient', 'etaient', 'etat', 'état', 'etions', 'étions'
    			, 'ete', 'été', 'etre', 'être', 'l', 'a', 'son', 'd', 'de', 'n', 'm', 'a', 'e');
    	
    	$mots_interdits_map = array();
    	foreach ($mots_interdits as $mot) {
    		$mots_interdits_map[$mot] = $mot;
    	}
    	
        $url = $string;
        $url = str_replace("'", "", $url);
        $url = str_replace("’", "", $url);
        $url = str_replace("-", "", $url);
        $url = str_replace(",", "", $url);
        $url = str_replace(".", "", $url);
        
        $url = str_replace("«", "", $url);
        $url = str_replace("»", "", $url);
        
        $url = str_replace("(", "", $url);
        $url = str_replace(")", "", $url);
        
        $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url); // substitutes anything but letters, numbers and '_' with separator
        $url = trim($url, "-");
        $url = iconv("utf-8", "us-ascii//TRANSLIT", $url); // TRANSLIT does the whole job
        $url = strtolower($url);
        $url = preg_replace('~[^-a-z0-9_]+~', '', $url); // keep only letters, numbers, '_' and separator
        
        $mots = explode('-', $url);
        $mots_propres = array();
        
        foreach ($mots as $mot) {
        	if (isset($mots_interdits_map[$mot])) continue;
        	$mots_propres[] = $mot;
        }
        
        $url = implode('-', $mots_propres);
        
        return $url;
    }
    
    public static function drupal_section_to_url($section) {
    	$section = preg_replace('~[^\\pL0-9_]+~u', '-', $section); // substitutes anything but letters, numbers and '_' with separator
    	$section = trim($section, "-");
    	$section = iconv("utf-8", "us-ascii//TRANSLIT", $section); // TRANSLIT does the whole job
    	$section = strtolower($section);
    	$section= preg_replace('~[^-a-z0-9_]+~', '', $section); // keep only letters, numbers, '_' and separator
    	
    	return $section;
    }
    
    public static function build_drupal_url_from_article($article, $date_forced = null, $max_length = 100) {
    	if (!$article) return null;
    	
    	if (false) $article = new Article();
    	
    	$date_parution = $date_forced ? $date_forced : $article->getDateParution();
    	if (!$date_parution) return null;
    	
    	$url_section = null;
    	$section = $article->getSection();
    	if ($section) {
    		$url_section = $article->getUrlSection();

    		if (true) {
    			$url_section = self::drupal_section_to_url($section);
    		}
    	}
    	
    	$date_str = $date_parution->format('Y/m/d');
    	
    	$url_title = $article->getUrlTitle();

    	if (true) {
    		$url_title = self::drupal_pathauto_cleanstring($article->getTitre());
    	}
    	
    	if (!$url_title) return null;
    	
    	$url = ($url_section ? '/'.$url_section : '').'/'.$date_str.'/'.$url_title;
    	
    	while (strlen($url) > $max_length) {
    		$last_index = strrpos($url, '-');
    		if (!$last_index) break;
			$url = substr($url, 0, $last_index);
		}
		
		return $url;
    }
    
    public static function build_drupal_url_alternatives_from_article($article, $max_length = 100) {

    	if (false) $article = new Article();
    	
    	$date = $article->getDateParution();
    	$date_tomorrow = clone $date;
    	$date_tomorrow->add(date_interval_create_from_date_string('1 day'));
    	
    	$url_section = null;
    	$section = $article->getSection();
    	if ($section) {
    		$url_section = $article->getUrlSection();

    		if (true) {
    			$url_section = self::drupal_section_to_url($section);
    		}
    	}
    	
    	$section_str = ($url_section ? '/'.$url_section : '');
    	
    	$date_str = $date->format('Y/m/d');
    	$date_tomorrow_str = $date_tomorrow->format('Y/m/d');
    	
    	$url_title = $article->getUrlTitle();

    	if (true) {
    		$url_title = self::drupal_pathauto_cleanstring($article->getTitre());
    	}
    	
    	$url_title_spe = $url_title.'-0';
    	

    	
    	$res = array();    	
    	
    	$res[] = $section_str.'/'.$date_tomorrow_str.'/'.$url_title;
    	$res[] = $section_str.'/'.$date_str.'/'.$url_title_spe;
    	$res[] = $section_str.'/'.$date_tomorrow_str.'/'.$url_title_spe;
    	
    	
    	$tmp_res = array();
    	foreach ($res as $url) {
    		while (strlen($url) > $max_length) {
    			$last_index = strrpos($url, '-');
    			if (!$last_index) break;
    			$url = substr($url, 0, $last_index);
    		}
    		
    		$tmp_res[] = $url;
    	}
    	$res = $tmp_res;
    	

    	
    	return $res;
    }
    
    public static function getMarkOn100($value, $median) {
    	if (!$median) return 0;
    	
    	// We're using a method f(x) = ((100 * x) + 1) / (x + 1)
    	// We then adjust it to get exactly what we want
    	// the median parameter is important, it's gives the value we want to get a mark of 50. It changes the all shape of the curve.
    	$first_minor = 0.01;
    	$second_minor = 0.0001;
    	
    	// With our method, we know that 1 give a mark of 50. So we have to divide our $value by $median to make sure that the value $median will obtain a mark of 50
    	
    	$new_value = $value / $median;
    	$mark = round(((100 * ($new_value - $first_minor)) + 1) / (($new_value - $second_minor) + 1));
    	
    	//error_log("getMarkOn100 : value[$value] / median[$median] ==> mark[$mark]");
    	
    	return $mark;
    }
}