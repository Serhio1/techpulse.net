<?php

namespace AjE\Bundle\AjaxProcessorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AjEAjaxProcessorBundle:Default:index.html.twig', array('name' => $name));
    }
}
