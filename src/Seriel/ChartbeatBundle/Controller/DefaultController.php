<?php

namespace Seriel\ChartbeatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SerielChartbeatBundle:Default:index.html.twig');
    }
}
