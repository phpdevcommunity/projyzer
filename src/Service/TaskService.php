<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class TaskService
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly MailerService         $mailerService,
        private readonly Environment           $twig,
        private readonly TranslatorInterface   $translator,
        private readonly Security   $security,
    )
    {
    }

    public function notify(Task $task): void
    {
        $users = [];
        $users[] = $task->getCreatedBy();
        $users[] = $task->getAssignedTo();
        $users[] = $task->getUpdatedBy();
        $users = array_merge($users, $task->getUsers()->toArray());

        $addresses = [];
        foreach ($users as $user) {
            if ($user instanceof User) {
                $addresses[$user->getEmail()] = new Address($user->getEmail());
            }
        }

        $currentUser =  $task->getUpdatedBy();
        $project = $task->getProject();
        $this->mailerService->send(
            (new Email())
                ->to(...array_values($addresses))
                ->subject(
                    $this->translator->trans(
                        'task_update_subject',
                        ['%project%'=> $project->getName(), '%task%' => sprintf('#%s', $task->getId())]
                    )
                )
                ->html($this->twig->render('email/task_notify.html.twig', [
                    'urlTask' => $this->urlGenerator->generate('tasks_get', ['task' => $task->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                    'task' => $task,
                    'project' => $task->getProject(),
                    'user' => $currentUser,
                ]))
        );
    }
}
