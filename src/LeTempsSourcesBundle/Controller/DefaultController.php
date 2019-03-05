<?php

namespace LeTempsSourcesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('LeTempsSourcesBundle:Default:index.html.twig');
    }
}
