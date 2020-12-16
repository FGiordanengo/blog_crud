<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        
        for($c = 1; $c <=3; $c++) {
            $category = (new Category)
                    ->setTitle($faker->sentence)
                    ->setDescription($faker->paragraph(3));

            $this->addReference('category' . $c, $category);
            $manager->persist($category);
        }
        for($a = 1; $a <= 20; $a++) {
            $article = (new Article())
                    ->setTitle($faker->sentence)
                    ->setContent($faker->paragraph(6))
                    ->setImage($faker->imageUrl())
                    ->setSlug($faker->sentence)
                    ->setIsActive($faker->boolean)
                    ->setCreatedAt($faker->dateTimeBetween("-6 months"));
            
            $category = $this->getReference('category'.mt_rand(1,3));
            $article->setCategory($category);

            $this->addReference('article' . $a, $article);

            $manager->persist($article);
        }
        
                
        for($k = 1; $k <= mt_rand(4, 10); $k++) {
            $comment = (new Comment())
                    ->setAuthor($faker->name)
                    ->setContent($faker->paragraph(2))
                    ->setCreatedAt($faker->dateTimeBetween("-6 months"));
            
            $article = $this->getReference('article'.mt_rand(1,20));
            $comment->setArticle($article);

            $manager->persist($comment);

        }
        
        $manager->flush();
    }
}