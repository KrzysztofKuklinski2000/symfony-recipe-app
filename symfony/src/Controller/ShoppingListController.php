<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\ShoppingListItem;
use App\Security\Voter\ShoppingListVoter;
use App\Service\ShoppingListService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\Turbo\TurboBundle;

#[IsGranted('ROLE_USER')]
final class ShoppingListController extends AbstractController
{

    public function __construct(private ShoppingListService $shoppingListService){}

    #[Route('/shopping/list', name: 'app_shopping_list')]
    public function index(): Response
    {
        /** @var User $user*/
        $user = $this->getUser();

        $shoppingListItems = $user->getShoppingListItems();

        $groupedItems = $this->shoppingListService->groupItemsByRecipe($shoppingListItems);


        return $this->render('shopping_list/index.html.twig', [
            'groupedItems' => $groupedItems,
        ]);
    }

    #[Route('/shopping/add/{id}', name: 'app_shopping_add', methods:['POST'])]
    public function add(Recipe $recipe, Request $request): Response
    {
        if ($this->isCsrfTokenValid('shoppingList' . $recipe->getId(), $request->request->get('_token'))) {
            $recipeItems = $recipe->getRecipeIngredients();
            $user = $this->getUser();

            foreach($recipeItems as $recipeItem) {
                $this->shoppingListService->addIngredientToShoppingList($user, $recipeItem, $recipe);
            }

            $this->shoppingListService->save();

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
                return $this->render('shopping_list/add_recipe_ingredients.stream.html.twig', [
                    'recipe' => $recipe,
                ]);
            }
            $this->addFlash('success', 'Dodano produkty do listy zakupów');
        }
        return $this->redirectToRoute('app_show', ['id' => $recipe->getId()]);
    }

    #[Route('/shopping/add-item/{id}', name: 'app_shopping_add_item', methods: ['POST'])]
    public function addItem(RecipeIngredient $ingredient, Request $request): Response
    {
        if ($this->isCsrfTokenValid('add_item' . $ingredient->getId(), $request->request->get('_token'))) {
            $user = $this->getUser();

            $this->shoppingListService->addIngredientToShoppingList($user, $ingredient);
            $this->shoppingListService->save();

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
                return $this->render('shopping_list/add_item.stream.html.twig', [
                    'ingredient' => $ingredient,
                ]);
            }

            $this->addFlash('success', 'Dodano produkt do listy zakupów');
        }
        return $this->redirectToRoute('app_show', ['id' => $ingredient->getRecipe()->getId()]);
    }

    #[Route('/shopping/toggle/{id}', name: 'app_shopping_toggle', methods: ['POST'])]
    public function toggle(ShoppingListItem $item, Request $request): Response
    {
        $this->denyAccessUnlessGranted(ShoppingListVoter::TOGGLE, $item);

        if ($this->isCsrfTokenValid('shoppingItem' . $item->getId(), $request->request->get('_token'))) {
            $this->shoppingListService->toggleItem($item);
            $this->shoppingListService->save();

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
                return $this->render('shopping_list/toggle_item.stream.html.twig', [
                    'item' => $item,
                ]);
            }
        }
        return $this->redirectToRoute('app_shopping_list');
    }

    #[Route('/shopping/delete/{id}', name: 'app_shopping_delete', methods: ['POST'])]
    public function delete(ShoppingListItem $item, Request $request): Response
    {
        $this->denyAccessUnlessGranted(ShoppingListVoter::DELETE, $item);

        if ($this->isCsrfTokenValid('shoppingItem' . $item->getId(), $request->request->get('_token'))) {
            $recipe = $item->getRecipe();
            $itemId = $item->getId();

            $counterId = $recipe ? 'ingredient-counter-' . $recipe->getId() : 'ingredient-counter-loose';

            $this->shoppingListService->deleteItem($item);
            $this->shoppingListService->save();

            $remainingCount = $this->shoppingListService->countRemainingItems($this->getUser(), $recipe);

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
                return $this->render('shopping_list/delete_item.stream.html.twig', [
                    'itemId' => $itemId,
                    'counterId' => $counterId,
                    'remainingCount' => $remainingCount,
                    'recipe' => $recipe,
                ]);
            }
        }
        return $this->redirectToRoute('app_shopping_list');
    }
}
