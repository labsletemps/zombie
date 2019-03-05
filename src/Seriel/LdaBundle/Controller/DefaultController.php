<?php

namespace Seriel\LdaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{

    public function indexAction()
    {
        return $this->render('SerielLdaBundle:Default:index.html.twig');
    }
}
