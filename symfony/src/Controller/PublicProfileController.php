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
final class PublicProfileController extends AbstractController
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

    #[Route('/follow/{id}', name: 'app_profile_follow', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function follow(User $userToFollow, EntityManagerInterface $em, Request $request): Response {

        if($this->isCsrfTokenValid('follow'.$userToFollow->getId(), $request->request->get('_token'))) {
            /** @var User $currentUser*/
            $currentUser = $this->getUser();

            $currentUser->addFollowing($userToFollow);
            $em->flush();

        }else {
            $this->addFlash('error', 'Nieprawidłowy token CSRF');
        }

        return $this->redirectToRoute('app_profile_show', ['id' => $userToFollow->getId()]);
    }

    #[Route('/unfollow/{id}', name: 'app_profile_unfollow', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function unfollow(User $userToUnfollow, EntityManagerInterface $em, Request $request): Response{
        /** @var User $currentUser*/
        $currentUser = $this->getUser();

        if ($this->isCsrfTokenValid('unfollow' . $userToUnfollow->getId(), $request->request->get('_token'))){
            $currentUser->removeFollowing($userToUnfollow);
            $em->flush();
        }else {
            $this->addFlash('error', 'Nieprawidłowy token CSRF');
        }



        return $this->redirectToRoute('app_profile_show', ['id' => $userToUnfollow->getId()]);
    }


}
