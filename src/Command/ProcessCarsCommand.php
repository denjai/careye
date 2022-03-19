<?php
declare(strict_types=1);

namespace App\Command;

use App\Exception\CarAdvertDeletedException;
use App\Repository\CarRepository;
use App\Services\CarClient;
use App\Services\CarManager;
use App\Services\CarResultTransformer;
use App\Services\LoginClient;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessCarsCommand extends Command
{
    private CarClient $carClient;
    private EntityManagerInterface $entityManager;
    private CarRepository $carRepository;
    private CarManager $carManager;
    private LoggerInterface $logger;

    public function __construct(
        CarClient $carClient,
        EntityManagerInterface $entityManager,
        CarRepository $carRepository,
        CarManager $carManager,
        LoggerInterface $logger
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

        foreach ($this->carRepository->getAllIterableResult() as $car) {
            try {
                $carInfo = $this->carClient->getCarInfo($car->getRemoteId());
            } catch (InvalidArgumentException $exception) {
                continue;
            } catch (CarAdvertDeletedException $carAdvertDeletedException) {
                //TODO change status in DB
            }

            $this->carManager->updatePrice($car, $carInfo);
            $this->carManager->updateDates($car, $carInfo);
        }

        $this->entityManager->flush();
        return 0;
    }
}