<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\ArticleType;




class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(ArticleRepository $repository): Response
    {
       // $repository = $this->getDoctrine()->getRepository(Article::class);
        $article = $repository -> findAll();
     
       
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $article
           
        ]);

    }


    /**
     * @Route("/",name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig',);
    }


    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article=null,Request $request, EntityManagerInterface $manager ){
        //$article = new Article();

        if(!$article)
        {
            $article = new Article();
        }
      /*  $form = $this-> createFormBuilder($article)
                     -> add('title',TextType::class,[
                        'attr' => ['placeholder' => 'entrer le titre par ici']
                        
                     ])
                     -> add('content',TextareaType::class, [
                        'attr' => ['placeholder' => 'contenu de article']
                        
                    ])
                     -> add('image', TextType::class, [
                        'attr' => ['placeholder' => 'entrer une image par ici']
                        
                     ])
                     -> getForm();*/

        $form = $this->createForm(ArticleType::class,$article); 

        $form -> handleRequest($request);

        if($form-> isSubmitted() && $form->isValid()){
            if(!$article->getId())
            {
                $article->setCreatedAt(new \DateTime());
            }
            

            $manager->persist($article);
            $manager-> flush();

            return $this-> redirectToRoute('blog_show',['id'=> $article->getId()]);
        }
        dump($article);

        return $this->render('blog/create.html.twig',[
            'formArticle'=> $form->createView(),
            'editMode' => $article->getId()!= null     
        ]);
            
    }


    /**
     * @Route("/blog/{id}",name="blog_show")
     */
    public function show(ArticleRepository $repo,$id){

      //  $repo = $this->getDoctrine()->getRepository(Article::class);
        $article = $repo->find($id);

        return $this->render('blog/show.html.twig',[
            'article'=> $article
        ]);

    }

    


}
