<?php

namespace App\Controller;

use App\Repository\CheckpointRepository;
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
use Symfony\Component\Serializer\SerializerInterface;

class CheckpointController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var CheckpointRepository
     */
    private $checkpointRepository;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @Route("/checkpoint", name="checkpoint")
     */
    public function index(): Response
    {
        return $this->render('checkpoint/index.html.twig', [
            'controller_name' => 'CheckpointController',
        ]);
    }

    public function __construct(EntityManagerInterface $manager, CheckpointRepository $checkpointRepository, SerializerInterface $serializer, UserRepository $userRepository)
    {
        $this->manager = $manager;
        $this->checkpointRepository = $checkpointRepository;
        $this->serializer = $serializer;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route(name="getBlockedCheckpoint", path="/api/checkpoint/blocked")
     */
    public function getBlockedCheckpoint() {
        if ($this->isGranted('ROLE_GACCES')) {
            $checkpoint = $this->checkpointRepository->findByBlocked(true);
            return $this->json($checkpoint, Response::HTTP_OK);
        }else {
            return $this->json('Access Denied');
        }
    }

    /**
     * @Route(name="blockCheckpoint", path="/api/checkpoint/{id}/block")
     */
    public function blockCheckpoint($id) {
        if ($this->isGranted('ROLE_GACCES')) {
            $checkpoint = $this->checkpointRepository->findOneBy(['id' => $id]);
            if ($checkpoint->getBlocked() === false) {
                $checkpoint->setBlocked(true);
                $this->manager->persist($checkpoint);
                $this->manager->flush();
                return $this->json('Checkpoint locked');
            }else {
                return $this->json('Checkpoint already locked');
            }
        }else {
            return $this->json('Access Denied');
        }
    }

    /**
     * @Route(name="unblockCheckpoint", path="/api/checkpoint/{id}/unblock")
     */
    public function unblockCheckpoint($id) {
        if ($this->isGranted('ROLE_GACCES')) {
            $checkpoint = $this->checkpointRepository->findOneBy(['id' => $id]);
            if ($checkpoint->getBlocked() === true) {
                $checkpoint->setBlocked(false);
                $this->manager->persist($checkpoint);
                $this->manager->flush();
                return $this->json('Checkpoint unlocked');
            }else {
                return $this->json('Checkpoint not locked');
            }
        }else {
            return $this->json('Access Denied');
        }
    }

    /**
     * @Route(name="assignControleur", path="/api/assign/controleur", methods={"POST"})
     */
    public function assignControleur(Request $request, CheckpointRepository $checkpointRepository) {
        if ($this->isGranted('ROLE_GACCES')) {
            $assignTab = $request->getContent();
            $assignJson = $this->serializer->decode($assignTab, 'json');
            $user = $this->userRepository->findOneBy(['id' => $assignJson['user']]);
            $checkpoint = $checkpointRepository->findOneBy(['id' => $assignJson['checkpoint']]);
            if ($user && $user->getRoles()[0] === 'ROLE_CONTROLEUR') {
                if ($checkpoint) {
                    $assignJson['user'] = '/api/user/'.$assignJson['user'];
                    $assignJson['checkpoint'] = 'api/checkpoint/'.$assignJson['checkpoint'].'/list';
                    $assign = $this->serializer->denormalize($assignJson, 'App\Entity\Assignment');
                    $user->setCheckpoint($checkpoint);

                    $this->manager->persist($assign);
                    $this->manager->flush();

                    return $this->json('CONTROLEUR assigned');
                }else {
                    return $this->json('Choose a checkpoint');
                }
            }else {
                return $this->json('Choose CONTROLEUR');
            }
            dd($user->getRoles());
        }else {
            return $this->json('Access Denied');
        }
    }
}
