<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Entity\User;


class AuthController extends AbstractController
{


    private $em;

    private $alerts = [];

    private $user = [];

    private $userRepository;

    private $alert = [];

    private $session;


    public function __construct(UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
   
    }

    #[Route('/auth/register', name: 'app_auth_register', methods: ['GET'])]
    public function register(): Response
    {
        return $this->render('auth/register.twig', [
            "title" => "Connectez-vous en 3 clics",
            "method" => "register",
            "user" => [],
            "alert" => $this->alert
        ]);
    }

    #[Route('/auth/login', name: 'app_auth_login', methods: ['POST'])]
    public function login(Request $request): Response
    {
        $user = $this->userRepository->findOneBy(["email" => $request->get("email")]);
        
        if($user == null){
            $this->addFlash("danger", "Cet email n'existe pas");
            return $this->redirectToRoute("app_auth_signup");
        }
        
        $passwordCheck = password_verify($request->get("password"), $user->getPassword());

        if( $passwordCheck == false){
            $this->addFlash("danger", "Mot de passe incorrect");
            return $this->redirectToRoute("app_auth_login");
        }

        $this->addFlash("success", "Bonjour " . $user->getUserName());

        $this->session = $request->getSession();
        $this->session = $this->session->set("userId", $user->getId());

        return $this->redirectToRoute("app_home");

    }


    #[Route('/auth/signup', name: 'app_auth_signup', methods: ['GET'])]
    public function signup(): Response
    {

        return $this->render('auth/signup.twig', [
            "title" => "Rejoignez la commuuuuu",
            "user" => [],
            "alert" => $this->alert
        ]);
    }

    #[Route('/auth/create', name: 'app_auth_create', methods: ['POST'])]
    public function create(Request $request): Response
    {

        //TO DO 
        // Utiliser Symfony Security

        $user = $this->userRepository->findOneBy(["email" => $request->get("email")]);

        if($user){
            $this->addFlash("danger", "Cet email existe déja");
            return $this->redirectToRoute("app_auth_register");
        }

        $password = $request->get("password");
        $passwordCheck = $request->get("passwordCheck");

        if($password !== $passwordCheck){
            $this->addFlash("danger", "Le mot de passe n'est pas le même");
            return $this->redirectToRoute("app_auth_signup");
        }

        $newUser = new User();
        $newUser->setUserName($request->get("userName"));
        $newUser->setEmail($request->get("email"));
        $newUser->setPassword(password_hash($password, PASSWORD_BCRYPT));
        $newUser->setImg("./img/default.webp");
        $newUser->setBio("Snowboarder en herbe");
        $newUser->setRole(0);
        $newUser->setCreatedAt(new \DateTimeImmutable());
        $newUser->setUpdatedAt(new \DateTimeImmutable());

        $this->em->persist($newUser);
   
        $this->em->flush();

        $this->addFlash("sucess", "Bienvenue parmi les riders " . $request->get("userName"));

        $this->session = $request->getSession();
        $this->session->set("userId", $newUser->getId());

        return $this->redirectToRoute("app_home");
    }

    #[Route('/auth/logout', name: 'app_auth_logout', methods: ['GET'])]
    public function logout(Request $request) 
    {

        $this->session = $request->getSession();
        $test = $this->session->clear();
        
        return $this->redirectToRoute("app_home");
    }


}
