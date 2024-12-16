<?php
// src/Command/CreateUserCommand.php
namespace App\Command;

use App\Entity\User;
use App\Event\BeforeSendMailEvent;
use App\Event\UserCreatedEvent;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsCommand(name: 'app:create-user')]
class CreateUserCommand extends Command
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $eventDispatcher
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Crea un nuevo usuario.')
            ->setHelp('Este comando permite crear un usuario.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {


        // Obtener el helper de preguntas
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        // Preguntar por el correo electrónico
        $emailQuestion = new Question('Por favor, ingrese el correo electrónico: ');
        $email = $helper->ask($input, $output, $emailQuestion);

        // Verificar si el correo electrónico ya existe
        if ($this->userRepository->findOneBy(['email' => $email])) {
            $output->writeln('Error: El correo electrónico ya está registrado.');
            return Command::FAILURE;
        }

        // Preguntar por la contraseña
        $passwordQuestion = new Question('Por favor, ingrese la contraseña: ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);
        $password = $helper->ask($input, $output, $passwordQuestion);

        // Preguntar por el nombre
        $nameQuestion = new Question('Por favor, ingrese el nombre: ');
        $name = $helper->ask($input, $output, $nameQuestion);

        // Preguntar por el primer apellido
        $surname1Question = new Question('Por favor, ingrese el primer apellido: ');
        $surname1 = $helper->ask($input, $output, $surname1Question);

        // Preguntar por el segundo apellido
        $surname2Question = new Question('Por favor, ingrese el segundo apellido: ');
        $surname2 = $helper->ask($input, $output, $surname2Question);

        // Generar el nick
        $nick = substr($name, 0, 2) . substr($surname1, 0, 2) . substr($surname2, 0, 2);

        

        // Crear una nueva entidad User y asignarle los valores proporcionados
        $user = new User();
        $user->setEmail($email);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setNombre($name);
        $user->setApellido1($surname1);
        $user->setApellido2($surname2);
        $user->setIsActive(true); // is_active a 1 (true)
        $user->setRoles(['ROLE_ADMIN']); // Rol a ROLE_ADMIN

        // Persistir el nuevo usuario en la base de datos
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('Usuario creado con éxito.');
        $output->writeln('Correo electrónico: ' . $email);
        $output->writeln('Nombre: ' . $name);
        $output->writeln('Primer Apellido: ' . $surname1);
        $output->writeln('Segundo Apellido: ' . $surname2);

        return Command::SUCCESS;
    }
}