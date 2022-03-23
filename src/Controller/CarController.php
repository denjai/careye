<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CarHistoryRepository;
use App\Repository\CarRepository;
use App\Services\CarSourceProvider;
use App\Services\CarManager;
use App\Services\SourceAwareCarClient;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CarController extends AbstractController
{
    private CarRepository $carRepository;
    private CarHistoryRepository $carHistoryRepository;
    private SourceAwareCarClient $carClient;
    private CarManager $carManager;
    private EntityManagerInterface $entityManager;
    private PaginatorInterface $paginator;
    private CarSourceProvider $carSourceProvider;

    public function __construct(
        CarRepository          $carRepository,
        CarHistoryRepository   $carHistoryRepository,
        SourceAwareCarClient   $carClient,
        CarManager             $carManager,
        EntityManagerInterface $entityManager,
        PaginatorInterface     $paginator,
        CarSourceProvider $carSourceProvider
    ) {
        $this->carRepository = $carRepository;
        $this->carHistoryRepository = $carHistoryRepository;
        $this->carClient = $carClient;
        $this->carManager = $carManager;
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
        $this->carSourceProvider = $carSourceProvider;
    }

    /**
     * @Route("/", methods={"GET"}, name="list-cars")
     */
    public function listCars(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $query = $this->carRepository
            ->createQueryBuilder('c')
            ->where('c.user = :user')
            ->setParameter('user', $this->getUser())
            ->orderBy('c.id', 'ASC')
        ;

        $cars = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('cars.html.twig', ['cars' => $cars]);
    }

    /**
     * @Route("/car/{id}", methods={"GET"}, name="car-info")
     */
    public function getCar(int $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $car = $this->carRepository->findOneBy(['id' => $id, 'user' => $this->getUser()]);
        $carHistory = $this->carHistoryRepository->findBy(['car' => $car]);

        return $this->render('car.html.twig', ['car' => $car, 'carHistory' => $carHistory]);
    }

    /**
     * @Route("car", methods={"POST"}, name="add-car")
     */
    public function addCar(Request $request): Response
    {
        $failed = false;
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $id = $request->request->get('id');

        try {
            $source = $this->carSourceProvider->getSource($id);
        } catch (InvalidArgumentException $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('list-cars');
        }


        if (preg_match('/\d{15,22}|[A-Za-z]+[\d@]+[\w@]*|[\d@]+[A-Za-z]+[\w@]*/', $id, $matches)) {
            $id = $matches[0];
        }

        try {
            $carInfo = $this->carClient->getCarInfo($id, $source);
            $car = $this->carRepository->findOneBy(['remoteId' => $id]);

            if ($car === null) {
                $this->carManager->createCar($carInfo, $this->getUser());
                $this->entityManager->flush();
            }
        } catch (Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            $failed =  true;
        }
        if (!$failed) {
            $this->addFlash('success', 'Successfully added.');
        }

        return $this->redirectToRoute('list-cars');
    }
}