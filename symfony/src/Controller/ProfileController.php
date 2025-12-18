<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/profile')]
final class ProfileController extends AbstractController
{
    #[Route('/following', name: 'app_profile_index')]
    #[IsGranted('ROLE_USER')]
    public function followingFeed(RecipeRepository $recipeRepository): Response {
        /** @var User $currentUser*/
        $currentUser = $this->getUser();
        $users = $currentUser->getFollowing();

        $recipes = $recipeRepository->findRecipesFromFollowing($currentUser);

        return $this->render('profile/following_feed.html.twig', [
            'users' => $users,
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
