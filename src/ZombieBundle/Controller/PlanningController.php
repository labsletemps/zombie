<?php

namespace ZombieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Seriel\AppliToolboxBundle\Utils\DateUtils;
use ZombieBundle\Entity\News\Article;

class PlanningController extends Controller
{
	public static function orderEvents($evt1, $evt2) {
		if ((!$evt1) && (!$evt2)) return 0;
		if (!$evt1) return 1;
		if (!$evt2) return -1;
		
		$date1 = $evt1['date'];
		$date2 = $evt2['date'];
		
		if ((!$date1) && (!$date2)) return 0;
		if (!$date1) return 1;
		if (!$date2) return -1;
		
		if (false) $date1 = new \DateTime();
	
		
		$d1 = $date1->format('Y-m-d H:i');
		$d2 = $date2->format('Y-m-d H:i');
		
		$res = strcmp($d1, $d2);
		if ($res != 0) return $res;
		
		// compare id.
		$tick1 = $evt1['numticket'];
		$tick2 = $evt2['numticket'];
		
		if ((!$tick1) && (!$tick2)) return 0;
		if (!$tick1) return 1;
		if (!$tick2) return -1;
		
		return strcmp($tick1, $tick2);
	}
	
	// Main page planning
	public function indexAction( $date=null)
    {
    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_planning')) {
    		throw $this->createAccessDeniedException();
    	}
    	// Let's get datas.
    	$articlesManager = $this->get("articles_manager");
    	if (false) $articlesManager = new ArticlesManager();
    	
    	$currDate = date('Y+W');
    	if (isset($date)) {
    		$firstDay = DateUtils::firstDayOfWeek($date);
    	}else {
    		$firstDay = DateUtils::firstDayOfWeek($currDate);
    	}
    	
    	$splitted = explode('-', $firstDay);
    	$year = intval($splitted[0]);
    	$month = intval($splitted[1]);
    	$day = intval($splitted[2]);
    	
    	$days = array();
    	$days[] = $firstDay;
    	
    	for ($i = 0; $i < 6; $i++) {
    		$d = $lastDay = date('Y-m-d', mktime(12, 0, 0, $month, $day+$i+1, $year));
    		$days[] = $d;
    	}
    	
    	$lastDay = $days[count($days)-1];
    	
    	$events = array();

    	$articles = $articlesManager->query(array('date_parution' => $firstDay.'::'.$lastDay));
    	foreach ($articles as $article) {
    		if (false) $article= new Article();
    	
    		$date = $article->getDateParution();
    		if (!$date) continue;
    		
    		// if hour is 0, value is 8.
    		if ($date->format('H:i') == '00:00') {
    			$date->add(\DateInterval::createFromDateString('8 hours'));
    		}
    	
    		$event = array('date' => $date, 'numticket' => $article->getId(), 'type' => 'inter', 'elem' => $article);
    	
    		$events[] = $event;
    	}
    	
    	// Let's order
    	usort($events, array('ZombieBundle\Controller\PlanningController', 'orderEvents'));
    	
    	$eventsByDay = array($days[0] => array(),
    						 $days[1] => array(),
    						 $days[2] => array(),
    						 $days[3] => array(),
    						 $days[4] => array(),
    						 $days[5] => array(),
    						 $days[6] => array());
    	
    	foreach ($events as $evt) {
    		$date = $evt['date']->format('Y-m-d');
    		$eventsByDay[$date][] = $evt;
    	}
    	
        return $this->render('ZombieBundle:Planning:planning.html.twig', array('days' => $days, 'eventsByDay' => $eventsByDay, 'date' => $currDate));
    }
    
    public function ajaxLoadAction($type, $date) {
    	if (!$this->get('security.authorization_checker')->isGranted('nav_page_planning')) {
    		throw $this->createAccessDeniedException();
    	}
    	if ($type == 'week') {
    		// Let's get datas.
    		$articlesManager = $this->get("articles_manager");
    		if (false) $articlesManager = new ArticlesManager();
    		 
    		$firstDay = DateUtils::firstDayOfWeek($date);
    		 
    		$splitted = explode('-', $firstDay);
    		$year = intval($splitted[0]);
    		$month = intval($splitted[1]);
    		$day = intval($splitted[2]);
    		 
    		$days = array();
    		$days[] = $firstDay;
    		 
    		for ($i = 0; $i < 6; $i++) {
    			$d = $lastDay = date('Y-m-d', mktime(12, 0, 0, $month, $day+$i+1, $year));
    			$days[] = $d;
    		}
    		 
    		$lastDay = $days[count($days)-1];
    		 
    		$events = array();
    		 
    		$articles = $articlesManager->query(array('date_parution' => $firstDay.'::'.$lastDay));
    		foreach ($articles as $article) {
    			if (false) $article= new Article();
    			 
    			$date = $article->getDateParution();
    			if (!$date) continue;
    		
    			// if hour is 0, value is 8.
    			if ($date->format('H:i') == '00:00') {
    				$date->add(\DateInterval::createFromDateString('8 hours'));
    			}
    			 
    			$event = array('date' => $date, 'numticket' => $article->getId(), 'type' => 'art', 'elem' => $article);
    			 
    			$events[] = $event;
    		}
    		 
    		// Let's order
    		usort($events, array('ZombieBundle\Controller\PlanningController', 'orderEvents'));
    		 
    		$eventsByDay = array($days[0] => array(),
    				$days[1] => array(),
    				$days[2] => array(),
    				$days[3] => array(),
    				$days[4] => array(),
    				$days[5] => array(),
    				$days[6] => array());
    		 
    		foreach ($events as $evt) {
    			$date = $evt['date']->format('Y-m-d');
    			$eventsByDay[$date][] = $evt;
    		}
    		 
    		return $this->render('ZombieBundle:Planning:planning_ajax.html.twig', array('days' => $days, 'eventsByDay' => $eventsByDay, 'date' => $date));
    	}
    }
}
