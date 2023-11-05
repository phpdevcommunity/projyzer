<?php

namespace App\Controller\Admin;

use App\Entity\Organization;
use App\Entity\OrganizationUnit;
use App\Entity\ProjectCategoryReference;
use App\Entity\ProjectCategoryReferenceStatus;
use App\Entity\TaskCategoryReference;
use App\Entity\TaskStatusReference;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle('Projyzer');
    }

    public function configureMenuItems(): iterable
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        yield MenuItem::linkToDashboard('Dashboard', 'fas fa-home');
        yield MenuItem::linkToRoute("Back to Application", 'fas fa-undo-alt', 'app_homepage');
        yield MenuItem::section('Organizations');
        yield MenuItem::linkToCrud('Manage Organizations', 'fas fa-sitemap', Organization::class);
        yield MenuItem::linkToCrud('Manage Organization Units', 'fas fa-sitemap', OrganizationUnit::class);
        yield MenuItem::section('Projects and Tasks');
        yield MenuItem::linkToCrud('Project Categories', 'fas fa-list', ProjectCategoryReference::class);
        yield MenuItem::linkToCrud('Task Categories', 'fas fa-list', TaskCategoryReference::class);
        yield MenuItem::linkToCrud('Task Statuses', 'fas fa-list', TaskStatusReference::class);
        yield MenuItem::linkToCrud('Tasks in Categories', 'fas fa-list', ProjectCategoryReferenceStatus::class);
        yield MenuItem::section('Users');
        yield MenuItem::linkToCrud('Manage Users', 'fas fa-user', User::class);
    }

    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('css/admin.css');
    }
}
