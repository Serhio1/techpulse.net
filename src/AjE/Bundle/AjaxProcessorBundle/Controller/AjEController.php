<?php

namespace AjE\Bundle\AjaxProcessorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Symfony\Component\HttpFoundation\Request;

class AjEController extends Controller
{
    public function handleAjax()
    {
        $request = Request::createFromGlobals();
        if(!$request->isXmlHttpRequest()){ 
            return;
        }
        $code = $request->request->get('code');
        
        
        
        switch ($code){
            case'home':
                
                return 'SiteBlogBundle:Default:AjE_template.html.twig';
                
            case'search':
                
                return 'SiteBlogBundle:Default:AjE_template.html.twig';
                
            case'single_post':
                
                return 'SiteBlogBundle:Default:single_post.html.twig';
            
#-------------------- for AjEMenu ------------------
            case'Главная':
                
                return 'SiteBlogBundle:Default:AjE_template.html.twig';
                
            case'Добавить пост':
                
                return 'SiteBlogBundle:Default:add_post.html.twig';
            
            case'Гостевая книга':
                
                return 'SiteBlogBundle:Default:guestbook.html.twig';
            
            case'Войти':
                
                return 'SiteUserBundle:Default:login.html.twig';
#-------------------- for AjEMenu ------------------   
            default: 
                
                return 'SiteBlogBundle:Default:not_found.html.twig';
            }
    }
}
