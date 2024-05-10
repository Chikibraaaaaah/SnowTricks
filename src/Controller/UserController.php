<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;

class UserController extends AbstractController
{

    private $user = [];

    private $repository = null;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    // #[Route('/user/{email}', name: 'app_user_mail')]
    // public function getByEmail($email): Response
    // {
    //     $user = $this->repository->findOneBy(["email" => $email]);
    // }

    #[Route('/user/{id}', name: 'app_user_getOne', methods: ["GET"])]
    public function getOne(Request $request): Response
    {
        $this->user = $this->repository->find($request->get("id"));
    
        return $this->user;
    }



    #[Route('/user/{id}/profile', name: 'app_user_getProfile', methods: ["GET"])]
    public function getProfile(Request $request): Response
    {

        $this->user = $this->repository->find($request->get("id"));

        return $this->render("user/profile.twig", [
            "user" => $this->user,
            "title" => "Bievenue sur ta page de merde " . $this->user->getUserName()
        ]);

    }
}
