<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\RecipeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(RecipeRepository $recipeRepository): Response
    {
        $recipes = $recipeRepository->findPublicRecipesExcludingUser($this->getUser());

        return $this->render('home/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/{id}', name: 'app_show', requirements: ['id' => '\d+'])]
    public function show(Recipe $recipe): Response {
        $commentForm = $this->createForm(CommentType::class, new Comment());

        return $this->render('home/show.html.twig', [
            'recipe' => $recipe,
            'commentForm' => $commentForm->createView(),
        ]);
    }
}
