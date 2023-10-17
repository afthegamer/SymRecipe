<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/utilisater/edition/{id}', name: 'user.edit')]
    public function edit(User $user,Request $request,EntityManagerInterface $manager): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('security.login');
        }

        if ($this->getUser() !== $user)
        {
            return $this->redirectToRoute('recipe.index');
        }

        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid())
        {
            $user=$form->getData();
            $manager->flush();

            $this->addFlash(
                'sucess',
                'les infor mation son bien update'
            );
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('pages/user/edit.html.twig', [
            'form'=> $form->createView()
        ]);
    }
}
