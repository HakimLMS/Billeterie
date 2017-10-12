<?php

namespace P4\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('P4BookingBundle:Default:index.html.twig');
    }
}
