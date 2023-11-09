<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/contact')]
class ContactController extends AbstractController
{
    #[Route('/', name: 'contact.index',methods: ['GET','POST'])]
    public function index(
        Request $request,
        EntityManagerInterface $manager,
        MailService $mailService
    ): Response
    {
        $contact=new Contact();
        if ($this->getUser()) {
            $contact->setFullName($this->getUser()->getFullName())
                ->setEmail($this->getUser()->getEmail());
        }
        $form = $this->createForm(ContactType::class,$contact);
        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid()){
            $contact=$form->getData();
            $manager->persist($contact);
            $manager->flush();

            $mailService->sendEmail(
                $contact->getEmail(),
                'admin@recipe.com',
                $contact->getSubject(),
                'emails/contact.html.twig',
                ['contact'=>$contact]
            );


            $this->addFlash(
                'success',
                'Votre demande a été envoyé avec succès !'
            );

            return $this->redirectToRoute('contact.index');
        }
        return $this->render('pages/contact/index.html.twig', [
            'form'=> $form->createView(),
            'controller_name' => 'ContactController',
        ]);
    }
}
