<?php

namespace App\Command;

use App\Service\MixRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:talk-to-me',
    description: 'Add a short description for your command',
)]
class TalkToMeCommand extends Command
{
    public function __construct(
        private MixRepository $mixRepository
    )
    {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::OPTIONAL, 'Your name')
            ->addOption('yell', null, InputOption::VALUE_NONE, 'Shall I yell?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name') ?: 'whoever you are';
        $shouldYell = $input->getOption('yell');
        $message = sprintf('Hey %s!', $name);
        if ($shouldYell) {
            $message = strtoupper($message);
        }
        $io->success($message);
        if ($io->confirm('Do you want a mix recommendation?')) {
            $mixes = $this->mixRepository->findAll();
            $mix = $mixes[array_rand($mixes)];
            $io->note('I recommend the mix: ' . $mix['title']);
        }
        return Command::SUCCESS;
    }
}
