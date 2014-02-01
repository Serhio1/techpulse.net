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
        $tagManager = $this->get('fpn_tag.tag_manager');
        //return new Response('<br><br><br><br><br><br><br>works<br><br><br><br><br><br><br><br>');

        $posts = $this->paginate($page,$query)
                ->getCurrentPageResults($page);
        foreach($posts as $post){
            $tagManager->loadTagging($post);
            /*foreach($tag as $post->getTags()){
                //echo $tag->getName();
            }*/
        }
        

        $request = Request::createFromGlobals();
        if ($request->isXmlHttpRequest()){
            
            return $this->ajaxProcessor(array('posts'=>$posts));
        }
        
        return $this->render('SiteBlogBundle:Default:index.html.twig',array('posts'=>$posts,
                  'sidebarData'=>$this->getSidebarData(),
                  'AjEPaginator'=>'true',
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

        /*if ($request->isXmlHttpRequest()){

            return $this->ajaxProcessor(array('form'=>$form->createView()));
        }*/
        
        if ($form->isValid()) {
            $newPost = $form->getData();
            $newPost->setLikes(0);
            $newPost->setViews(0);
            $newPost->setDateNow();
            $tagManager = $this
                           ->get('fpn_tag.tag_manager');
            $tags = $form->get('tags')->getData();
            $tags=explode(',',$tags);
            
            foreach ($tags as $tag){
                $tag = $tagManager
                        ->loadOrCreateTag($tag);
                $tagManager->addTag($tag, $newPost);
            }
            
            //return new Response('<br><br><br><br><br><br><br>'.print_r($tags).'ololoolo<br><br><br><br><br><br>');
            /*$tag = $tagManager
                        ->loadOrCreateTag($tags);
            $tagManager->addTag($tag, $newPost);*/
//-------------------------------------------------- 
           /* foreach ($tags as $tag) {
                $tag = $tagManager
                        ->loadOrCreateTag($tag);
                $tagManager->addTag($tag, $newPost);
            }*/
//--------------------------------------------------
            
            
            
            $em = $this->getDoctrine()
                       ->getEntityManager();
            $em->persist($newPost);
            $em->flush();
            $tagManager->saveTagging($newPost);
            $tagManager->loadTagging($post);
            
            return $this->redirect($this->generateUrl('homepage', array(
                    'sidebarData'=>$this->getSidebarData()
                    )));
        }
        
        return $this->render('SiteBlogBundle:Default:add_post.html.twig',array('form'=>$form->createView(),
                      
                      ));
    }
    
    
    function searchAction($searchStr=Null, $page)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('SiteBlogBundle:Post')->search($searchStr);
        $posts = $this->paginate($page,$query);
        
        $request = Request::createFromGlobals();
        if ($request->isXmlHttpRequest()){
            return $this->ajaxProcessor(array('posts'=>$posts,
                'AjEPaginator'=>'true'));
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
                return $this->render('SiteBlogBundle:Default:AjE_template.html.twig',array('posts'=>$data['posts'],'AjEPaginator'=>'true'));
                
            case'search':
                return $this->render('SiteBlogBundle:Default:AjE_template.html.twig',array('posts'=>$data['posts'],'AjEPaginator'=>'true'));
            
#-------------------- for AjEMenu ------------------
            case'Action2':
                return $this->render('SiteBlogBundle:Default:add_post.html.twig',array('form'=>$data['form'],'AjEPaginator'=>'false'));
            case'Action3':
                return $this->render('SiteBlogBundle:Default:add_post.html.twig',array('form'=>$data['form']));    
#-------------------- for AjEMenu ------------------                
                
            default: 
                
                return $this->render('SiteBlogBundle:Default:not_found.html.twig');
            }
    }
    
    
}
