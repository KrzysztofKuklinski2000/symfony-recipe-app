<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[IsGranted('IS_EMAIL_VERIFIED')]
final class MealPlanController extends AbstractController
{
    #[Route('/meal-plan', name: 'app_meal_plan', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $weekOffset = $request->query->getInt('week', 0);
        $startOfWeek = new \DateTimeImmutable(sprintf('monday this week %s weeks', $weekOffset));

        $weekDays = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->modify(sprintf('+%d days', $i));

            $weekDays[] = [
                'label' => $this->getPolishWeekdayName($date),
                'date' => $date,
            ];
        }

        $mealTypes = [
            'breakfast' => 'Śniadanie',
            'lunch' => 'Obiad',
            'dinner' => 'Kolacja',
        ];

        return $this->render('meal_plan/index.html.twig', [
            'weekDays' => $weekDays,
            'mealTypes' => $mealTypes,
            'weekOffset' => $weekOffset,
            'startOfWeek' => $startOfWeek,
            'endOfWeek' => $startOfWeek->modify('+6 days'),
        ]);
    }

    private function getPolishWeekdayName(\DateTimeImmutable $date): string
    {
        return match ($date->format('N')) {
            '1' => 'Poniedziałek',
            '2' => 'Wtorek',
            '3' => 'Środa',
            '4' => 'Czwartek',
            '5' => 'Piątek',
            '6' => 'Sobota',
            '7' => 'Niedziela',
        };
    }
}
