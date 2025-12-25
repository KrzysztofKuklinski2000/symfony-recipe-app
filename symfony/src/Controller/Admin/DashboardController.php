<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Comment;
use App\Entity\Category;
use App\Repository\UserRepository;
use App\Repository\RecipeRepository;
use App\Repository\CommentRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

#[IsGranted('ROLE_ADMIN')]
#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(private RecipeRepository $recipeRepository, private CommentRepository $commentRepository, private CategoryRepository $categoryRepository, private UserRepository $userRepository)
    {

    }
    public function index(): Response
    {
       return $this->render('admin/dashboard.html.twig', [
            'users_count' => $this->userRepository->count(),
            'recipes_count' => $this->recipeRepository->count(),
            'comments_count' => $this->commentRepository->count(),
            'categories_count' => $this->categoryRepository->count(),
       ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Moja przepisy');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Użytkownicy', 'fas fa-users', User::class);

        yield MenuItem::section('Zarządzanie przepisami');
        yield MenuItem::linkToCrud("Przepisy", 'fas fa-utensils', Recipe::class);
        yield MenuItem::linkToCrud('Kategorie', 'fas fa-tags', Category::class);
        yield MenuItem::linkToCrud('Komentarze', 'fas fa-comments', Comment::class);

        yield MenuItem::section('Aplikacja');
        yield MenuItem::linkToRoute('Wróć do strony', 'fas fa-arrow-left', 'app_home');
    }
}
