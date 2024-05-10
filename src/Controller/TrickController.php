<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Trick;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use App\Repository\ImgRepository;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\ServiceController;

class TrickController extends AbstractController
{

    private $em;

    private $session;

    private $trickRepository;

    private $userRepository;

    private $imgRepository;

    private $videoRepository;

    private $user = [];

    private $trick = [];

    private $tricks = [];

    private $serviceController;


    public function __construct(TrickRepository $trickRepository, UserRepository $userRepository, EntityManagerInterface $em, ServiceController $serviceController, ImgRepository $imgRepository, VideoRepository $videoRepository)
    {
        $this->trickRepository = $trickRepository;
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->serviceController = $serviceController;
        $this->imgRepository = $imgRepository;
        $this->videoRepository = $videoRepository;
    }


    #[Route('/tricks/new', name: 'app_tricks_new', methods: ["GET"])]
    public function newTrick(Request $request): Response
    {
        $id = $request->getSession()->get("userId");
        $this->user = $this->userRepository->find($id);

        return $this->render("trick/create.twig",[
            "title" => "Partages ton trick de malaaaaaaade",
            "user" => $this->user
        ]);
    }


    #[Route('/tricks/create', name: 'app_tricks_create', methods: ["POST"])]
    public function create(Request $request): Response
    {

        $files = $request->files;
        $destination = $this->getParameter('kernel.project_dir').'/public/img';

        foreach ($files as $fileArray) {
            foreach ($fileArray as $file) {
                // Vérifier si le fichier est valide
                if ($file instanceof UploadedFile) {
                    // Déplacer le fichier vers le dossier de destination
                    $file->move($destination, $file->getClientOriginalName());
                }
            }
        }

        $userId = $request->getSession()->get("userId");

        $this->trick = new Trick();
        $this->trick->setName($request->request->get("title"));
        $this->trick->setDescription($request->request->get("description"));
        $this->trick->setAuthorId($userId);
        $this->trick->setCreatedAt(new \DateTimeImmutable());
        $this->trick->setUpdatedAt(new \DateTimeImmutable());

        $this->em->persist($this->trick);
        $this->em->flush();

        $trickId = $this->trick->getId();

        $this->addFlash("success", "Trick crée avec succès");
        sleep(1);

        $serviceController = new ServiceController($this->em, $this->mediaRepository);
        $serviceController->uploadFile($request, $trickId);

        var_dump($serviceController);
        die();


        return $this->redirectToRoute("app_home");

    }


    public function getId(){

    }






}
