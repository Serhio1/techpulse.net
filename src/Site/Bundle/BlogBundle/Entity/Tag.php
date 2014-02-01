<?php

namespace Site\Bundle\BlogBundle\Entity;

use \FPN\TagBundle\Entity\Tag as BaseTag;
use Doctrine\ORM\Mapping as ORM;

/**
 * Site\Bundle\BlogBundle\Entity\Tag
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Tag extends BaseTag
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Tagging", mappedBy="tag", fetch="EAGER")
     **/
    protected $tagging;
}