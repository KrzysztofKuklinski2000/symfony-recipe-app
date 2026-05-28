<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\RecipeRating;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\UX\Turbo\TurboBundle;

final class RecipeRatingController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em) {

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

        $ratingRepository = $this->em->getRepository(RecipeRating::class);
        $existingRating = $ratingRepository->findOneBy([
            'recipe' => $recipe,
            'author' => $user
        ]);

        if ($existingRating) {
            $existingRating->setScore($score);
        } else {
            $newRating = new RecipeRating();
            $newRating->setRecipe($recipe);
            $newRating->setAuthor($user);
            $newRating->setScore($score);
            $this->em->persist($newRating);
        }

        $this->em->flush();

        if(TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);


            return $this->render('recipe_rating/rate_success.stream.html.twig', [
                'recipe' => $recipe,
            ]);
        }

        $this->addFlash('success', 'Twoja ocena została zapisana!');
        return $this->redirectToRoute('app_show', ['id' => $recipe->getId()]);
    }
}
