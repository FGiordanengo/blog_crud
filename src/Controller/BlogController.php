<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ArticleRepository $repo)
    {
        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/article/new", name="article_new")
     * @Route("/article/{id}/edit", name="article_edit")
     */
    public function form(Article $article = null, Request $request, EntityManagerInterface  $manager) 
    {
        if(!$article) {
            $article = new Article();
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if(!$article->getId()) {
                $article->setCreatedAt(new \DateTime());
            }
            
            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }


    /**
     * @Route("/article/{id}", name="article_show")
     */
    public function show(Article $article)
    {
        
        return $this->render('blog/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/article/delete/{id}", name="article_delete")
     */
    public function delete(Article $article, EntityManagerInterface $manager)
    {
        $manager->remove($article);
        $manager->flush();

        return $this->redirectToRoute("home");
    }
}
