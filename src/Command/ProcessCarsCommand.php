<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\Car;
use App\Exception\CarInfoServerException;
use App\Repository\CarRepository;
use App\Services\MobileClient;
use App\Services\CarManager;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessCarsCommand extends Command
{
    private MobileClient $carClient;
    private EntityManagerInterface $entityManager;
    private CarRepository $carRepository;
    private CarManager $carManager;
    private LoggerInterface $logger;

    public function __construct(
        MobileClient           $carClient,
        EntityManagerInterface $entityManager,
        CarRepository          $carRepository,
        CarManager             $carManager,
        LoggerInterface        $logger
    ) {
        parent::__construct();

        $this->carClient = $carClient;
        $this->entityManager = $entityManager;
        $this->carRepository = $carRepository;
        $this->carManager = $carManager;
        $this->logger = $logger;
    }

    protected function configure(): void
    {
        $this->setName('app:process-cars');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->info('Processing cars...');
        $output->writeln('Processing cars...');

        foreach ($this->carRepository->getAllActiveIterableResult() as $car) {
            try {
                $carInfo = $this->carClient->getCarInfo($car->getRemoteId());
            } catch (InvalidArgumentException $exception) {
                $this->carManager->changeStatus($car, Car::STATUS_CLOSED);
                continue;
            } catch (CarInfoServerException $serverException) {
                continue;
            }

            $this->carManager->updatePrice($car, $carInfo);
            $this->carManager->updateDates($car, $carInfo);
        }

        $this->entityManager->flush();
        return 0;
    }
}