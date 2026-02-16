<?php

namespace App\EventListener;

use App\Entity\Recipe;
use App\Service\NutritionApiService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Recipe::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Recipe::class)]
class RecipeListener
{
    public function __construct(
        private NutritionApiService $nutritionApi,
        private LoggerInterface $logger,
    ) {}

    public function prePersist(Recipe $recipe, PrePersistEventArgs $event): void
    {
        $this->calculateCalories($recipe);
    }

    public function preUpdate(Recipe $recipe, PreUpdateEventArgs $event): void
    {
        $this->calculateCalories($recipe);
    }

    private function calculateCalories(Recipe $recipe): void
    {
        if(!$recipe->isCalculateNutrition()) {
            $recipe->setKcal(null);
            return;
        }

        try {
            $ingredients = $recipe->getRecipeIngredients();

            if ($ingredients->isEmpty()) {
                $recipe->setKcal(null);
                return;
            }

            $query = [];

            foreach ($ingredients as $ingredient) {
                $name = $ingredient->getName();
                $quantity = $ingredient->getQuantity();
                $unit = $ingredient->getUnit();
                $factor = $ingredient->getNutritionFactor();

                if (!$name) {
                    continue;
                }

                if($factor <= 0.01) continue;

                $quantity = $quantity * $factor;

                $unitString = $unit ? $unit->value : '';
                $unitString = match ($unitString) {
                    'ml' => 'g',
                    'l' => 'kg',
                    default => $unitString,
                };

                $query[] = trim(sprintf('%s%s %s', $quantity ?? '', $unitString ?? '', $name));
            }

            if (empty($query)) {
                return;
            }

            $queryString = implode(', ', $query);

            $totalKcal = $this->nutritionApi->calculateTotalCalories($queryString);

            if ($totalKcal !== null) {
                $recipe->setKcal($totalKcal);
            }
        } catch (\Throwable $e) {
            $this->logger->error('RecipeListener: Krytyczny błąd podczas liczenia kalorii', [
                'recipe_id' => $recipe->getId(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return;
        }
    }
}
