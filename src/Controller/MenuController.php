<?php

namespace App\Controller;

use App\Entity\FoodPointing;
use App\Repository\CarteRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class MenuController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var MenuRepository
     */
    private $menuRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var CarteRepository
     */
    private $carteRepository;

    /**
     * @Route("/menu", name="menu")
     */
    public function index(): Response
    {
        return $this->render('menu/index.html.twig', [
            'controller_name' => 'MenuController',
        ]);
    }
    
    public function __construct(EntityManagerInterface $manager, MenuRepository $menuRepository, UserRepository $userRepository, SerializerInterface $serializer, CarteRepository $carteRepository) {
        $this->manager = $manager;
        $this->menuRepository = $menuRepository;
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
        $this->carteRepository = $carteRepository;
    }

    /**
     * @Route(name="addFoodPointing", path="/api/foodPointing/add", methods={"POST"})
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function addFoodPointing(Request $request) {
        if ($this->isGranted('ROLE_ACCEPTEUR')) {
            $foodTab = $request->getContent();
            $foodPointingJson = $this->serializer->decode($foodTab, 'json');
            $carte = $this->carteRepository->findOneBy(['idCarte' => $foodPointingJson['idCarte']]);
            $user = $carte->getUser();
            $menu = $this->menuRepository->findOneBy(['libelle' => $foodPointingJson['menu']]);
            if ($carte->getBlocked() !== true) {
                $foodPrice = $menu->getPrice();
                if ($carte->getSolde() < $foodPrice) {
                    return $this->json('not enought money');
                }else {
                    $carte->setSolde($carte->getSolde() - $foodPrice);
                    $foodPointing = $this->serializer->denormalize($foodPointingJson, 'App\Entity\FoodPointing');
                    $foodPointing->setUser($user)
                                 ->setMenu($menu)
                                 ->setPointDate(new \DateTime('now'));

                    $this->manager->persist($foodPointing);
                    $this->manager->persist($carte);
                    $this->manager->flush();

                    return $this->json('Successful');
                }
            }else {
                return $this->json('Blocked card');
            }
        }else {
            return $this->json('Access Denied');
        }
    }
}
