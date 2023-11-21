<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'ingredient.index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(IngredientRepository $repository,
                          PaginatorInterface $paginator,
                          Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $repository->findBy(['user'=>$this->getUser()]), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients
        ]);
    }
    #[Route('/ingredient/creation', 'ingredient.new')]
    #[IsGranted('ROLE_USER')]
    public function new(
        Request $request,
        EntityManagerInterface $manager
    ): Response{

        $ingredient= new Ingredient();
        $form=$this->createForm(IngredientType::class, $ingredient);

//        dd($request->request);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $ingredient=$form->getData();
            $ingredient->setUser($this->getUser());
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès !'
            );

            return $this->redirectToRoute('ingredient.index');

        }
        return $this->render("pages/ingredient/new.html.twig",[
            'form' =>$form->createView()
        ]);
    }
//    grace    as SensioFrameworkExtraBundle plus besoin de faire :
//    public function edit(IngredientRepository $repository,int $id) :Response
//    {
//        $ingredient= $repository->findOneBy(["id"=>$id]);
//        $form = $this->createForm(IngredientType::class, $ingredient);
//
//        pour récupérer id si ça ne fonctionne pas il fait réinstall avec cette commande
// composer require sensio/framework-extra-bundle
    //#[Security("is_granted('ROLE_USER') and user === ingredient.getUser()")]
    #[Route('/ingredient/edition/{id}', 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(
        Ingredient $ingredient,
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        $form = $this->createForm(IngredientType::class, $ingredient,[
            'label_button' => 'Mettre à jour mon ingrédient',
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

            return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/ingredient/suppression/{id}', 'ingredient.delete', methods: ['GET'])]
    //#[Security("is_granted('ROLE_USER') and user === ingredient.getUser()")]
    public function delete(EntityManagerInterface $manager, Ingredient $ingredient):Response{
        $manager->remove($ingredient);
        $manager->flush();
        $this->addFlash(
            'success',
            'Votre ingrédient a été modifié avec succès !'
        );
        return $this->redirectToRoute('ingredient.index');
    }
}

