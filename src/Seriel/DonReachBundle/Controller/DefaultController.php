<?php

namespace Seriel\DonReachBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SerielDonReachBundle:Default:index.html.twig');
    }
}
