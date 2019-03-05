<?php

namespace Seriel\DandelionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SerielDandelionBundle:Default:index.html.twig');
    }
}
