<?php

namespace App\Command;

use App\Repository\HostRepository;
use App\Repository\OrderRepository;
use App\Service\Host\HostBusinessClassService;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(name: 'app:host:update-business-class')]
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
        $currentDateTime = date('Y-m-d H:i:s');
        $this->hostBusinessClassService->setBusinessClass(
            $this->hostRepository,
            $this->orderRepository,
            $currentDateTime
        );
        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->setDescription('This command runs daily to update host business class')
            ->setHelp('First parameter is fromDate and second is toDate. Input date range in format YYYY-MM-DD.'
                . "\n" .
                'Example: symfony console app:business-class');
    }

}