<?php

namespace App\Entity;

use App\Repository\MealPlanItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MealPlanItemRepository::class)]
class MealPlanItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $plannedFor = null;

    #[ORM\Column(length: 30)]
    private ?string $mealType = null;

    #[ORM\Column]
    private ?int $servings = null;

    #[ORM\ManyToOne(inversedBy: 'mealPlanItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recipe $recipe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlannedFor(): ?\DateTimeImmutable
    {
        return $this->plannedFor;
    }

    public function setPlannedFor(\DateTimeImmutable $plannedFor): static
    {
        $this->plannedFor = $plannedFor;

        return $this;
    }

    public function getMealType(): ?string
    {
        return $this->mealType;
    }

    public function setMealType(string $mealType): static
    {
        $this->mealType = $mealType;

        return $this;
    }

    public function getServings(): ?int
    {
        return $this->servings;
    }

    public function setServings(int $servings): static
    {
        $this->servings = $servings;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): static
    {
        $this->recipe = $recipe;

        return $this;
    }
}
