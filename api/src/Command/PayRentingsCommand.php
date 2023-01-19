<?php

namespace App\Command;

use App\Repository\RentingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'PayRentingsCommand',
    description: 'Execute this command to pay rentings',
)]
class PayRentingsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $manager,
        private RentingRepository $rentingRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Execute this command to pay rentings')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $rentings = $this->rentingRepository->findRentingsToPay();

            foreach ($rentings as $renting) {
                $renting->setStatus(true);
                $owner = $renting->getHousing()->getOwner();
                $owner->setBalance($owner->getBalance() + $renting->getPrice());

                $this->manager->persist($renting);
                $this->manager->persist($owner);
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            return Command::FAILURE;
        }
    }
}
