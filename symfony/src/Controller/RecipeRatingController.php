<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\User;
use App\Service\RecipeRatingService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\UX\Turbo\TurboBundle;

final class RecipeRatingController extends AbstractController
{
    public function __construct(private readonly RecipeRatingService $ratingService) {

    }

    #[Route('/rate/{id}', name: 'app_recipe_rate', methods: ['POST'])]
    public function rate(Recipe $recipe, Request $request, #[CurrentUser] User $user): Response
    {
        if (!$this->isCsrfTokenValid('rate' . $recipe->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('app_show', ['id' => $recipe->getId()]);
        }

        if ($recipe->getAuthor() === $user) {
            $this->addFlash('error', 'Nie możesz ocenić własnego przepisu.');
            return $this->redirectToRoute('app_show', ['id' => $recipe->getId()]);
        }

        $score = $request->request->getInt('score');
        if ($score < 1 || $score > 5) {
            $this->addFlash('error', 'Nieprawidłowa wartość oceny.');
            return $this->redirectToRoute('app_show', ['id' => $recipe->getId()]);
        }

        $this->ratingService->rateRecipe($recipe, $user, $score);

        if(TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);


            return $this->render('recipe_rating/rate_success.stream.html.twig', [
                'recipe' => $recipe,
            ]);
        }

        $this->addFlash('success', 'Twoja ocena została zapisana!');
        return $this->redirectToRoute('app_show', ['id' => $recipe->getId()]);
    }

    #[Route('/rate/{id}/cancel', name: 'app_recipe_rate_cancel', methods: ['POST'])]
    public function cancelRate(Recipe $recipe, Request $request, #[CurrentUser] User $user): Response
    {
        if (!$this->isCsrfTokenValid('cancel_rate' . $recipe->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('app_show', ['id' => $recipe->getId()]);
        }

        $this->ratingService->cancelRecipeRating($recipe, $user);

        if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

            return $this->render('recipe_rating/rate_success.stream.html.twig', [
                'recipe' => $recipe,
            ]);
        }

        $this->addFlash('success', 'Twoja ocena została usunięta.');
        return $this->redirectToRoute('app_show', ['id' => $recipe->getId()]);
    }
}
