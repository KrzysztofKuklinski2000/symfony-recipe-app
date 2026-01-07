<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Recipe;
use Symfony\UX\Turbo\TurboBundle;
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
    public function __construct(private readonly EntityManagerInterface $em){}

    #[Route('/', name: 'app_favorite_index')]
    public function index(): Response {
        $currentUser = $this->getUser();
        assert($currentUser instanceof User);

        $recipes = $currentUser->getFavorites();


       return $this->render('favorite/index.html.twig', [
           'recipes' => $recipes,
       ]);
    }

    #[Route('/add/{id}', name: 'app_favorite_add', methods: ['POST'])]
    #[IsGranted('IS_EMAIL_VERIFIED')]
    public function add(Recipe $recipe, Request $request): Response
    {
        $this->denyAccessUnlessGranted(RecipeVoter::FAVORITE, $recipe);

        if ($this->isCsrfTokenValid('favorite' . $recipe->getId(), $request->request->get('_token'))) {

            $user = $this->getUser();
            assert($user instanceof User);

            $user->addFavorite($recipe);
            $this->em->flush();
        }

        if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

            $isFavoritePage = $request->request->get('is_favorite_page');

            return $this->render('favorite/add_success.stream.html.twig', [
                'recipe' => $recipe,
                'is_favorite_page' => $isFavoritePage,
            ]);
        }


        return $this->render('favorite/_btn.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    #[Route('/remove/{id}', name: 'app_favorite_remove', methods: ['POST'])]
    #[IsGranted('IS_EMAIL_VERIFIED')]
    public function remove(Recipe $recipe, Request $request): Response
    {
        $this->denyAccessUnlessGranted(RecipeVoter::FAVORITE, $recipe);

        if ($this->isCsrfTokenValid('unfavorite' . $recipe->getId(), $request->request->get('_token'))) {

            $user = $this->getUser();
            assert($user instanceof User);

            $user->removeFavorite($recipe);
            $this->em->flush();

            $isOnFavoritePage = $request->request->get('remove_from_list');

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
                return $this->render('favorite/delete_success.stream.html.twig', [
                    'recipe' => $recipe,
                    'is_favorite_page' => $isOnFavoritePage,
                ]);
            }
        }

        return $this->render('favorite/_btn.html.twig', [
            'recipe' => $recipe,
        ]);
    }
}
