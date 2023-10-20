<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Cache\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;

class RecipeController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/recette', name: 'recipe.index', methods: ['GET'])]
    public function index(RecipeRepository $repository,PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $repository->findBy(['user'=>$this->getUser()]), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recette/communaute', 'recipe.community', methods: ['GET'])]
    public function indexPublic(
        RecipeRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $recipes = $paginator->paginate(
            $repository->findPublicRecipe(null),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/recipe/community.html.twig', [
            'recipes' => $recipes
        ]);
    }


//    #[IsGranted('ROLE_USER')]
    #[Security("is_granted('ROLE_USER') and (recipe.getIsPublic() === true || user === recipe.getUser())")]
    #[Route('/recette/{id}', 'recipe.show', methods: ['GET', 'POST'])]
    public function show(
        Recipe $recipe,
        Request $request,
        EntityManagerInterface $manager
    ): Response
    {
        return $this->render('pages/recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/recette/creation', 'recipe.new', methods: ['GET', 'POST'])]
    public function new(
                Request $request,
                EntityManagerInterface $manager
        ):Response
        {
            $recipe = new Recipe();
            $form =$this->createForm(RecipeType::class,$recipe);
            $form->handleRequest($request);
            if($form->isSubmitted()&&$form->isValid())
            {
                $recipe=$form->getData();
                $recipe->setUser($this->getUser());
                $manager->persist($recipe);
                $manager->flush();
                $this->addFlash(
                    'success',
                    'Votre recette a bien était Créer'
                );
                return $this->redirectToRoute('recipe.index');

            }
            return $this->render('pages/recipe/new.html.twig',[
                'form'=>$form->createView()
            ]);
        }
    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
    #[Route('/recette/edition/{id}', 'recipe.edit', methods: ['GET', 'POST'])]
    public function edit(
        Recipe $recipe,
        Request $request,
        EntityManagerInterface $manager
    ): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();

            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès !'
            );

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
    #[Route('/recette/{id}', 'recipe.delete', methods: ['GET', 'POST'])]
    public function delete(EntityManagerInterface $manager, Recipe $recipe):Response{
        $manager->remove($recipe);
        $manager->flush();
        $this->addFlash(
            'success',
            'Votre recette a été supprimer avec succès !'
        );
        return $this->redirectToRoute('recipe.index');
    }
}
