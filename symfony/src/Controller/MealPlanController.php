<?php

namespace App\Controller;

use App\Entity\MealPlanItem;
use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\MealPlanItemRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[IsGranted('IS_EMAIL_VERIFIED')]
final class MealPlanController extends AbstractController
{
    private const MEAL_TYPES = [
        'breakfast' => 'Śniadanie',
        'lunch' => 'Obiad',
        'dinner' => 'Kolacja',
    ];

    /**
     * @throws Exception
     */
    #[Route('/meal-plan', name: 'app_meal_plan', methods: ['GET'])]
    public function index(Request $request, MealPlanItemRepository $mealPlanItemRepository): Response
    {
        $weekOffset = $request->query->getInt('week', 0);
        $startOfWeek = new DateTimeImmutable(sprintf('Monday this week %s weeks', $weekOffset));

        $weekDays = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->modify(sprintf('+%d days', $i));

            $weekDays[] = [
                'label' => $this->getPolishWeekdayName($date),
                'date' => $date,
            ];
        }
        $endOfWeek = $startOfWeek->modify('+6 day');

        $user = $this->getUser();
        assert($user instanceof User);

        $mealPlanItems = $mealPlanItemRepository->findForUserBetweenDates($user, $startOfWeek, $endOfWeek);
        $plannedItems = [];

        foreach ($mealPlanItems as $mealPlanItem) {
            $dataKey = $mealPlanItem->getPlannedFor()?->format('Y-m-d');
            $mealType = $mealPlanItem->getMealType();

            if(!$dataKey || !$mealType) {
                continue;
            }

            $plannedItems[$dataKey][$mealType][] = $mealPlanItem;
        }

        return $this->render('meal_plan/index.html.twig', [
            'weekDays' => $weekDays,
            'mealTypes' => self::MEAL_TYPES,
            'weekOffset' => $weekOffset,
            'startOfWeek' => $startOfWeek,
            'endOfWeek' => $endOfWeek,
            'plannedItems' => $plannedItems,
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/meal-plan/add/{id}', name: 'app_meal_plan_add', methods: ['POST'])]
    public function add(Recipe $recipe, Request $request, EntityManagerInterface $em): Response {

        if(!$this->isCsrfTokenValid('mealPlan'. $recipe->getId(), $request->request->get('_token'))){
            $this->addFlash('danger', 'Nie udalo się dodać przepisu do planera');

            return $this->redirectAfterMealPlanAction($request, $recipe);
        }

        $plannedFor = $request->request->get('plannedFor');
        $mealType = $request->request->get('mealType');

        if(!$plannedFor || !array_key_exists($mealType, self::MEAL_TYPES)){
            $this->addFlash('danger', 'Wybierz poprawny dzień i typ posiłku.');

            return $this->redirectAfterMealPlanAction($request, $recipe);
        }

        $user = $this->getUser();


        $mealPlanItem = new MealPlanItem();
        $mealPlanItem
            ->setUser($user)
            ->setRecipe($recipe)
            ->setPlannedFor(new DateTimeImmutable($plannedFor))
            ->setMealType($mealType)
            ->setServings($request->request->getInt('targetServings', $recipe->getServings() ?: 1));

        $em->persist($mealPlanItem);
        $em->flush();

        $this->addFlash('success', 'Dodano przepis do planera.');
        return $this->redirectAfterMealPlanAction($request, $recipe);
    }

    #[Route('/meal-plan/remove/{id}', name: 'app_meal_plan_remove', methods: ['POST'])]
    public function remove(MealPlanItem $mealPlanItem, Request $request, EntityManagerInterface $em): Response {

        $user = $this->getUser();
        assert($user instanceof User);

        if($mealPlanItem->getUser() !== $user){
            throw $this->createAccessDeniedException();
        }

        if(!$this->isCsrfTokenValid('removeMealPlanItem'. $mealPlanItem->getId(), $request->request->get('_token'))){
            $this->addFlash('danger', 'Nie udalo się usunąć przepisu do planera');

            return $this->redirectToRoute('app_meal_plan', [
                'week' => $request->request->getInt('week'),
            ]);
        }

        $em->remove($mealPlanItem);
        $em->flush();

        $this->addFlash('success', 'Usunięto posiłek z planera.');

        return $this->redirectToRoute('app_meal_plan', [
            'week' => $request->request->getInt('week'),
        ]);
    }

    private function redirectAfterMealPlanAction(Request $request, Recipe $recipe): Response {
        $redirectTo = $request->request->get('redirectTo');

        if(is_string($redirectTo) && str_starts_with($redirectTo, '/') && !str_starts_with($redirectTo, '//')){
            return $this->redirect($redirectTo);
        }

        return $this->redirectToRoute('app_show', ['id' => $recipe->getId()]);
    }

    private function getPolishWeekdayName(DateTimeImmutable $date): string
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
