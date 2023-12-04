<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Project;
use App\Entity\ProjectCategoryReferenceStatus;
use App\Entity\Task;
use App\Entity\TaskComment;
use App\Entity\TaskCommentFile;
use App\Entity\TaskFile;
use App\Entity\TaskStatusReference;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\ProjectCategoryReferenceStatusRepositoryRepository;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/tasks')]
class TaskController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly TranslatorInterface $translator)
    {
    }

    #[Route('', name: 'tasks_index')]
    public function index(TaskRepository $taskRepository): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        return $this->render('task/index.html.twig', [
            'tasks' => $taskRepository->findByUser($user)
        ]);
    }

    #[Route('/{task}', name: 'tasks_get', methods: ['GET'])]
    #[Route('/{task}/comment', name: 'tasks_comment', methods: ['POST'])]
    public function task(Request $request, Task $task): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $project = $task->getProject();
        if (!$task->canAccess($user)) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createFormBuilder(null, [
            'action' => $this->generateUrl('tasks_comment', ['task' => $task->getId()])
        ])
            ->add('comment', TextareaType::class, [
                "label" => false,
                'attr' => [
                    'placeholder' => $this->translator->trans('add_a_comment'),
                    'rows' => 5
                ]
            ])
            ->add('files', FileType::class, [
                'required' => false,
                'multiple' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'submit'
            ])
            ->getForm();

        if ($project->isActive() && $form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $comment = (new TaskComment())
                ->setUser($user)
                ->setContent($form->get('comment')->getData());

            /**
             * @var UploadedFile $uploadedFile
             */
            foreach ($form->get('files')->getData() as $uploadedFile) {
                $taskCommentFile = (new TaskCommentFile)
                    ->setFile(
                        (new File())
                            ->setName($uploadedFile->getClientOriginalName())
                            ->setSize($uploadedFile->getSize())
                            ->setMimeType($uploadedFile->getMimeType())
                            ->setContent($uploadedFile->getContent())
                    );
                $comment->addFile($taskCommentFile);
            }
            $task->addComment($comment);
            $this->em->persist($task);
            $this->em->flush();
            $this->addFlash('success', $this->translator->trans('comment_added_successfully'));
            return $this->redirectToRoute('tasks_get', ['task' => $task->getId()]);
        }

        return $this->render('task/task.html.twig', [
            'task' => $task,
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/projects/{project}/create', name: 'tasks_add')]
    public function add(Request $request, Project $project): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if (!$project->canCreateTask($user)) {
            throw new AccessDeniedHttpException();
        }

        if ($project->isActive() === false) {
            $this->addFlash('error', $this->translator->trans('project_closed_error'));
//            $this->addFlash('error', "Désolé, le projet est actuellement fermé, vous ne pouvez pas ajouter de nouvelles tâches ou tickets.");
            return $this->redirectToRoute('projects_get', ['slug' => $project->getSlug()]);
        }

        /**
         * @var Collection<ProjectCategoryReferenceStatus> $statuses
         */
        $statuses = $project->getProjectCategoryReference()->getProjectCategoryReferenceStatuses()->filter(function (ProjectCategoryReferenceStatus $status) {
            return $status->getIsInitial() === true;
        });
        if ($statuses->isEmpty()) {
            $categoryLabel = $project->getProjectCategoryReference()->getLabel();
            $this->addFlash('error', $this->translator->trans('category_initialization_error', ['%category_label%' => $categoryLabel]));
//            $this->addFlash('error',
//                sprintf("Vous devez définir un statut d'initialisation par défaut pour la catégorie : %s", $categoryLabel)
//            );
            return $this->redirectToRoute('projects_get', ['slug' => $project->getSlug()]);
        }
        $task = (new Task())
            ->setTaskStatusReference($statuses[0]->getTaskStatusReference())
            ->setProject($project)
            ->setCreatedBy($user);

        return $this->form($request, $task);
    }

    #[Route('/edit/{task}', name: 'tasks_edit')]
    public function edit(Request $request, Task $task): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $users = $task->getProject()->getUsers();
        if (!$task->canEdit($user)) {
            throw new AccessDeniedHttpException();
        }

        $taskStatusReference = $this->em->getRepository(TaskStatusReference::class)->findOneBy(['label' => $task->getLastStatus()?->getLabel()]);
        $task->setTaskStatusReference($taskStatusReference);
        return $this->form($request, $task);
    }

    private function form(Request $request, Task $task): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $form = $this->createForm(TaskType::class, $task, [
            'project_category_reference' => $task->getProject()->getProjectCategoryReference(),
            'task' => $task
        ]);
        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {

            /**
             * @var User $user
             */
            $user = $this->getUser();
            $task->getLastStatus()?->setUser($user);


            /**
             * @var UploadedFile $uploadedFile
             */
            foreach ($form->get('files')->getData() as $uploadedFile) {
                $taskFile = (new TaskFile())
                    ->setFile(
                        (new File())
                            ->setName($uploadedFile->getClientOriginalName())
                            ->setSize($uploadedFile->getSize())
                            ->setMimeType($uploadedFile->getMimeType())
                            ->setContent($uploadedFile->getContent())
                    );
                $task->addFile($taskFile);
            }

            if ($task->getId()) {
                $task->setUpdatedBy($user);
            }

            $this->em->persist($task);
            $this->em->flush();
            $this->addFlash('success', $this->translator->trans('task_saved_successfully'));

            $referer = $request->query->get('redirect_url');
            if ($referer) {
                return $this->redirect($referer);
            }
            return $this->redirectToRoute('tasks_get', ['task' => $task->getId()]);
        }

        return $this->render('task/form.html.twig', [
            'form' => $form->createView(),
            'task' => $task
        ]);
    }

    #[Route('/comment-file/{taskCommentFile}', name: 'tasks_comment_file', methods: ['GET'])]
    public function taskCommentFile(Request $request, TaskCommentFile $taskCommentFile): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $task = $taskCommentFile->getComment()->getTask();
        if (!$task->canEdit($user)) {
            throw new AccessDeniedHttpException();
        }
        $file = $taskCommentFile->getFile();
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

    #[Route('/file/{uid}', name: 'tasks_file', methods: ['GET'])]
    public function taskFile(Request $request, taskFile $taskFile): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $file = $taskFile->getFile();
        if (!$file instanceof File) {
            throw new NotFoundHttpException();
        }

        $task = $taskFile->getTask();
        if (!$task->canAccess($user)) {
            throw new AccessDeniedHttpException();
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

    #[Route('/{task}/completed', name: 'tasks_change_completed', methods: ['POST'])]
    public function changeCompleted(Request $request, Task $task): Response
    {

        /**
         * @var User $user
         */
        $user = $this->getUser();

        $response = $this->redirectToRoute('tasks_get', ['task' => $task->getId()]);
        $referer = $request->query->get('redirect_url');
        if ($referer) {
            $response = $this->redirect($referer);
        }

        $percentageCompleted = $request->request->get('percentageCompleted');
        if (!is_numeric($percentageCompleted)) {
            $this->addFlash('error', $this->translator->trans('invalid_number_value'));
//            $this->addFlash('error', 'La valeur doit être un nombre valide');
            return $response;
        }
        $task->setPercentageCompleted((int)$percentageCompleted);
        $this->em->flush();
        $this->addFlash('success', $this->translator->trans('update_successful'));
        return $response;
    }

    #[Route('/{task}/status', name: 'tasks_change_status', methods: ['POST'])]
    public function changeStatus(Request $request, Task $task): Response
    {

        /**
         * @var User $user
         */
        $user = $this->getUser();

        $response = $this->redirectToRoute('tasks_get', ['task' => $task->getId()]);
        $referer = $request->query->get('redirect_url');
        if ($referer) {
            $response = $this->redirect($referer);
        }

        $id = $request->request->get('projectCategoryReferenceStatusId');
        if (!is_numeric($id)) {
            $this->addFlash('error', $this->translator->trans('invalid_identifier'));
            return $response;
        }
        $projectCategoryReferenceStatus = $this->em->getRepository(ProjectCategoryReferenceStatus::class)->find($id);
        if (!$projectCategoryReferenceStatus instanceof ProjectCategoryReferenceStatus) {
            $this->addFlash('error', $this->translator->trans('status_not_exist_for_project_category'));
            return $response;
        }

        $task->setTaskStatusReference($projectCategoryReferenceStatus->getTaskStatusReference());
        $this->em->flush();
        $this->addFlash('success', $this->translator->trans('update_successful'));
        return $response;
    }
}
