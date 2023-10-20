<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    #[Route('/utilisateur/edition/{id}', name: 'user.edit',methods: ['GET','POST'])]
    public function edit(User $choosenUser,
                         Request $request,
                         EntityManagerInterface $manager,
                         UserPasswordHasherInterface $hasher
    ): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('security.login');
        }

        if ($this->getUser() !== $choosenUser)
        {
            return $this->redirectToRoute('recipe.index');
        }

        $form = $this->createForm(UserType::class,$choosenUser);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid())
        {
            if($hasher->isPasswordValid($choosenUser, $form->getData()->getPlainPassword())){
//                dd($form);

                $choosenUser=$form->getData();
                $manager->persist($choosenUser);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'les information son bien update'
                );
                return $this->redirectToRoute('recipe.index');
            }else{
                $this->addFlash(
                    'warning',
                    'Le mot de passe est incorrecte'
                );
            }
        }
        return $this->render('pages/user/edit.html.twig', [
            'form'=> $form->createView()
        ]);
    }
    #[Security("is_granted('ROLE_USER') and user === user.getUser()")]
    #[Route('/utilisateur/edition-mot-de-passe/{id}','user.edit.password',methods: ['GET','POST'])]
    public function editPassword(
        User $choosenUser,
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $hasher
    ): Response
    {

        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($choosenUser, $form->getData()['plainPassword'])) {
                $choosenUser->setUpdatedAt(new \DateTimeImmutable());

                $choosenUser->setPlainPassword(
                    $form->getData()['newPassword']
                );

                $manager->persist($choosenUser);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'les information son bien update'
                );
                return $this->redirectToRoute('recipe.index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe est incorrecte'
                );
            }
        }
        return $this->render('pages/user/edit_password.html.twig', [
            'form' => $form->createView()
        ]);
    }



}
