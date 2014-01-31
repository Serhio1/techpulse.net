<?php

namespace Site\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SiteUserBundle:Default:index.html.twig', array('name' => $name));
    }
}
