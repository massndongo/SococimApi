<?php

namespace App\Controller;

use App\Entity\Pointing;
use App\Repository\CarteRepository;
use App\Repository\PointingRepository;
use App\Repository\ProfilRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class PointingController extends AbstractController
{
    /**
     * @var PointingRepository
     */
    private $pointingRepository;
    /**
     * @var DenormalizerInterface
     */
    private $serializer;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var CarteRepository
     */
    private $carteRepository;

    /**
     * @Route("/pointing", name="pointing")
     */
    public function index(): Response
    {
        return $this->render('pointing/index.html.twig', [
            'controller_name' => 'PointingController',
        ]);
    }

    public function __construct(DenormalizerInterface $serializer, EntityManagerInterface $manager, UserRepository $userRepository, CarteRepository $carteRepository, PointingRepository $pointingRepository)
    {
        $this->serializer = $serializer;
        $this->manager = $manager;
        $this->userRepository = $userRepository;
        $this->carteRepository = $carteRepository;
        $this->pointingRepository = $pointingRepository;
    }

    /**
     * @Route(name="employePointing", path="/api/employe/{carte}/pointing", methods={"POST"})
     */
    public function employePointing(Request $request, $carte) {
        if ($this->isGranted('ROLE_CONTROLEUR')) {
            $carte = $this->carteRepository->findOneBy(['idCarte' => $carte]);
            $user = $this->getUser();
            $point = $carte->getPointings()->last();
            if ($point) {
                if ($point->getDateSortie() === null) {
                    return $this->json('Please unpoint on last checkpoint');
                }
            }
            $pointing = new Pointing();
            $pointing->setCarte($carte)
                     ->setDateEntree(new \DateTime('now'))
                     ->setCheckpoint($user->getCheckpoint())
                     ->setUser($user);

            $this->manager->persist($pointing);
            $this->manager->flush();

            return $this->json('Successful pointing');
        }else {
            return $this->json('Access Denied');
        }
    }

    /**
     * @Route(name="employeunPointing", path="/api/employe/{carte}/unpointing", methods={"PUT"})
     */
    public function employeunPointing(Request $request, $carte) {
        if ($this->isGranted('ROLE_CONTROLEUR')) {
            $carte = $this->carteRepository->findOneBy(['idCarte' => $carte]);
            $user = $this->getUser();
            $pointing = $carte->getPointings()->last();
            if ($pointing->getDateSortie() === null) {
                $pointing->setDateSortie(new \DateTime('now'));

                $this->manager->persist($pointing);
                $this->manager->flush();

                return $this->json('unpointing successful');
            }else {
                return $this->json('Card already unpointed');
            }
        }else {
            return $this->json('Access Denied');
        }
    }

    /**
     * @Route(name="visitorPointing", path="/api/pointing/visitor", methods={"POST"})
     */
    public function visitorPointing() {

    }
}
