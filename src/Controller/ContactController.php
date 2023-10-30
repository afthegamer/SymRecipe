<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
#[Route('/contact')]
class ContactController extends AbstractController
{
    #[Route('/', name: 'contact.index',methods: ['GET','POST'])]
    public function index(Request $request, EntityManagerInterface $manager,MailerInterface $mailer): Response
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

            $email = (new TemplatedEmail())
                ->from($contact->getEmail())
                ->to('admin@recipe.com')
                ->subject($contact->getSubject())

                // path of the Twig template to render
                ->htmlTemplate('emails//contact.html.twig')

                // pass variables (name => value) to the template
                ->context([
                    'contact'=>$contact
                ]);
//                ->html($contact->getMessage());

            $mailer->send($email);
//            dd($mailer);
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
