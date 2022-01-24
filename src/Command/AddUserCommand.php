<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class AddUserCommand extends Command
{
    protected static $defaultName = 'app:add-user';
    protected static $defaultDescription = 'Add user command';
    /**
     * @var ManagerRegistry
     */
    private $registry;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(ManagerRegistry $registry, UserPasswordEncoderInterface $encoder, string $name = null)
    {
        parent::__construct($name);
        $this->registry = $registry;
        $this->encoder = $encoder;
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addOption('email', 'm', InputOption::VALUE_REQUIRED, 'User email')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'User password')
            ->addOption('isAdmin', 'a', InputOption::VALUE_OPTIONAL, 'User is admin')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('add-user-command');
        $io = new SymfonyStyle($input, $output);
        $email = $input->getOption('email');
        $pwd = $input->getOption('password');
        $isAdmin = $input->getOption('isAdmin');

        $io->title('Please provide with user related data');
        if (!$email) {
            $email = $io->ask('Enter user email');
        }

        if (!$pwd) {
            $pwd = $io->askHidden('Enter user password');
        }

        if (!$isAdmin) {
            $isAdmin = $io->ask('Is User admin? 0 or 1');
            $isAdmin = boolval($isAdmin);
        }

        try {
            $user = $this->createUser($email, $pwd, $isAdmin);
        } catch (RuntimeCommandException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $successMsg = sprintf("User with id: %d and email: %s has been successfully created", $user->getId(), $email);
        $io->success($successMsg);
        $event = $stopwatch->stop('add-user-command');
        $eventMsg = sprintf(
            "The event \"%s\" lasted for %.2f milliseconds and used %.2fMB of memory",
            $event->getName(),
            $event->getDuration(),
            $event->getMemory() / 1024 / 1024
        );
        $io->info($eventMsg);

        return Command::SUCCESS;
    }

    private function createUser(string $email, string $pwd, bool $isAdmin): User
    {
        $userRepository = $this->registry->getRepository(User::class);
        $entityManager = $this->registry->getManager();
        $user = $userRepository->findOneBy(['email' => $email]);
        if ($user) {
            throw new RuntimeCommandException(sprintf("User with email %s already exists.", $email));
        }
        $user = new User();
        $user->setEmail($email);
        $user->setRoles([$isAdmin ? "ROLE_ADMIN" : "ROLE_USER"]);
        $user->setIsVerified(true);
        $encodedPassword = $this->encoder->encodePassword($user, $pwd);
        $user->setPassword($encodedPassword);
        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }
}
