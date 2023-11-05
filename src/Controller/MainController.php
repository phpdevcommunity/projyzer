<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Repository\ProjectUserRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
//    private UserService $userService;
//
//    public function __construct(UserService $userService)
//    {
//        $this->userService = $userService;
//    }

    #[Route('', name: 'app_homepage')]
    public function index(
        ProjectRepository $projectRepository,
        ProjectUserRepository $projectUserRepository,
        UserRepository $userRepository,
        TaskRepository $taskRepository
    ): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $projects = $projectRepository->findBy([], ['id' => 'DESC'], 5);
            $projectsCount = $projectRepository->count([]);
            $usersCount = $userRepository->count(['active' => true]);
            $tasksCount = $taskRepository->count(['active' => true]);
        } elseif ($this->isGranted('ROLE_ADMIN')) {
            $projects = $projectRepository->findBy(['organizationUnit' => $user->getOrganizationUnit()], ['id' => 'DESC'], 5);
            $projectsCount = $projectRepository->count(['organizationUnit' => $user->getOrganizationUnit()]);
            $usersCount = $userRepository->count(['active' => true, 'organizationUnit' => $user->getOrganizationUnit()]);
            $tasksCount = $taskRepository->countByOrganization($user->getOrganizationUnit());
        } else {
            $projects = [];
            foreach ($projectUserRepository->findAllByUser($user, 5) as $projectUser) {
                $projects[] = $projectUser->getProject();
            }
            $projectsCount = $projectUserRepository->countByUser($user);
            $tasksCount = $taskRepository->countByUser($user);
            $usersCount = null;
        }


        return $this->render('main/index.html.twig', [
            'projects' => $projects,
            'users_count' => $usersCount,
            'tasks_count' => $tasksCount,
            'projects_count' => $projectsCount
        ]);
    }
}
