<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {

        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Moja przepisy');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Zarządzanie przepisami');
        yield MenuItem::linkToCrud("Przepisy", 'fas fa-utensils', Recipe::class);

        yield MenuItem::section('Aplikacja');
        yield MenuItem::linkToCrud('Wróć do strony', 'fas fa-arrow-left', Recipe::class)
            ->setRoute('app_home', []);
    }
}
