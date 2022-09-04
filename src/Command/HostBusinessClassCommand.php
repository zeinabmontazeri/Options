<?php

namespace App\Command;

use App\Repository\HostRepository;
use App\Repository\OrderRepository;
use App\Service\Host\HostBusinessClassService;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(name: 'app:business-class')]
class HostBusinessClassCommand extends Command
{

    protected HostBusinessClassService $hostBusinessClassService;
    protected HostRepository $hostRepository;
    protected OrderRepository $orderRepository;

    public function __construct(
        HostBusinessClassService $hostBusinessClassService,
        HostRepository           $hostRepository,
        OrderRepository          $orderRepository
    )
    {
        $this->hostBusinessClassService = $hostBusinessClassService;
        $this->hostRepository = $hostRepository;
        $this->orderRepository = $orderRepository;
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->hostBusinessClassService->setBusinessClass(
            $this->hostRepository,
            $this->orderRepository,
            $input->getArgument('fromDate'),
            $input->getArgument('toDate')
        );
        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->setDescription('This command allows you to set business class for hosts.')
            ->setHelp('First parameter is fromDate and second is toDate. Input date range in format YYYY-MM-DD.'
                . "\n" .
                'Date range cannot be more than 62 days.'
                . "\n" .
                'Example: symfony console app:business-class 2021-01-01 2021-03-01')
            ->addArgument('fromDate', InputArgument::REQUIRED, 'From date')
            ->addArgument('toDate', InputArgument::REQUIRED, 'To date');
    }

}