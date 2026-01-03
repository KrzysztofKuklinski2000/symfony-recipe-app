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
    public function __construct(private EntityManagerInterface $em, private ShoppingListItemRepository $repository){}


    public function addIngredientToShoppingList(
        User $user,
        RecipeIngredient $ingredient,
        ?Recipe $recipe = null,
        float $scaleFactor = 1,
    ): void
    {
        $existingItem = $this->repository->findOneBy([
            'user' => $user,
            'name' => $ingredient->getName(),
            'recipe' => $recipe
        ]);

        if($existingItem){
            $existingItem->increnentCount();
            $existingItem->setIsChecked(false);
        }else {
            $newRecipeItem = new ShoppingListItem();
            $newRecipeItem->setUser($user);
            $newRecipeItem->setRecipe($recipe);
            $newRecipeItem->setName($ingredient->getName());
            if($ingredient->getQuantity()) {
                $scaledQuantity = $ingredient->getQuantity() * $scaleFactor;

                $displayValue = (float)round($scaledQuantity, 2);

                $unit = $ingredient->getUnit();

                $quantityString = $displayValue.($unit ? '  '. $unit: '');

                $newRecipeItem->setQuantity($quantityString);
            }else {
                $newRecipeItem->setQuantity(null);
            }

            $newRecipeItem->setIsChecked(false);

            $this->em->persist($newRecipeItem);

            $this->save();
        }
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

    public function save(): void {
        $this->em->flush();
    }

    public function countRemainingItems(User $user, ?Recipe $recipe = null): int {
        return $this->repository->count(['user' => $user, 'recipe' => $recipe]);
    }
}
