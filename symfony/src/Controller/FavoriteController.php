<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Security\Voter\RecipeVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/favorite')]
#[IsGranted('ROLE_USER')]
final class FavoriteController extends AbstractController
{
    #[Route('/', name: 'app_favorite_index')]
    public function index(): Response {
        /** @var User $currentUser*/
        $currentUser = $this->getUser();
        $recipes = $currentUser->getFavorites();


       return $this->render('favorite/index.html.twig', [
           'recipes' => $recipes,
       ]);
    }

    #[Route('/add/{id}', name: 'app_favorite_add', methods: ['POST'])]
    public function favorite(Recipe $recipe, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted(RecipeVoter::FAVORITE, $recipe);

        if ($this->isCsrfTokenValid('favorite' . $recipe->getId(), $request->request->get('_token'))) {
            /** @var User $currentUser*/
            $currentUser = $this->getUser();

            $currentUser->addFavorite($recipe);
            $em->flush();
        }

        return $this->redirect($request->headers->get('referer', $this->generateUrl('app_home')));
    }

    #[Route('/remove/{id}', name: 'app_favorite_remove', methods: ['POST'])]
    public function unfavorite(Recipe $recipe, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted(RecipeVoter::FAVORITE, $recipe);

        if ($this->isCsrfTokenValid('unfavorite' . $recipe->getId(), $request->request->get('_token'))) {
            /** @var User $currentUser*/
            $currentUser = $this->getUser();
            $currentUser->removeFavorite($recipe);
            $em->flush();
        }

        return $this->redirect($request->headers->get('referer', $this->generateUrl('app_home')));
    }
}
