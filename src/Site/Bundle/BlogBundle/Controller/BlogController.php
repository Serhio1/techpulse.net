<?php

namespace Site\Bundle\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Site\Bundle\BlogBundle\Entity\Post;
use Site\Bundle\BlogBundle\Form\PostType;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Adapter\DoctrineCollectionAdapter;
use Pagerfanta\Adapter\ArrayAdapter;


class BlogController extends Controller
{
    
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $this->getDoctrine()->getRepository('SiteBlogBundle:Post')->findAll();
 
        
        
        
        $posts = $this->paginate($page,$query)
                ->getCurrentPageResults($page);

        $request = Request::createFromGlobals();
        if ($request->isXmlHttpRequest()){

            return $this->ajaxProcessor(array('posts'=>$posts));
        }
        
        return $this->render('SiteBlogBundle:Default:index.html.twig',array('posts'=>$posts,
                  'sidebarData'=>$this->getSidebarData()
                  ));
    }
    
    
    public function postByIdAction($id)
    {
        $post = $this->getDoctrine()->getRepository('SiteBlogBundle:Post')->find($id);
        $post->setViews($post->getViews()+1);
        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();
        
        return $this->render('SiteBlogBundle:Default:single_post.html.twig',array(
            'post'=>$post,
            'sidebarData'=>$this->getSidebarData()
             ));
    }
    
    
    public function addPostAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(new PostType(), $post);
        $form->handleRequest($request);
        if ($form->isValid()) {
            
            $post = $form->getData();
            $post->setLikes(0);
            $post->setViews(0);
            $post->setDateNow();
            
            $em->persist($post);
            $em->flush();
            return $this->redirect($this->generateUrl('single_post', array(
                    'id' => $post->getId(),
                    'sidebarData'=>$this->getSidebarData()
                    )));
        }
        
        return $this->render('SiteBlogBundle:Default:add_post.html.twig',array('form'=>$form->createView(),
                      'sidebarData'=>$this->getSidebarData(),
                      ));
    }
    
    
    function searchAction($searchStr=Null, $page)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('SiteBlogBundle:Post')->search($searchStr);
        $posts = $this->paginate($page,$query);
        
        $request = Request::createFromGlobals();
        if ($request->isXmlHttpRequest()){
            return $this->ajaxProcessor(array('posts'=>$posts));
        }
        
        return $this->render('SiteBlogBundle:Default:index.html.twig',array(
                'posts'=>$posts,
                'sidebarData'=>$this->getSidebarData(),
                ));
    }
    
//---------------------------------------------------    
    
    function getSidebarData()
    {
        $em = $this->getDoctrine()->getManager();
        $lastArticles = $em->getRepository('SiteBlogBundle:Post')->getLastArticles();
        $mostViewed = $em->getRepository('SiteBlogBundle:Post')->getMostViewed();
        $lastGuestComments = $em->getRepository('SiteBlogBundle:GuestComment')->getLastComments();
        
        return array(
            'lastArticles'=>$lastArticles,
            'mostViewed'=>$mostViewed,
            'guestComments'=>$lastGuestComments,
        );
    }
    
    function paginate($page, $query)
    {
        $em = $this->getDoctrine()->getManager();
        $adapter = new DoctrineORMAdapter($query);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage(10);
        $pager->setCurrentPage($page);
        try {
            $posts = $pager->getCurrentPageResults($page);
        } catch (NotValidCurrentPageException $e) {
            
            return $this->render('SiteBlogBundle:Default:not_found.html.twig');
        }
        
        return $pager;
    }
    
//------------------------------------------------
    
    public function ajaxProcessor($data)
    {
        $request = Request::createFromGlobals();
        $code = $request->request->get('code');
        
        switch ($code){
            case'home':
                
                return $this->render('SiteBlogBundle:Default:AjE_template.html.twig',array('posts'=>$data['posts']));
                
            case'search':
                return $this->render('SiteBlogBundle:Default:AjE_template.html.twig',array('posts'=>$data['posts']));
                
            default: 
                
                return $this->render('SiteBlogBundle:Default:not_found.html.twig');
            }
    }
    
    
}
