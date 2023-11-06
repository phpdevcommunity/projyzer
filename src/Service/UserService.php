<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class UserService
{

    public function __construct(
        private readonly UserRepository              $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly MailerService $mailerService,
        private readonly Environment $twig,
        private readonly TranslatorInterface $translator,
    )
    {
    }

    public function register(User $user): void
    {
        $user
            ->setHash(null)
            ->setActive(true);

        $this->userRepository->save($user, true);
    }

    public function sendEmailRegistration(User $user): void
    {
        if ($user->isActive()) {
            return;
        }

        if (!$user->isSendActivationEmail()) {
            return;
        }

        $user->generateHash();
        $this->mailerService->send(
            (new Email())
                ->to(new Address($user->getEmail()))
                ->subject($this->translator->trans('email.subject_activation_account'))
                ->html($this->twig->render('email/active_account.html.twig', [
                    'activationLink' => $this->urlGenerator->generate('app_register', ['hash' => $user->getHash()], UrlGeneratorInterface::ABSOLUTE_URL),
                    'user' => $user,
                ]))
        );
        $user->setSendActivationEmail(false);
        $this->userRepository->save($user, true);
    }

//    public function passwordForgot(User $user):void
//    {
//        if ($user->getId() === null) {
//            throw new \InvalidArgumentException('id user can not be null');
//        }
//        $user->generateHash();
//        $this->userRepository->save($user, true);
//
//        $this->mailerService->send(
//            (new Email())
//                ->to(new Address($user->getEmail()))
//                ->subject("[NOREPLY] Demande de ré-initialisation de mot de passe")
//                ->html($this->twig->render('email/password_forgot.html.twig', [
//                    'urlResetPassword' => $this->urlGenerator->generate('app_password_reset', ['hash' => $user->getHash()], UrlGeneratorInterface::ABSOLUTE_URL),
//                    'user' => $user
//                ]))
//        );
//    }

    public function passwordChange(User $user, string $newPlainPassword): void
    {
        if ($user->getId() === null) {
            throw new \InvalidArgumentException('id user can not be null');
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $newPlainPassword));
        $this->userRepository->save($user, true);
    }
}
