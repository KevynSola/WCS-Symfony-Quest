<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Repository\ActorRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/actor', name: 'app_actor_')]
class ActorController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ActorRepository $actorRepository): Response
    {
        return $this->render('actor/index.html.twig', [
            'actors' => $actorRepository->findAll(),
        ]);
    }

    #[Route('/{actor}', requirements: ['id' => '\d+'], methods: ['GET'], name: 'show')]
    public function show(Actor $actor, ActorRepository $actorRepository)
    {
        return $this->render('actor/show.html.twig', [
            'actor' => $actor,
        ]);
    }
}
