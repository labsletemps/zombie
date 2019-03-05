<?php

namespace ZombieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ZombieBundle\Managers\Securite\SecurityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
	public function indexAction(Request $request)
    {
    	
    	$mtime_start = ''.microtime();
    	error_log('INDEX EX START : '.$mtime_start);
    	
    	$securityManager = $this->get('security_manager');
    	if (false) $securityManager = new SecurityManager();
    	
    	// Important to init credentials
    	$securityManager->getCurrentIndividu();
    	
        return $this->render('ZombieBundle:Default:index.html.twig');
    }
    
    public function workInProgressAction() {
    	return $this->render('ZombieBundle:Default:work_in_progress.html.twig');
    }
}
