<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    #[Route('/recipe', name: 'recipe.index')]
    public function index(RecipeRepository $repository,PaginatorInterface $paginator, Request $request): Response
    {

        $recipes = $paginator->paginate(
            $repository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }
    #[Route('/recipe/nouveau', name: 'recipe.new', methods: ['GET','POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $manager
    ): Response{

        $recipe= new Recipe();
//        $ingredient = new Ingredient();
        $form=$this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $recipe=$form->getData();
            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été modifié avec succès !'
            );

            return $this->redirectToRoute('recipe.index');

        }
        return $this->render("pages/recipe/new.html.twig",[
            'form' =>$form->createView()
        ]);
    }
//    grace    as SensioFrameworkExtraBundle plus besoin de faire :
//    public function edit(RecipeRepository $repository,int $id) :Response
//    {
//        $recipe= $repository->findOneBy(["id"=>$id]);
//        $form = $this->createForm(RecipeType::class, $recipe);
//
//        pour récupérer id si ça ne fonctionne pas il fait réinstall avec cette commande
// composer require sensio/framework-extra-bundle
    #[Route('/recipe/edition/{id}', 'recipe.edit', methods: ['GET','POST'])]
    public function edit(
        Recipe $recipe,
        Request $request,
        EntityManagerInterface $manager
    ): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
//            updateAt

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été modifié avec succès !'
            );

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/recipe/suppression/{id}','recipe.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Recipe $recipe):Response{
        $manager->remove($recipe);
        $manager->flush();
        $this->addFlash(
            'success',
            'Votre ingrédient a été modifié avec succès !'
        );
        return $this->redirectToRoute('recipe.index');
    }
}
