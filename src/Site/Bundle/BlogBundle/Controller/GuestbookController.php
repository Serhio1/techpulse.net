<?php

namespace Site\Bundle\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Adapter\DoctrineCollectionAdapter;
use Pagerfanta\Adapter\ArrayAdapter;
use Site\Bundle\BlogBundle\Entity\GuestComment;
use Site\Bundle\BlogBundle\Form\GuestCommentType;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



class GuestbookController extends Controller
{
    protected $template = 'SiteBlogBundle:Default:guestbook.html.twig';
    
    public function indexAction($page, Request $request)
    {
        $query = $this->getDoctrine()->getRepository('SiteBlogBundle:GuestComment')->findAll();
        $comments = $this->paginate($page,$query);
        
        $comment = new GuestComment();
        $form = $this->createForm(new GuestCommentType(), $comment, array(
            'action' => $this->generateUrl('guestbook'),
        ));
        $form->handleRequest($request);
        
       
        
        $template = $this->container->get('aje')->handleAjax();
        $this->template=($template)?$template:$this->template;

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $comment = $form->getData();
            $comment->setDateNow();
            
            $em->persist($comment);
            $em->flush();
            
            return $this->forward('SiteBlogBundle:Blog:index', array('page'=>1));
        }

        return $this->render($this->template,array('comments'=>$comments,
            'form'=>$form->createView(),
        ));
    }
    
    public function addCommentAction()
    {
        
        
        /*return $this->render('SiteBlogBundle:Default:add_post.html.twig',array('form'=>$form->createView(),
                      'sidebarData'=>$this->getSidebarData(),
                      ));*/
    }
    



#----------------------------------------------
public function paginate($page, $query)
    {
        $em = $this->getDoctrine()->getManager();
        
        $adapter = new DoctrineORMAdapter($query);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage(10);
        
        try {
            $comments = $pager->getCurrentPageResults();
        } catch (NotValidCurrentPageException $e) {
            
            return $this->render('SiteBlogBundle:Default:not_found.html.twig');
        }
        
        return $comments;
    }

}