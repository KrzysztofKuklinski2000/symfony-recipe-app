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
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

#[IsGranted('ROLE_ADMIN')]
#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private RecipeRepository $recipeRepository,
        private CommentRepository $commentRepository,
        private CategoryRepository $categoryRepository,
        private UserRepository $userRepository,
        private AdminUrlGenerator $urlGenerator,
        )
    {

    }
    public function index(): Response
    {
        $commentsUrl = $this->urlGenerator
            ->setController(CommentCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        $usersUrl = $this->urlGenerator
            ->setController(UserCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->render('admin/dashboard.html.twig', [
            'users_count' => $this->userRepository->count(),
            'recipes_count' => $this->recipeRepository->count(),
            'comments_count' => $this->commentRepository->count(),
            'categories_count' => $this->categoryRepository->count(),

            'latest_comments' => $this->commentRepository->findBy([], ['createdAt' => 'DESC'], 5),
            'latest_users' => $this->userRepository->findBy([], ['id' => 'DESC'], 5),

            'comments_url' => $commentsUrl,
            'users_url' => $usersUrl,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Moje przepisy');
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
