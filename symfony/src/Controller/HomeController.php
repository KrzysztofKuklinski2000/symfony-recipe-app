<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\CommentType;
use App\Repository\RecipeRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\Turbo\TurboBundle;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Route('/category/{slug}', name: 'app_home_category')]
    public function index(
        RecipeRepository $recipeRepository,
        CategoryRepository $categoryRepository,
        #[MapEntity(mapping: ['slug' => 'slug'])]
        ?Category $category = null,
        Request $request
    ): Response
    {
        $limit = 4;
        $page = $request->query->get('page', 1);
        $phrase = $request->query->get('phrase') ?? null;

        $recipes = $recipeRepository->findPublicRecipesExcludingUser($this->getUser(), $category, $phrase, $page, $limit);

        $hasNextPage = count($recipes) > $limit;

        if($hasNextPage) array_pop($recipes);

        $categories = $categoryRepository->findAll();

        if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

            return $this->render('home/load_more.stream.html.twig', [
                'recipes' => $recipes,
                'page' => $page,
                'hasNextPage' => $hasNextPage,
                'category' => $category,
                'phrase' => $phrase,
            ]);
        }

        return $this->render('home/index.html.twig', [
            'recipes' => $recipes,
            'currentCategory' => $category,
            'categories' => $categories,
            'hasNextPage' => $hasNextPage,
            'page' => $page,
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
