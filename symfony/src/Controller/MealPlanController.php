<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MealPlanController extends AbstractController
{
    #[Route('/meal-plan', name: 'app_meal_plan')]
    public function index(): Response
    {
        $weekDays = [
            'Poniedziałek',
            'Wtorek',
            'Środa',
            'Czwartek',
            'Piątek',
            'Sobota',
            'Niedziela'
        ];

        $mealTypes = [
            'breakfast',
            'lunch',
            'dinner',
        ];

        return $this->render('meal_plan/index.html.twig', [
            'weekDays' => $weekDays,
            'mealTypes' => $mealTypes,
        ]);
    }
}
