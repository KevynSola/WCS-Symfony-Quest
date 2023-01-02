<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Season;
use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Form\ProgramType;
use App\Form\SearchProgramType;
use App\Service\ProgramDuration;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use App\Repository\ProgramRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }
    
    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository, Request $request): Response
    {
        $form = $this->createForm(SearchProgramType::class);
        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid()){
            $search = $form->getData()['search'];
            $programs = $programRepository->findLikeName($search);
        } else {
            $programs = $programRepository->findAll();
        }
        return $this->renderForm('program/index.html.twig', [
            'programs' => $programs,
            'form' => $form,
        ]);
    }

    #[Route('/new', name:'new')]
    public function new(Request $request, ProgramRepository $programRepository, MailerInterface $mailer, SluggerInterface $slugger): Response
    {
        $program = new Program();

        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $slug = $this->slugger->slug($program->getTitle());
            $program->setSlug($slug);
            $programRepository->save($program, true);
            $program->setOwner($this->getUser());

            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('your_email@example.com')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('program/newProgramEmail.html.twig', ['program' => $program]));

            $mailer->send($email);

            $this->addFlash('success', 'Cette nouvelle série est créée');

            return $this->redirectToRoute('program_index');
        }

        return $this->renderForm('program/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', methods: ['GET'], name: 'show')]
    public function show(Program $program, ProgramRepository $programRepository, ProgramDuration $programDuration): Response
    {
        $seasons = $program->getSeasons();

        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
            'programDuration' => $programDuration->calculate($program),
        ]);
    }

    #[Route('/{slug}/seasons/{season}', requirements: ['id' => '\d+'], methods: ['GET'], name: 'season_show')]
    public function showSeason(Program $program, Season $season): Response
    {
        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
        ]);
    }

    #[Route('/{slug}/season/{season}/episode/{slugEp}', methods: ['GET'], name: 'episode_show')]
    #[ParamConverter('episode', options: ['mapping' => ['slugEp' => 'slug']])]
    #[ParamConverter('program', options: ['mapping' => ['slug' => 'slug']])]
    public function showEpisode(
        Program $program,
        Season $season,
        Episode $episode,
        CommentRepository $commentRepository,
        CategoryRepository $categoryRepository,
        Request $request
        )
    {
        $categories = $categoryRepository->findAll();
        /* $comments = $commentRepository->findBy(['episode' => $episode->getId()]);
        $comment = new Comment();

        if(){
            $commentRepository->save($comment, true);

            $this->addFlash('succes', 'Votre commentaire est en ligne');
            return $this->redirectToRoute('episode_show', [
                'episode' => $episode->getId(),
                'season' => $season->getId(),
            ]);
        } */

        return $this->renderForm('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            /* 'comments' => $comments, */
            'categories' => $categories,
        ]);
    }

    #[Route('/{slug}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Program $program, ProgramRepository $programRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($this->getUser() !== $program->getOwner()) {
            // If not the owner, throws a 403 Access Denied exception
            throw $this->createAccessDeniedException('Only the owner can edit the program!');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $this->slugger->slug($program->getTitle());
            $program->setSlug($slug);
            $programRepository->save($program, true);

            return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('program/edit.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', requirements: ['id' => '\d+'], name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Program $program, ProgramRepository $programRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $program->getId(), $request->request->get('_token'))) {
            $programRepository->remove($program, true);
            $this->addFlash('danger', 'Cet épisode a été supprimé');
        }

        return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{slug}/watchlist', requirements: ['id' => '\d+'], methods: ['GET', 'POST'], name: 'watchlist')]
    public function addToWatchlist(Program $program, UserRepository $userRepository): Response
    {
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with this id found in program\'s table.'
            );
        }      

        /** @var \App\Entity\User */
        $user = $this->getUser();       
        if ($user->isInWatchlist($program)) {
            $user->removeFromWatchlist($program);
        } else {
            $user->addToWatchlist($program);
        }        

        $userRepository->save($user, true);        

        return $this->redirectToRoute('program_index', ['slug' => $program->getSlug()], Response::HTTP_SEE_OTHER);
    }
}