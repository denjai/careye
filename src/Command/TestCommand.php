<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\CarRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    private CarRepository $carRepository;
    private $start;

    public function __construct(CarRepository $carRepository)
    {
        parent::__construct();
        $this->carRepository = $carRepository;
        $this->start = microtime(true);
    }

    protected function configure(): void
    {
        $this->setName('app:test');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);
        $car = $this->carRepository->findOneBy(['remoteId' => '11621334781951056']);
        $car = $this->carRepository->findOneBy(['remoteId' => '11647103910069075']);
        $car = $this->carRepository->findOneBy(['remoteId' => '11645979306816069']);
        var_dump($car);
        var_dump(microtime(true) - $start);
        var_dump(microtime(true) - $this->start);

        return 0;
    }
}