<?php

namespace App\Controller;

use App\Entity\Carte;
use App\Repository\CarteRepository;
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

class CarteController extends AbstractController
{
    /**
     * @var UserController
     */
    private $userController;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordHasherInterface
     */
    private $encoder;
    /**
     * @var ProfilRepository
     */
    private $profilRepository;
    /**
     * @var DenormalizerInterface
     */
    private $serializer;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var UserService
     */
    private $userService;
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var CarteRepository
     */
    private $carteRepository;

    /**
     * @Route("/carte", name="carte")
     */
    public function index(): Response
    {
        return $this->render('carte/index.html.twig', [
            'controller_name' => 'CarteController',
        ]);
    }

    public function __construct(MailerInterface $mailer, UserPasswordHasherInterface $encoder, DenormalizerInterface $serializer, ProfilRepository $profilRepository, EntityManagerInterface $manager, UserService $userService, UserRepository $userRepository, UserController $userController, CarteRepository $carteRepository)
    {
        $this->encoder = $encoder;
        $this->serializer = $serializer;
        $this->profilRepository = $profilRepository;
        $this->manager = $manager;
        $this->userService = $userService;
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
        $this->userController = $userController;
        $this->carteRepository = $carteRepository;
    }

    /**
     * @Route(name="addCard", path="/api/card/add", methods={"POST"})
     */
    public function addCard(Request $request) {
        if ($this->isGranted('ROLE_GCARTE')|| $this->isGranted('ROLE_PARTENAIRE')) {
            $user = $request->getContent();
            
            dd($userTab);
            foreach ($userTab as $key => $value) {
                $value = $this->serializer->decode($userTab, 'json');
                $mat = 'SC'.$this->userController->random().date('Y').$this->userController->random1().substr($userTab['phone'], -3);
                $password = $this->userController->random().$this->userController->random1();
                $profil = $this->profilRepository->findOneBy(['libelle' => 'EMPLOYE']);
                $userTab['profil'] = '/api/profils/'.$profil->getId();
                $user = $this->serializer->denormalize($userTab, 'App\Entity\User');
                $user->setMat($mat)
                    ->setPassword($this->encoder->hashPassword($user, $password));

                $this->manager->persist($user);

                $carte = new Carte();
                $idCarte = 'M'.$this->userController->random1().'SC'.$this->userController->random();
                $numCompte = date('si').$this->userController->random2().date('mY');
                $carte->setIdCarte($idCarte)
                    ->setNumCarte($numCompte)
                    ->setUser($user);

                $this->manager->persist($carte);
                $this->manager->flush();
            }
            $uploadedfile = $request->files->get('avatar');
            // dd($uploadedfile);
            // if ($uploadedfile) {
            //     $file = $uploadedfile->getRealPath();
            //     return $uploadedfile;
            //     $avatar = fopen($file, 'r+');
            //     $userTab['avatar'] = $avatar;
            //     return $this->json('Veuillez choisir une image svp');
            // }else
            // {
            //     $file = $uploadedfile->getRealPath();
            //     $avatar = fopen($file, 'r+');
            //     $userTab['avatar'] = $avatar;
            // }


            // $emailFrom = 'sococim216@gmail.com';
            // $emailTo = 'rahmane961@gmail.com';
            // $email = $this->userService->email($user->getEmail(), $password, $emailFrom, $emailTo);
            // $this->mailer->send($email);

            return $this->json('Carte activée avec succès');
        }else {
            return $this->json('Access Denied');
        }
    }

    /**
     * @Route(name="reloadCardByPhone", path="/api/reload/phone/{phone}", methods={"PUT"})
     */
    public function reloadCardByPhone(Request $request, $phone) {
        if ($this->isGranted('ROLE_GCARTE')|| $this->isGranted('ROLE_PARTENAIRE')) {
            $reloadTab = $request->getContent();
            $reload = $this->serializer->decode($reloadTab, 'json');
            $user = $this->userRepository->findOneBy(['phone' => $phone]);
            $carte = $user->getCarte();
            if ($carte) {
                if ($reload['montant'] === '' || $reload['montant'] === null) {
                    return $this->json('Montant required');
                }else {
                    if ($carte->getBlocked() === true) {
                        return $this->json('Blocked card cannot reload');
                    }else {
                        $carte->setSolde($carte->getSolde() + $reload['montant']);
                        $this->manager->persist($carte);
                        $this->manager->flush();
                        return $this->json('Reload successfull');
                    }
                }
            }else {
                return $this->json('Card doesn\'t exist');
            }
        } else {
            return $this->json('Acces Denied');
        }
    }

    /**
     * @Route(name="reloadCardByCard", path="/api/reload/card/{card}", methods={"PUT"})
     */
    public function reloadCardByCard(Request $request, $card) {
        if ($this->isGranted('ROLE_GCARTE')|| $this->isGranted('ROLE_PARTENAIRE')) {
            $reloadTab = $request->getContent();
            $reload = $this->serializer->decode($reloadTab, 'json');
            $carte = $this->carteRepository->findOneBy(['numCarte' => $card]);
            if ($carte) {
                if ($reload['montant'] === '' || $reload['montant'] === null) {
                    return $this->json('Montant required');
                }else {
                    if ($carte->getBlocked() === true) {
                        return $this->json('Blocked card cannot reload');
                    }else {
                        $carte->setSolde($carte->getSolde() + $reload['montant']);
                        $this->manager->persist($carte);
                        $this->manager->flush();
                        return $this->json('Reload successfull');
                    }
                }
            }else {
                return $this->json('Card doesn\'t exist');
            }
        } else {
            return $this->json('Acces Denied');
        }
    }

    /**
     * @Route(name="blockCard", path="/api/card/{id}/block", methods={"PUT"})
     */
    public function blockCard($id) {
        if ($this->isGranted('ROLE_GCARTE')|| $this->isGranted('ROLE_PARTENAIRE')) {
            $carte = $this->carteRepository->findOneBy(['id' => $id]);
            if ($carte->getBlocked() === false) {
                $carte->setBlocked(true);
                $this->manager->persist($carte);
                $this->manager->flush();

                return $this->json('Carte bloquée');
            }else {
                return $this->json('Carte dejà bloquée');
            }
        }else {
            return $this->json('Acces Denied');
        }
    }

    /**
     * @Route(name="unblockCard", path="/api/card/{id}/unblock", methods={"PUT"})
     */
    public function unblockCard($id) {
        if ($this->isGranted('ROLE_GCARTE')|| $this->isGranted('ROLE_PARTENAIRE')) {
            $carte = $this->carteRepository->findOneBy(['id' => $id]);
            if ($carte->getBlocked() === true) {
                $carte->setBlocked(false);
                $this->manager->persist($carte);
                $this->manager->flush();

                return $this->json('Carte débloquée');
            }else {
                return $this->json('Cette Carte n\'est pas bloquée ');
            }
        }else {
            return $this->json('Acces Denied');
        }
    }
}
