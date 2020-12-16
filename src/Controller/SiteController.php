<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
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

    /**
     * @Route("/compte-supprime", name="account_deleted")
     */
    public function accountDeleted(): Response
    {
        return $this->render('site/account_deleted.html.twig');
    }

    /**
     * @Route("/contact/{action}", name="contact")
     */
    public function contact(Request $request, MailerInterface $mailer, ?string $action = null): Response 
    {
        if(isset($request->request->get('contact')["object"])) {
            $action = $request->request->get('contact')["object"];
            dump($action);

        } else if($action == "reservation") {
            $action = 1;
        } else {
            $action = 0;
        }

        $form = $this->createForm(ContactType::class, ["object" => $action]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form-> isValid()) {
            $name = $form->get('name')->getData();
            $email = $form->get('email')->getData();
            $object = $form->get('object')->getData();
            $message = $form->get('message')->getData();

            if($object === 1) {
                $object = "Prise de rendez-vous";
            } else if($object == 2) {
                $object = "Demande de devis";
            } else {
                $object = "Erreur";
            }

            $date = $form->has('date') ? $form->get('date')->getData() : null;

            $msg = (new TemplatedEmail())
                ->from($email)
                ->to($_ENV['MAIL_SITE'])
                ->subject($object)
                ->textTemplate('mail/contact.txt.twig')
                ->context([
                    'name' =>$name,
                    'mail' => $email,
                    'message' => $message,
                    'date' => $date,
                ]);
                $mailer->send($msg);

                $this->addFlash('success', 'Votre message a bien été envoyé');
        }

        return $this->render('site/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function getSidebar (CategoryRepository $categoryRepository) {
        return $this->render('site/blog/_sidebar.html.twig',[
            'categories' => $categoryRepository->listCategories()
        ]);
    }
}
