<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route(name="site_")
 */
class SiteController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('site/index.html.twig');
    }

    /**
     * @Route("/a-propos", name="about")
     */
    public function about(): Response
    {
        return $this->render('site/about.html.twig');
    }

    /**
     * @Route("/blog", name="blog")
     */
    public function blog(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request) : Response
    {
 
        $articles= $paginator->paginate(
            $articleRepository->findAllPublished(),
            $request->query->get('page', 1),
            6
        );
        // $articles = $articleRepository->findAll();
 
        return $this->render('site/blog/blog.html.twig',[
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/blog/categorie/{slug}", name="blog_category")
     */
    public function category(CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request, $slug) : Response
    {
        $category = $categoryRepository->detailedCategory($slug);

        if(!$category) {
            throw $this->createNotFoundException('Catégorie introuvable');   
        }
        $articles= $paginator->paginate(
            $category->getArticles(),
            $request->query->get('page', 1),
            6
        );
 
        return $this->render('site/blog/category.html.twig',[
            'articles' => $articles,
            'category' => $category
        ]);
    }

    /**
     * @Route("/blog/{slug}", name="blog_show")
     */
    public function blogShow(ArticleRepository $articleRepository, $slug) : Response
    {
        $article = $articleRepository->detailedArticle($slug);
        if(!$article) {
            throw $this->createNotFoundException('Article introuvable');   
        }

        return $this->render('site/blog/show.html.twig',[
            'article' => $article
        ]);
    }

    public function getSidebar (CategoryRepository $categoryRepository) {
        return $this->render('site/blog/_sidebar.html.twig',[
            'categories' => $categoryRepository->listCategories()
        ]);
    }
}
