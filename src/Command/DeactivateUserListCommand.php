<?php
// src/Command/DeactivateUserListCommand.php
namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;

#[AsCommand(name: 'app:deactivate-user-list')]
class DeactivateUserListCommand extends Command
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
        // Obtener todos los usuarios activos
        $activeUsers = $this->userRepository->findBy(['isActive' => true]);

        // Verificar si hay usuarios activos en la base de datos
        if (count($activeUsers) === 0) {
            $output->writeln('No hay usuarios activos registrados.');
            return Command::FAILURE;
        }

        // Mostrar lista de correos electrónicos de usuarios activos
        $output->writeln('Seleccione un usuario activo para desactivar:');
        
        $table = new Table($output);
        $table->setHeaders(['Número', 'Correo Electrónico']);
        
        foreach ($activeUsers as $index => $user) {
            $table->addRow([$index + 1, $user->getEmail()]);
        }

        $table->render();

        // Obtener el helper de preguntas
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        // Preguntar por el número del usuario a desactivar
        $numberQuestion = new Question('Por favor, ingrese el número del usuario que desea desactivar: ');
        $number = $helper->ask($input, $output, $numberQuestion);

        // Validar el número ingresado
        if (!is_numeric($number) || $number < 1 || $number > count($activeUsers)) {
            $output->writeln('Número inválido.');
            return Command::FAILURE;
        }

        // Obtener el usuario seleccionado
        $selectedUser = $activeUsers[$number - 1];
        $selectedEmail = $selectedUser->getEmail();

        // Confirmar el correo electrónico
        $confirmEmailQuestion = new Question('Por favor, vuelva a ingresar el correo electrónico para confirmar: ');
        $confirmEmail = $helper->ask($input, $output, $confirmEmailQuestion);

        // Verificar que los correos electrónicos coincidan
        if ($selectedEmail !== $confirmEmail) {
            $output->writeln('Error: Los correos electrónicos no coinciden.');
            return Command::FAILURE;
        }

        // Desactivar el usuario
        $selectedUser->setIsActive(false);

        // Guardar los cambios en la base de datos
        $this->entityManager->flush();

        $output->writeln('<fg=green>Usuario desactivado con éxito.</>');
        $output->writeln('Correo electrónico: ' . $selectedEmail);

        return Command::SUCCESS;
    }
}