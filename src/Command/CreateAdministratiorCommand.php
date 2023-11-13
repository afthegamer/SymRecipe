<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Create an administrator',
)]
class CreateAdministratiorCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct('app:create-administrator');

        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('full_name', InputArgument::OPTIONAL, 'Full Name')
            ->addArgument('email', InputArgument::OPTIONAL, 'Email')
            ->addArgument('password', InputArgument::OPTIONAL, 'Password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        // Fonction utilitaire pour poser une question
        $askQuestion = function ($message, $default) use ($input, $output, $helper) {
            if (!$default) {
                $question = new Question($message);
                return $helper->ask($input, $output, $question);
            }
            return $default;
        };

        // Poser des questions si nécessaire
        $fullName = $askQuestion('Quel est le nom de l\'administrateur : ', $input->getArgument('full_name'));
        $email = $askQuestion('Quel est l\'email de ' . $fullName . ' : ', $input->getArgument('email'));
        $plainPassword = $askQuestion('Quel est le mot de passe de ' . $fullName . ' : ', $input->getArgument('password'));

        // Création de l'utilisateur
        $user = (new User())->setFullName($fullName)
            ->setEmail($email)
            ->setPlainPassword($plainPassword)
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

        // Persistance de l'utilisateur
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Le nouvel administrateur a été créé !');

        return Command::SUCCESS;
    }
}
