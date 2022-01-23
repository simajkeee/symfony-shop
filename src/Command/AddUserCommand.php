<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

class AddUserCommand extends Command
{
    protected static $defaultName = 'app:add-user';
    protected static $defaultDescription = 'Add user command';

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addOption('email', 'm', InputOption::VALUE_REQUIRED, 'User email')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'User password')
            ->addOption('isAdmin', 'a', InputOption::VALUE_OPTIONAL, 'User is admin', '0')
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

        $successMsg = sprintf("User with id: %d and email: %s has been successfully created", 123, $email);
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
}
