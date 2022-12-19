<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/category', name: 'category_' )]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name:'new')]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('category_index');
        }

        return $this->renderForm('category/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{categoryName}', name: 'show')]
    public function show(string $categoryName, CategoryRepository $categoryRepository, ProgramRepository $programRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $category = $categoryRepository->findOneBy(
            ['name' => $categoryName],
            ['name' => 'ASC'],
        );
        if(!$category){
            throw $this->createNotFoundException('Catégorie inconnu');
        } else{
            $programs = $programRepository->findBy(
                ['category' => $category],
                ['id' => 'ASC'],
                3,
                0);
        }
        
        return $this->render('category/show.html.twig', [
            'categories' => $categories,
            'programs' => $programs,
            'categoryName' => $category,
        ]);
    }
}
