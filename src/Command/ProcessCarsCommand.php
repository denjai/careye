<?php
declare(strict_types=1);

namespace App\Command;

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
    private LoginClient $loginClient;
    private CarClient $carClient;
    private array $cardCheckList;
    private EntityManagerInterface $entityManager;
    private CarRepository $carRepository;
    private CarResultTransformer $carResultTransformer;
    private CarManager $carManager;
    private LoggerInterface $logger;

    public function __construct(
        LoginClient $loginClient,
        CarClient $carClient,
        array $cardCheckList,
        EntityManagerInterface $entityManager,
        CarRepository $carRepository,
        CarResultTransformer $carResultTransformer,
        CarManager $carManager,
        LoggerInterface $logger
    ) {
        parent::__construct();

        $this->loginClient = $loginClient;
        $this->carClient = $carClient;
        $this->cardCheckList = $cardCheckList;
        $this->entityManager = $entityManager;
        $this->carRepository = $carRepository;
        $this->carResultTransformer = $carResultTransformer;
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

        foreach ($this->cardCheckList as $carId) {
            try {
                $carInfo = $this->carClient->getCarInfo($carId);
            } catch (InvalidArgumentException $exception) {
                continue;
            }

            $car = $this->carRepository->findOneBy(['remoteId' => $carId]);

            if ($car !== null) {
                $this->carManager->updatePrice($car, $carInfo);
                $this->carManager->updateDates($car, $carInfo);
            } else {
                $car = $this->carResultTransformer
                    ->transform($carInfo)
                    ->setRemoteId($carId)
                ;
                $this->entityManager->persist($car);
            }
        }

        $this->entityManager->flush();
        return 0;
    }
}