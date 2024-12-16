<?php
// src/Command/ActivateUserListCommand.php
namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'app:activate-user-list',
    description: 'Activa un usuario.',
    hidden: false,
    aliases: ['app:enable-user']
)]
class ActivateUserListCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Activa un usuario.')
            ->setHelp('Este comando permite activar un usuario estableciendo el atributo is_active a 1.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Obtener usuarios inactivos
        $inactiveUsers = $this->userRepository->findBy(['isActive' => false]);

        if (empty($inactiveUsers)) {
            $output->writeln('<comment>No hay usuarios inactivos registrados.</comment>');
            return Command::FAILURE;
        }

        // Mostrar tabla de usuarios inactivos
        $output->writeln('<info>Seleccione un usuario inactivo para activar:</info>');
        $table = new Table($output);
        $table->setHeaders(['Número', 'Correo Electrónico']);

        foreach ($inactiveUsers as $index => $user) {
            $table->addRow([$index + 1, $user->getEmail()]);
        }

        $table->render();

        // Obtener el helper para preguntar al usuario
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        // Pregunta para seleccionar un usuario
        $numberQuestion = new Question('Por favor, ingrese el número del usuario que desea activar: ');
        $numberQuestion->setValidator(function ($value) use ($inactiveUsers) {
            if (!is_numeric($value) || $value < 1 || $value > count($inactiveUsers)) {
                throw new \RuntimeException('Número inválido. Por favor, ingrese un número válido.');
            }
            return (int) $value;
        });
        $numberQuestion->setMaxAttempts(3); // Reintenta si la entrada es inválida

        // Preguntar al usuario
        $number = $helper->ask($input, $output, $numberQuestion);
        $selectedUser = $inactiveUsers[$number - 1];

        // Pregunta de confirmación
        $confirmQuestion = new ChoiceQuestion(
            '¿Está seguro de que desea activar este usuario? (Si/No)',
            ['Si', 'No'],
            1 // Por defecto, "No"
        );
        $confirmQuestion->setNormalizer(fn ($value) => ucfirst(strtolower($value))); // Normaliza la entrada

        $confirmation = $helper->ask($input, $output, $confirmQuestion);

        if ($confirmation !== 'Si') {
            $output->writeln('<comment>Activación cancelada.</comment>');
            return Command::SUCCESS;
        }

        // Activar el usuario seleccionado
        $selectedUser->setIsActive(true);
        $this->entityManager->flush();

        $output->writeln('<fg=green>Usuario activado con éxito:</>');
        $output->writeln('<info>Correo Electrónico: ' . $selectedUser->getEmail() . '</info>');

        return Command::SUCCESS;
    }
}
