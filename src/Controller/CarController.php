<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CarHistoryRepository;
use App\Repository\CarRepository;
use App\Services\CarClient;
use App\Services\CarResultTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
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
    private PaginatorInterface $paginator;

    public function __construct(
        CarRepository $carRepository,
        CarHistoryRepository $carHistoryRepository,
        CarClient $carClient,
        CarResultTransformer $carResultTransformer,
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator
    ) {
        $this->carRepository = $carRepository;
        $this->carHistoryRepository = $carHistoryRepository;
        $this->carClient = $carClient;
        $this->carResultTransformer = $carResultTransformer;
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
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
                    ->setUser($this->getUser())
                ;
                $this->entityManager->persist($car);
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