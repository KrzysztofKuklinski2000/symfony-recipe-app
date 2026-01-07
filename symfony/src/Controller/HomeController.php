<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\Comment;
use App\Entity\Category;
use App\Enum\DietaryTag;
use App\Enum\Difficulty;
use App\Form\CommentType;
use Symfony\UX\Turbo\TurboBundle;
use App\Repository\RecipeRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    private const PER_PAGE = 1;
    public function __construct(
        private readonly RecipeRepository $recipeRepository,
        private readonly CategoryRepository $categoryRepository
    ){}

    #[Route('/', name: 'app_home')]
    #[Route('/category/{slug}', name: 'app_home_category')]
    public function index(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        ?Category $category = null,
        Request $request
    ): Response {
        $page = (int) $request->query->get('page', 1);
        $phrase = $request->query->get('phrase') ?? null;
        $difficultyValue = $request->query->get('difficulty');
        $tagValues = $request->query->all('tags');

        $recipes = $this->recipeRepository->findPublicRecipesExcludingUser(
            $this->getUser(),
            $category,
            $phrase,
            $difficultyValue,
            $tagValues,
            $page,
            self::PER_PAGE
        );

        $hasNextPage = count($recipes) > self::PER_PAGE;

        if($hasNextPage) array_pop($recipes);

        if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

            return $this->render('home/load_more.stream.html.twig', [
                'recipes' => $recipes,
                'page' => $page,
                'hasNextPage' => $hasNextPage,
                'category' => $category,
                'phrase' => $phrase,
                'difficulty' => $difficultyValue,
                'tags' => $tagValues,
            ]);
        }

        return $this->render('home/index.html.twig', [
            'recipes' => $recipes,
            'currentCategory' => $category,
            'categories' => $this->categoryRepository->findAll(),
            'hasNextPage' => $hasNextPage,
            'page' => $page,

            'currentPhrase' => $phrase,
            'currentDifficulty' => $difficultyValue,
            'currentTags' => $tagValues,

            'difficulties' => Difficulty::cases(),
            'tags' => DietaryTag::cases(),
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
