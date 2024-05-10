<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\TrickRepository;
use Monolog\DateTimeImmutable;

class HomeController extends AbstractController
{
    private $alerts = [];

    private $tricks = [];

    private $session = [];

    private $user;

    private $trickRepository = null;

    public function __construct(TrickRepository $trickRepository, UserRepository $userRepo)
    {
        $this->trickRepository = $trickRepository;
        $this->userRepo = $userRepo;
    }


    #[Route("/", name: "app_home")]
    public function home(Request $request): Response
    {
        $this->tricks = $this->trickRepository->findAll() ?? [];
        $this->session = $request->getSession();
        $userId = $this->session->get("userId");

        if($userId){
            $this->user = $this->userRepo->find($userId) ;
        }else{
            $this->user = [];
        }


        return $this->render('home.twig', [
           "user" => $this->user,
           "title" =>"Bienvenue sur SnowTicks",
           "page_title" =>"Bienvenue sur SnowTicks",
           "tricks" => $this->tricks,
           "type" => "success"
        ]);
    }
}