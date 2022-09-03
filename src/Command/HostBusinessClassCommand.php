<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(
    name: 'app:business-class',
    description: 'Set business class for hosts',
)]
class HostBusinessClassCommand extends Command
{


    protected EntityManagerInterface $em;
    protected static $defaultDescription = 'Set business class for hosts.
    This command will be executed every 1st day of the month.';

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ]
        $fromDate = $input->getArgument('fromDate');
        dd(gettype($fromDate));
        $toDate = $input->getArgument('toDate');
        $entityManager = $this->em;
        $query = $entityManager->createQuery
        (
            'SELECT h.id as hostId, COUNT(o.id) ordersCount, SUM(o.payablePrice) as totalSell
            FROM App\Entity\Host h, App\Entity\Order o
            INNER JOIN App\Entity\Event e WITH o.event = e.id
            INNER JOIN App\Entity\Experience ex WITH e.experience = ex.id
            WHERE h.id = ex.host AND (o.createdAt BETWEEN :fromDate AND :toDate) AND o.status = :status
            GROUP BY h.id ORDER BY totalSell DESC')
            ->setParameter('fromDate', $fromDate)
            ->setParameter('toDate', $toDate)
            ->setParameter('status', 'checkout');

        var_dump($query->getResult());
        return Command::SUCCESS;
    }


    protected function configure(): void
    {
        $this->setHelp('This command will be executed every 1st day of the month and will set business class for hosts.');
        $this->addArgument('fromDate', InputArgument::REQUIRED, 'From date')
            ->addArgument('toDate', InputArgument::REQUIRED, 'To date');
    }


}