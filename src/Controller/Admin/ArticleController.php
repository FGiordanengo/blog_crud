<?php
 
namespace App\Controller\Admin;
 
use App\Entity\Article;
use App\Form\Admin\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Stof\DoctrineExtensionsBundle\Uploadable\UploadableManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 
/**
 * @Route("/admin/article", name ="admin_article_")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ArticleRepository $repo, Request $request, PaginatorInterface $paginator)
    {
        $articles = $paginator->paginate(
            $repo->findAll(),
            $request->query->get('page', 1),
            10
        );
 
        return $this->render('admin/article/index.html.twig', [
            'articles' => $articles,
        ]);
    }
 
    /**
     * @Route("/ajouter", name="new")
     * @Route("/{id}/modifier", name="edit")
     */
    public function form(Request $request, EntityManagerInterface $manager, UploadableManager $uploadableManager, Article $article = null) 
    {
        if(!$article) {
            $article = new Article();
            $msg = "L'article a bien été ajouté";
        }
 
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
 
        if($form->isSubmitted() && $form->isValid()) {
            if(!$article->getId()) {
                $article->setCreatedAt(new \DateTime());
            }

            if($article->getFile()) {
                $uploadableManager->markEntityToUpload($article, $article->getFile());
            }
            
            $manager->persist($article);
            $manager->flush();

            if(!isset($msg)) {
                $msg = "L'article a bien été modifié";
            }

            $this->addFlash('success', $msg);
 
            return $this->redirectToRoute('admin_article_show', ['id' => $article->getId()]);
        }
 
        return $this->render('admin/article/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }
 
    /**
     * @Route("/{id}", name="show")
     */
    public function show(Article $article)
    {
        
        return $this->render('admin/article/show.html.twig', [
            'article' => $article,
        ]);
    }
 
    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Article $article, EntityManagerInterface $manager)
    {
        $manager->remove($article);
        $manager->flush();
 
        return $this->redirectToRoute("admin_article_index");
    }
}