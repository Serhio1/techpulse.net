<?php

namespace Site\Bundle\BlogBundle\DataFixtures;

use Site\Bundle\BlogBundle\Entity\Post,
    Doctrine\Common\DataFixtures\FixtureInterface,
    Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\Persistence\ObjectManager,
    Symfony\Component\Yaml\Yaml,
    Doctrine\Common\Collections\ArrayCollection;

class LoadPostData extends AbstractFixture implements FixtureInterface
{
    /**
    * Load data fixtures with the passed EntityManager
    *
    * @param ObjectManager $manager
    */
    public function load(ObjectManager $manager)
    {
        $posts = Yaml::parse($this->getYmlFile());
        
        print_r($posts);

        foreach ($posts['posts'] as $post) {
            $postObject = new Post();

            $postObject->setTitle($post['title']);
            $postObject->setText($post['text']);
            $postObject->setAuthorId($post['authorId']);
            $postObject->setLikes(0);
            $postObject->setViews(0);
            $postObject->setDate(new \DateTime('now'));

            //foreach ($post['categories'] as $reference) {
                //$postObject->addCategory($this->getReferencesFromArray($post['categories']));
            //}
            
           // $this->setTags($post['categories']);

            $manager->persist($postObject);
        }

        $manager->flush();
    }
    
    /**
    * Get the order of this fixture
    *
    * @return integer
    */
    public function getOrder()
    {
        return 2;
    }

    protected function getYmlFile()
    {
        return __DIR__ . '/Data/posts.yml';
    }
}