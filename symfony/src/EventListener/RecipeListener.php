<?php
namespace App\EventListener;

use App\Entity\Recipe;
use Doctrine\ORM\Events;
use App\Service\NutritionApiService;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Recipe::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Recipe::class)]
class RecipeListener {

    public function __construct(private NutritionApiService $nutritionApi){}

    public function prePersist(Recipe $recipe, PrePersistEventArgs $event): void {
        $this->calculateCalories($recipe);
    }

    public function preUpdate(Recipe $recipe, PreUpdateEventArgs $event): void
    {
        $this->calculateCalories($recipe);
    }

    private function calculateCalories(Recipe $recipe): void {

        $ingredients = $recipe->getRecipeIngredients();

        if($ingredients->isEmpty()) {
            $recipe->setKcal(0);
            return;
        }

        $query = [];

        foreach($ingredients as $ingredient) {
            $name = $ingredient->getName();
            $quantity = $ingredient->getQuantity();
            $unit = $ingredient->getUnit();

            if(!$name) {
                continue;
            }

            $part = trim(sprintf('%s %s %s', $quantity, $unit, $name));
            $query[] = $part;
        }

        if(empty($query)) {
            return;
        }

        $queryString = implode(', ', $query);

        $totalCalories = $this->nutritionApi->calculateTotalCalories($queryString);

        if($totalCalories !== null) {
            $recipe->setKcal($totalCalories);
        }
    }
}
