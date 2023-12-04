<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\OrganizationUnit;
use App\Entity\Project;
use App\Entity\ProjectCategoryReference;
use App\Entity\ProjectCategoryReferenceStatus;
use App\Entity\ProjectFile;
use App\Entity\TaskStatusReference;
use App\Entity\User;
use App\Form\ProjectType;
use App\Repository\ProjectCategoryReferenceRepository;
use App\Repository\ProjectRepository;
use App\Repository\ProjectUserRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/projects')]
class ProjectController extends AbstractController
{

    public function __construct(private readonly EntityManagerInterface $em, private readonly TranslatorInterface $translator)
    {
    }

    #[Route('', name: 'projects_index')]
    public function index(ProjectRepository $projectRepository, ProjectUserRepository $projectUserRepository): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $projects = $projectRepository->findBy([], ['id' => 'DESC']);
        }
        elseif ($this->isGranted('ROLE_ADMIN')) {
            $projects = $projectRepository->findByOrganizationUnit($user->getOrganizationUnit());
        }
        else {
            $projects = [];
            foreach ($projectUserRepository->findAllByUser($user) as $projectUser) {
                $projects[] = $projectUser->getProject();
            }
        }

        return $this->render('project/index.html.twig', [
            'projects' => $projects
        ]);
    }

    #[Route('/orgUnit/{organizationUnit}/create', name: 'projects_add')]
    public function add(Request $request, OrganizationUnit $organizationUnit, ProjectCategoryReferenceRepository $projectCategoryReferenceRepository): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($user->getOrganizationUnit() !== $organizationUnit) {
            throw new AccessDeniedHttpException();
        }

        if ($projectCategoryReferenceRepository->count(['organizationUnit' => $organizationUnit]) === 0) {
            $this->addFlash('error', $this->translator->trans('not_exist_project_category_reference'));
            return $this->redirectToRoute('projects_index');
        }

        return $this->form(
            $request,
            (new Project())
                ->setOrganizationUnit($organizationUnit)
                ->setActive(true)
                ->setCreatedBy($user)
        );
    }

    #[Route('/edit/{project}', name: 'projects_edit')]
    public function edit(Request $request, Project $project): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$project->canEditProject($user)) {
            throw new AccessDeniedHttpException();
        }

        return $this->form($request, $project);
    }

    private function form(Request $request, Project $project): Response
    {
        $form = $this->createForm(ProjectType::class, $project, [
            'organization_unit' => $project->getOrganizationUnit()
        ]);
        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {

            /**
             * @var UploadedFile $uploadedFile
             */
            foreach ($form->get('files')->getData() as $uploadedFile) {
                $projectFile = (new ProjectFile())
                    ->setFile(
                        (new File())
                            ->setName($uploadedFile->getClientOriginalName())
                            ->setSize($uploadedFile->getSize())
                            ->setMimeType($uploadedFile->getMimeType())
                            ->setContent($uploadedFile->getContent())
                    );
                $project->addFile($projectFile);
            }

            $this->em->persist($project);
            $this->em->flush();
            $this->addFlash('success', $this->translator->trans('project_saved_successfully'));
            return $this->redirectToRoute('projects_get', ['slug' => $project->getSlug()]);
        }

        return $this->render('project/form.html.twig', [
            'form' => $form->createView(),
            'project' => $project
        ]);
    }

    #[Route('/{slug}', name: 'projects_get')]
    public function project(Project $project, TaskRepository $taskRepository): Response
    {

        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$project->canAccess($user)) {
            throw new AccessDeniedHttpException();
        }

        /**
         * @var array<ProjectCategoryReferenceStatus> $statuses
         */
        $statuses = $project->getProjectCategoryReference()?->getProjectCategoryReferenceStatuses() ?: [];

        return $this->render('project/project.html.twig', [
            'project' => $project,
            'tasks' => $taskRepository->findByProject($project),
            'statuses' => $statuses
        ]);
    }

    #[Route('/close/{project}', name: 'projects_close')]
    public function close(Request $request, Project $project): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$project->canCloseProject($user)) {
            throw new AccessDeniedHttpException();
        }

        $project->setActive(false);
        $this->em->flush();
        $this->addFlash('success', $this->translator->trans('project_closed_successfully', ['%project_id%' => $project->getId()]));

        return $this->redirectReferer($request);
    }

    #[Route('/open/{project}', name: 'projects_open')]
    public function open(Request $request, Project $project): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$project->canCloseProject($user)) {
            throw new AccessDeniedHttpException();
        }

        $project->setActive(true);
        $this->em->flush();
        $this->addFlash('success', $this->translator->trans('project_reopened_successfully', ['%project_id%' => $project->getId()]));

        return $this->redirectReferer($request);
    }

    #[Route('/file/{uid}', name: 'projects_file', methods: ['GET'])]
    public function projectFile(Request $request, projectFile $projectFile): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $project = $projectFile->getProject();
        if (!$project->canAccess($user)) {
            throw new AccessDeniedHttpException();
        }

        $file = $projectFile->getFile();
        if (!$file instanceof File) {
            throw new NotFoundHttpException();
        }
        $response = new Response(stream_get_contents($file->getContent()));

        if ($file->getMimeType() == 'application/pdf') {
            $response->headers->set('Content-Type', 'application/pdf');
            $disposition = HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_INLINE,
                $file->getName()
            );
        } else {
            $disposition = HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                $file->getName()
            );
        }

        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }

    private function redirectReferer(Request $request): Response
    {
        $referer = $request->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('projects_index');
    }
}
