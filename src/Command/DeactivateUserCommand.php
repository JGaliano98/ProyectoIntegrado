<?php
// src/Command/DeactivateUserCommand.php
namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\QuestionHelper;

#[AsCommand(name: 'app:deactivate-user')]
class DeactivateUserCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Desactiva un usuario.')
            ->setHelp('Este comando permite desactivar un usuario estableciendo el atributo is_active a 0.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Obtener el helper de preguntas
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        // Preguntar por el correo electrónico
        $emailQuestion = new Question('Por favor, ingrese el correo electrónico del usuario a desactivar: ');
        $email = $helper->ask($input, $output, $emailQuestion);

        // Confirmar el correo electrónico
        $confirmEmailQuestion = new Question('Por favor, vuelva a ingresar el correo electrónico para confirmar: ');
        $confirmEmail = $helper->ask($input, $output, $confirmEmailQuestion);

        // Verificar que los correos electrónicos coincidan
        if ($email !== $confirmEmail) {
            $output->writeln('Error: Los correos electrónicos no coinciden.');
            return Command::FAILURE;
        }

        // Buscar el usuario por correo electrónico
        $user = $this->userRepository->findOneBy(['email' => $email]);

        // Verificar si el usuario existe
        if (!$user) {
            $output->writeln('Error: No se encontró un usuario con ese correo electrónico.');
            return Command::FAILURE;
        }

        // Desactivar el usuario
        $user->setIsActive(false);

        // Guardar los cambios en la base de datos
        $this->entityManager->flush();

        $output->writeln('<fg=green>Usuario desactivado con éxito.</>');

        return Command::SUCCESS;
    }
}