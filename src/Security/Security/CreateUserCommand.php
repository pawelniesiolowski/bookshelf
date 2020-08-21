<?php

namespace App\Security\Security;

use App\Security\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';
    private $entityManager;
    private $passwordEncoder;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        string $name = null
    ) {
        parent::__construct($name);
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you create new user with specific role.')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('surname', InputArgument::REQUIRED)
            ->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
            ->addArgument('roles', InputArgument::IS_ARRAY)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'User Creator',
            '============',
            '',
        ]);
        $name = $input->getArgument('name');
        $surname = $input->getArgument('surname');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $roles = $input->getArgument('roles');
        if (!UserRolesDictionary::areValid($roles)) {
            $output->writeln('Invalid roles given.');
            return 1;
        }
        $output->writeln('Name: ' . $name);
        $output->writeln('Surname: ' . $surname);
        $output->writeln('Email: ' . $email);
        $output->writeln('Roles: ' . implode(', ', $roles));
        $user = new User($email, $password, $name, $surname);
        $user->changePassword($this->passwordEncoder->encodePassword($user, $password));
        $user->setRoles($roles);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return 0;
    }
}
