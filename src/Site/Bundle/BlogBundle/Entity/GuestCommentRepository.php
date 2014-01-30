<?php

namespace Site\Bundle\BlogBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * GuestCommentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GuestCommentRepository extends EntityRepository
{
    public function findAll()
    {
        
        return $this->getEntityManager()
             ->createQueryBuilder()
             ->select('c')
             ->from('SiteBlogBundle:GuestComment','c')
             ->orderBy('c.date','DESC')
             ->getQuery();   
    }
    
    public function getLastComments()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT c FROM SiteBlogBundle:GuestComment c ORDER BY c.date DESC')
            ->setMaxResults(6)
            ->execute();
    }
}
