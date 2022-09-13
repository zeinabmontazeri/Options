<?php

namespace App\Command;


use Predis\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Flex\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

#[AsCommand(
    name: 'flush-cache',
    description: 'Add a short description for your command',
)]
class FlushCacheCommand extends Command
{
    public function __construct(protected $redisHost ,private $redisPort,string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $redis = new Client([
            'host'   => $this->redisHost,
            'port'   => $this->redisPort
        ]);
        $io = new SymfonyStyle($input, $output);
        $redis->flushAll();

        $io->success('Cache has been flushed');

        return Command::SUCCESS;
    }
}
