<?php

namespace Site\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function loginAction()
    {
        return $this->redirect($this->generateUrl('login'));
        //return $this->redirect($controller);//$this->render('FOSUserBundle:Security:login.html.twig');
    }
}
