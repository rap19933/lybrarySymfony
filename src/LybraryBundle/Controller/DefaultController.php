<?php

namespace LybraryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('LybraryBundle:Default:index.html.twig');
    }
}
