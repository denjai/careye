<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CarHistoryRepository;
use App\Repository\CarRepository;
use App\Services\CarClient;
use App\Services\CarResultTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CarController extends AbstractController
{
    private CarRepository $carRepository;
    private CarHistoryRepository $carHistoryRepository;
    private CarClient $carClient;
    private CarResultTransformer $carResultTransformer;
    private EntityManagerInterface $entityManager;

    public function __construct(
        CarRepository $carRepository,
        CarHistoryRepository $carHistoryRepository,
        CarClient $carClient,
        CarResultTransformer $carResultTransformer,
        EntityManagerInterface $entityManager
    ) {
        $this->carRepository = $carRepository;
        $this->carHistoryRepository = $carHistoryRepository;
        $this->carClient = $carClient;
        $this->carResultTransformer = $carResultTransformer;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", methods={"GET"}, name="list-cars")
     */
    public function listCars(): Response
    {
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

    /**
     * @Route("car", methods={"POST"}, name="add-car")
     */
    public function addCar(Request $request): Response
    {
        $id = $request->request->get('id');

        if (preg_match('/\d{15,22}/', $id, $matches)) {
            $id = $matches[0];
        }

        try {
            $carInfo = $this->carClient->getCarInfo($id);
            $car = $this->carRepository->findOneBy(['remoteId' => $id]);

            if ($car === null) {
                $car = $this->carResultTransformer
                    ->transform($carInfo)
                    ->setRemoteId($id)
                ;
                $this->entityManager->persist($car);
                $this->entityManager->flush();
            }
        } catch (Exception $exception) {
            return new Response($exception->getMessage());
            //TODO show error message on page
            // $this->session->getFlashBag()->add('error', 'user.logout');
        }

        return $this->redirectToRoute('list-cars');
    }
}