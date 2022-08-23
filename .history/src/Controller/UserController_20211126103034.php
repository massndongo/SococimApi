<?php

namespace App\Controller;

use App\Repository\ProfilRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @var UserPasswordHasherInterface
     */
    private $encoder;
    /**
     * @var DenormalizerInterface
     */
    private $serializer;
    /**
     * @var ProfilRepository
     */
    private $profilRepository;
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
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    public function __construct(MailerInterface $mailer, UserPasswordHasherInterface $encoder, DenormalizerInterface $serializer, ProfilRepository $profilRepository, EntityManagerInterface $manager, UserService $userService, UserRepository $userRepository)
    {
        $this->encoder = $encoder;
        $this->serializer = $serializer;
        $this->profilRepository = $profilRepository;
        $this->manager = $manager;
        $this->userService = $userService;
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route(name="addUser", path="/api/user/add", methods={"POST"})
     * @throws TransportExceptionInterface|ExceptionInterface
     */
    public function addUser(Request $request) {

        $userTab = $request->request->all();
        $uploadedfile = $request->files->get('avatar');
        if ($uploadedfile === null) {
            return $this->json('Veuillez choisir une image svp');
        }else
        {
            $file = $uploadedfile->getRealPath();
            $avatar = fopen($file, 'r+');
            $userTab['avatar'] = $avatar;
        }
        $profilLibelle = $userTab['profil'];
        $mat = 'SC'.$this->random().date('Y').$this->random1().substr($userTab['phone'], -3);
        $password = "passer";
        $profil = $this->profilRepository->findOneBy(['libelle' => $userTab['profil']]);
        $userTab['profil'] = '/api/profils/'.$profil->getId();
        $user = $this->serializer->denormalize($userTab, 'App\Entity\User');
        $user->setMat($mat)
             ->setPassword($this->encoder->hashPassword($user, $password));

        $this->manager->persist($user);
        $this->manager->flush();

        // $emailFrom = 'sococim216@gmail.com';
        // $emailTo = $user->getEmail();
        // $email = $this->userService->email($user->getEmail(), $password, $emailFrom, $emailTo);
        // $this->mailer->send($email);

        return $this->json('Profil '.$profilLibelle.' créé avec succès');
    }

    /**
     * @Route(name="getGCarte", path="/api/gcarte/list", methods={"GET"})
     */
    public function getGCarte() {
        if ($this->isGranted('ROLE_PARTENAIRE')) {
            $user = $this->userRepository->findByProfil(2);
            if ($user) {
                foreach ($user as $use) {
                    if ($use->getBlocked() === false) {
                        return $this->json($use, Response::HTTP_OK);
                    }else {
                        return $this->json([], Response::HTTP_OK);
                    }
                }
            }else {
                return $this->json([], Response::HTTP_OK);
            }
        }else {
            return $this->json("Acces denied !");
        }
    }

    /**
     * @Route(name="getGAcces", path="/api/gacces/list", methods={"GET"})
     */
    public function getGAcces() {
        if ($this->isGranted('ROLE_PARTENAIRE')) {
            $user = $this->userRepository->findByProfil(3);
            if ($user) {
                foreach ($user as $use) {
                    if ($use->getBlocked() === false) {
                        return $this->json($use, Response::HTTP_OK);
                    }else {
                        return $this->json([], Response::HTTP_OK);
                    }
                }
            }else {
                return $this->json([], Response::HTTP_OK);
            }
        }else {
            return $this->json("Acces denied !");
        }
    }

    /**
     * @Route(name="getAccepteur", path="/api/accepteur/list", methods={"GET"})
     */
    public function getAccepteur() {
        if ($this->isGranted('ROLE_PARTENAIRE')) {
            $user = $this->userRepository->findByProfil(4);
            if ($user) {
                foreach ($user as $use) {
                    if ($use->getBlocked() === false) {
                        return $this->json($use, Response::HTTP_OK);
                    }else {
                        return $this->json([], Response::HTTP_OK);
                    }
                }
            }else {
                return $this->json([], Response::HTTP_OK);
            }
        }else {
            return $this->json("Acces denied !");
        }
    }

    /**
     * @Route(name="getControleur", path="/api/controleur/list", methods={"GET"})
     */
    public function getControleur() {
        if ($this->isGranted('ROLE_PARTENAIRE')) {
            $user = $this->userRepository->findByProfil(5);
            if ($user) {
                foreach ($user as $use) {
                    if ($use->getBlocked() === false) {
                        return $this->json($use, Response::HTTP_OK);
                    }else {
                        return $this->json([], Response::HTTP_OK);
                    }
                }
            }else {
                return $this->json([], Response::HTTP_OK);
            }
        }else {
            return $this->json("Acces denied !");
        }
    }

    /**
     * @Route(name="getEmploye", path="/api/employe/list", methods={"GET"})
     */
    public function getEmploye() {
        if ($this->isGranted('ROLE_PARTENAIRE')) {
            $user = $this->userRepository->findByProfil(6);
            if ($user) {
                foreach ($user as $use) {
                    if ($use->getBlocked() === false) {
                        return $this->json($use, Response::HTTP_OK);
                    }else {
                        return $this->json([], Response::HTTP_OK);
                    }
                }
            }else {
                return $this->json([], Response::HTTP_OK);
            }
        }else {
            return $this->json("Acces denied !");
        }
    }

    /**
     * @Route(name="getBlockedEmploye", path="/api/employe/blocked", methods={"GET"})
     */
    public function getBlockedEmploye() {
        if ($this->isGranted('ROLE_PARTENAIRE')) {
            $user = $this->userRepository->findByProfil(6);
            if ($user) {
                foreach ($user as $use) {
                    if ($use->getBlocked() === true) {
                        return $this->json($use, Response::HTTP_OK);
                    }else {
                        return $this->json([], Response::HTTP_OK);
                    }
                }
            }else {
                return $this->json([], Response::HTTP_OK);
            }
        }else {
            return $this->json("Acces denied !");
        }
    }

    /**
     * @Route(name="getEmployeById", path="/api/employe/{id}/list", methods={"GET"})
     */
    public function getEmployeById($id) {
        if ($this->isGranted('ROLE_PARTENAIRE')) {
            $user = $this->userRepository->findOneBy(['id' => $id]);
            if ($user->getRoles()[0] === 'ROLE_EMPLOYE' && $user->getBlocked() !== true) {
                return $this->json($user, Response::HTTP_OK);
            }else {
                return $this->json('Not an Employe or blocked Employe');
            }
        }else {
            return $this->json("Acces denied !");
        }
    }

    /**
     * @Route(name="blockUser", path="/api/user/{id}/block", methods={"PUT"})
     */
    public function blockUser($id) {
        if ($this->isGranted('ROLE_PARTENAIRE')) {
            $user = $this->userRepository->findOneBy(['id' => $id]);
            if ($user->getBlocked() === true) {
                return $this->json('Profil déja bloqué');
            }else {
                $user->setBlocked(true);
                $this->manager->persist($user);
                $this->manager->flush();
                return $this->json('Profil bloqué');
            }
        }else {
            return $this->json("Acces denied !");
        }
    }

    /**
     * @Route(name="unblockUser", path="/api/user/{id}/unblock", methods={"PUT"})
     */
    public function unblockUser($id) {
        if ($this->isGranted('ROLE_PARTENAIRE')) {
            $user = $this->userRepository->findOneBy(['id' => $id]);
            if ($user->getBlocked() === false) {
                return $this->json('Profil pas bloqué');
            }else {
                $user->setBlocked(false);
                $this->manager->persist($user);
                $this->manager->flush();
                //dd($user);
                return $this->json('Profil débloqué');
            }
        }else {
            return $this->json("Acces denied !");
        }
    }

    public function random() {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 4; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public function random1() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyz1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 4; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public function random2() {
        $alphabet = '1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 6; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
    /**
     * @Route(
     *     name="Profil",
     *     methods={"GET"},
     *     path="/api/profil/data",
     *     defaults={
     *          "__controller"="App\Controller\UserController::Profil",
     *          "__api_resource_class"="App\Entity\User::class",
     *          "__api_collection_operation_name"="Profil"
     *     }
     * )
     */
    public function Profil()
    {
        $data = $this->getUser();
        return $this->json($data);
    }
}
