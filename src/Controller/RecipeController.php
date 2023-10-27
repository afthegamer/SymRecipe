<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Form\MarkType;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
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

#[Route('/recette')]
class RecipeController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/', name: 'recipe.index', methods: ['GET'])]
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

    #[Route('/communaute', 'recipe.community', methods: ['GET'])]
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

    #[IsGranted('ROLE_USER')]
    #[Route('/creation', 'recipe.new', methods: ['GET', 'POST'])]
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
    #[Route('/edition/{id}', 'recipe.edit', methods: ['GET', 'POST'])]
    public function edit(
        Recipe $recipe,
        Request $request,
        EntityManagerInterface $manager
    ): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe,[
            'label_button'=>'Mettre a jour ma recette'
        ]);
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
    #[Security("is_granted('ROLE_USER') and (recipe.getIsPublic() === true || user === recipe.getUser())")]
    #[Route('/{id}', 'recipe.show', methods: ['GET','POST'])]
    public function show(
        Recipe $recipe,
        MarkRepository $markRepository,
        Request $request,
        EntityManagerInterface $manager
    ): Response
    {
        $mark =new Mark();
        $form = $this->createForm(MarkType::class,$mark);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mark->setUsers($this->getUser())
                ->setRecipe($recipe)
            ;

            $existingMark= $markRepository->findOneBy([
                'users'=>$this->getUser(),
                'recipe'=>$recipe
            ]);

            if (!$existingMark) {
                $manager->persist($mark);
            } else {
                $existingMark->setMark(
                    $form->getData()->getMark()
                );
            }



            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès !'
            );

            $manager->flush();
            return $this->redirectToRoute('recipe.show',['id'=>$recipe->getId()]);
        }
        return $this->render('pages/recipe/show.html.twig', [
            'recipe' => $recipe,
            'form'=>$form->createView()
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
