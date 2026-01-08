<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\ShoppingListItem;
use App\Repository\ShoppingListItemRepository;
use Doctrine\ORM\EntityManagerInterface;

class ShoppingListService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ShoppingListItemRepository $repository
    ){}


    public function addIngredientToShoppingList(
        User $user,
        RecipeIngredient $ingredient,
        ?Recipe $recipe = null,
        float $scaleFactor = 1,
    ): void
    {
        $unit = $ingredient->getUnit() === '' ? null : $ingredient->getUnit();

        $existingItem = $this->repository->findOneBy([
            'user' => $user,
            'name' => $ingredient->getName(),
            'recipe' => $recipe,
            'unit' => $unit,
        ]);

        $quantityToAdd = 0.0;

        if($ingredient->getQuantity()){
            $quantityToAdd = $ingredient->getQuantity() * $scaleFactor;
        }

        if($existingItem){
            if($quantityToAdd > 0) {
                $currentQuantity = $existingItem->getQuantity() ?? 0.0;

                $newQuantity = $currentQuantity + $quantityToAdd;

                $existingItem->setQuantity($newQuantity);

                $existingItem->setCount(1);
            }else {
                // Dla produktów bez wagi (np. 2x "Sól do smaku")
                $existingItem->incrementCount();
            }
            $existingItem->setIsChecked(false);
            return;
        }

        $newRecipeItem = new ShoppingListItem();
        $newRecipeItem->setUser($user);
        $newRecipeItem->setRecipe($recipe);
        $newRecipeItem->setName($ingredient->getName());
        $newRecipeItem->setCount(1);
        $newRecipeItem->setIsChecked(false);
        $newRecipeItem->setUnit($unit);

        if($quantityToAdd > 0) {
            $newRecipeItem->setQuantity($quantityToAdd);
        }else {
            $newRecipeItem->setQuantity(null);
        }


        $this->em->persist($newRecipeItem);
    }

    public function groupItemsByRecipe(iterable $shoppingListItems): array
    {
        $groupedItems = [];

        foreach ($shoppingListItems as $item) {
            $key = $item->getRecipe() ?  $item->getRecipe()->getId() : 'loose';

            if (!isset($groupedItems[$key])) {
                $groupedItems[$key] = [
                    'recipe' => $item->getRecipe(),
                    'items' => []
                ];
            }

            $groupedItems[$key]['items'][] = $item;
        }

        return $groupedItems;
    }

    public function toggleItem(ShoppingListItem $item): void
    {
        $item->setIsChecked(!$item->isChecked());
    }

    public function deleteItem(ShoppingListItem $item): void
    {
        $this->em->remove($item);
    }

    public function deleteByRecipe(User $user, ?int $recipeId = null): void
    {
        $this->repository->deleteByRecipe($user, $recipeId);
    }


    public function save(): void {
        $this->em->flush();
    }

    public function countRemainingItems(User $user, ?Recipe $recipe = null): int {
        return $this->repository->count(['user' => $user, 'recipe' => $recipe]);
    }
}
