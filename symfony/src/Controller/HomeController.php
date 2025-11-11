<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(RecipeRepository $recipeRepository): Response
    {
        $recipes = $recipeRepository->findBy([], ['id' => 'DESC'], 20);

        return $this->render('home/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/{id}', name: 'app_show', requirements: ['id' => '\d+'])]
    public function show(Recipe $recipe): Response {
        return $this->render('home/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }
}
