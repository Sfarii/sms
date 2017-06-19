<?php

namespace AO\OIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AOOIBundle:Default:index.html.twig');
    }
}
