<?php

namespace Site\Bundle\BlogBundle\Entity;

use Doctrine\ORM\EntityRepository;


class PostRepository extends EntityRepository
{
    
    public function findAll()
    {
        
        return $this->getEntityManager()
             ->createQueryBuilder()
             ->select('p')
             ->from('SiteBlogBundle:Post','p')
             ->orderBy('p.date','DESC')
             ->getQuery();   
    }
    
    public function search($searchStr)
    {
        return $this
            ->createQueryBuilder('p')
            ->where('p.title LIKE :search')
            ->orWhere('p.text LIKE :search')
            ->setParameter(':search', '%' . $searchStr . '%');

    }
    
    public function getLastArticles()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT p FROM SiteBlogBundle:Post p ORDER BY p.date DESC')
            ->setMaxResults(6)
            ->execute();
    }
    
    public function getMostViewed()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT p FROM SiteBlogBundle:Post p ORDER BY p.views DESC')
            ->setMaxResults(6)
            ->execute();
    }
    
    
}
