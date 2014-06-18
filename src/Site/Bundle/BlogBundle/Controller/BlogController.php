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
    protected $template = 'SiteBlogBundle:Default:index.html.twig';

    
    public function indexAction($page)
    {
        $query = $this->getDoctrine()->getRepository('SiteBlogBundle:Post')
                ->findAllQuery();
        $posts = $this->paginate($page,$query)
                 ->getCurrentPageResults($page);
        $template = $this->container->get('aje')->handleAjax();
        
        $sidebarData = $this->getSidebarData();
        
        for ($i=0; $i<4; $i++) {
            $mostViewedHead[$i] = $sidebarData['mostViewed'][$i];
        }

        
        
        /*$postsText = array();
        $images = array();
        foreach ($posts as $post) {

            $images[$post->getTitle()] = $post->getText();
            $imgPos = strripos($post->getText(), '<img src="');
            $imgSrcEnd = strripos($post->getText(), '">', $imgPos+10);
            $imgSrc = substr($post->getText(),$imgPos+10,$imgSrcEnd);
           // echo '<img src="'.$imgSrc;
            //$imgSrc = $imgPos
        }*/
        
        
        $this->getTags($posts);
        $this->template=($template)?$template:$this->template;
        
        return $this->render($this->template,array(
            'posts'=>$posts,
            'sidebarData'=>$sidebarData,
            'mostViewedHead'=>$mostViewedHead
        ));
    }
    
    
    public function postByIdAction($id)
    {
        $post = $this->getDoctrine()->getRepository('SiteBlogBundle:Post')->find($id);
        $post->setViews($post->getViews()+1);
        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();
        $template = $this->container->get('aje')->handleAjax();
        $this->template=($template)?$template:$this->template;
        $this->getTags(array($post));

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
        $template = $this->container->get('aje')->handleAjax();
        $this->template=($template)?$template:$this->template;
        if ($form->isValid()) {
            $newPost = $form->getData();
            $tagManager = $this
                           ->get('fpn_tag.tag_manager');
            $tags = $form->get('tags')->getData();
            $newPost = $this->tagsProcessor($tags, $newPost);
            $em = $this->getDoctrine()
                       ->getEntityManager();
            $em->persist($newPost);
            $em->flush();
            $tagManager->saveTagging($newPost);
            
            return $this->redirect($this->generateUrl('homepage'));
        }
        
        return $this->render($this->template,array(
            'form'=>$form->createView(),
            'sidebarData'=>$this->getSidebarData(),
        ));
    }
    
    
    public function searchAction($searchStr=Null, $page)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('SiteBlogBundle:Post')->search($searchStr);
        $posts = $this->paginate($page,$query);
        $template = $this->container->get('aje')->handleAjax();
        $this->template=($template)?$template:$this->template;
        
        return $this->render($this->template,array(
            'posts'=>$posts,
            'sidebarData'=>$this->getSidebarData(),
        ));
    }
    
//---------------------------------------------------    
    
    public function getSidebarData()
    {
        $em = $this->getDoctrine()->getManager();
        $lastArticles = $em->getRepository('SiteBlogBundle:Post')
                ->getLastArticles();
        $mostViewed = $em->getRepository('SiteBlogBundle:Post')
                ->getMostViewed();
        $lastGuestComments = $em->getRepository('SiteBlogBundle:GuestComment')
                ->getLastComments();
        
        return array(
            'lastArticles'=>$lastArticles,
            'mostViewed'=>$mostViewed,
            'guestComments'=>$lastGuestComments,
        );
    }
    
    private function paginate($page, $query)
    {
        $em = $this->getDoctrine()->getManager();
        $adapter = new DoctrineORMAdapter($query);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($this->container->getParameter('posts_per_page'));
        $pager->setCurrentPage($page);
        try {
            $posts = $pager->getCurrentPageResults($page);
        } catch (NotValidCurrentPageException $e) {
            
            return $this->render('SiteBlogBundle:Default:not_found.html.twig');
        }
        
        return $pager;
    }
    
    private function tagsProcessor($tags, $newPost)
    {
        $tagManager = $this->get('fpn_tag.tag_manager');
        $tags=explode(',',$tags);
        
        foreach ($tags as $tag){
            $tag = $tagManager->loadOrCreateTag($tag);
            $tagManager->addTag($tag, $newPost);
        }
        
        return $newPost;
    } 
    
    protected function getTags($posts)
    {
        $tagManager = $this->get('fpn_tag.tag_manager');
        foreach($posts as $post){
            $tagManager->loadTagging($post);
        }
    }

}