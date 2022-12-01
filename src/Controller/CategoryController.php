<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
