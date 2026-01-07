<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RecipeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/profile')]
final class ProfileController extends AbstractController
{

    public function __construct(private readonly RecipeRepository $recipeRepository) {}

    #[Route('/following', name: 'app_profile_index')]
    #[IsGranted('ROLE_USER')]
    public function followingFeed(): Response {
        $currentUser = $this->getUser();
        assert($currentUser instanceof User);

        $recipes = $this->recipeRepository->findRecipesFromFollowing($currentUser);

        return $this->render('profile/following_feed.html.twig', [
            'users' => $currentUser->getFollowing(),
            'recipes' => $recipes,
        ]);
    }

    #[Route('/{id}', name: 'app_profile_show', requirements: ['id' => '\d+'])]
    public function userProfile(User $user): Response
    {
        return $this->render('profile/user_profile.html.twig', [
            'user' => $user,
        ]);
    }
}
