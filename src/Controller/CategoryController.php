<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categories", name="categories")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/list", name="_list")
     */
    public function list(CategoryRepository $categoryRepository): Response
    {
        $allCategories = $categoryRepository->findAll();

        return $this->json($allCategories, Response::HTTP_OK, [], ['groups' => 'api_categories']);
    }

    /**
     * @Route("/{id}", name="_read")
     */
    public function read($id, CategoryRepository $categoryRepository): Response
    {
        $currentCategory = $categoryRepository->find($id);

        return $this->json($currentCategory, Response::HTTP_OK, [], ['groups' => 'api_categories']);
    }

    /**
     * @Route("/add", name="_add")
     */
    // public function add(Request $request, CategoryRepository $categoryRepository): Response
    // {
    //     $newCategory = new Category;

        
    // }
}
