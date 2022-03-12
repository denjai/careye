<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CarHistoryRepository;
use App\Repository\CarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\Routing\Annotation\Route;

class CarController extends AbstractController
{
    private CarRepository $carRepository;
    private CarHistoryRepository $carHistoryRepository;

    public function __construct(CarRepository $carRepository, CarHistoryRepository $carHistoryRepository)
    {
        $this->carRepository = $carRepository;
        $this->carHistoryRepository = $carHistoryRepository;
    }

    /**
     * @Route("/", methods={"GET"})
     */
    public function listCars(?Profiler $profiler): Response
    {
        if (null !== $profiler) {
            // if it exists, disable the profiler for this particular controller action
            $profiler->disable();
        }

        $cars = $this->carRepository->findAll();
        //TODO add pagination
        return $this->render('cars.html.twig', ['cars' => $cars]);
    }

    /**
     * @Route("/car/{id}", methods={"GET"}, name="car-info")
     */
    public function getCar(int $id): Response
    {
        $car = $this->carRepository->find($id);
        $carHistory = $this->carHistoryRepository->findBy(['car' => $car]);

        return $this->render('car.html.twig', ['car' => $car, 'carHistory' => $carHistory]);
    }
}