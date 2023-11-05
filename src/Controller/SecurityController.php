<?php

namespace App\Controller;

use App\Entity\User;

use App\Form\PasswordChangeType;
use App\Form\RegisterType;
use App\Model\Form\PasswordChange;
use App\Model\Form\Register;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{

    public function __construct(private readonly UserService $userService, private readonly TranslatorInterface $translator)
    {
    }

    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
    }


    #[Route('/register/{hash}', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, User $user, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_homepage');
        }

        if ($user->isActive()) {
            $this->addFlash('info', $this->translator->trans('flash_messages.account_active'));
            return $this->redirectToRoute('app_login');
        }

        $register = new Register();
        $register
            ->setUsername($user->getUsername())
            ->setEmail($user->getEmail());

        $form = $this->createForm(RegisterType::class, $register);
        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            try {

                $user
                    ->setUsername($register->getUsername())
                    ->setEmail($register->getEmail())
                    ->setPassword($passwordHasher->hashPassword($user, $register->getPassword()));

                $this->userService->register($user);
                $this->addFlash('success', $this->translator->trans('flash_messages.account_activated'));
                return $this->redirectToRoute('app_login');

            } catch (\Throwable $e) {
                error_log($e->getMessage());
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
//
//    #[Route('/mot-de-passe/oublie', name: 'app_password_forgot', methods: ['GET', 'POST'])]
//    public function passwordForgot(Request $request, UserRepository $userRepository): Response
//    {
//        if ($this->getUser()) {
//            return $this->redirectToRoute('app_homepage');
//        }
//
//        $form = $this->createFormBuilder(['message' => 'Entrer votre email'])
//            ->add('email', EmailType::class, [
//                'label' => 'Adresse email',
//                'attr' => [
//                    'placeholder' => "Email de connexion",
//                    'autocomplete' => "username"
//                ],
//            ])
//            ->getForm();
//
//        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
//            $data = $form->getData();
//            $user = $userRepository->findOneBy(['email' => $data['email']]);
//            if ($user !== null) {
//                try {
//                    $this->userService->passwordForgot($user);
//                    $this->addFlash('success', "Un email de ré-initialisation vient d'être envoyé à " . $user->getEmail());
//                    return $this->redirectToRoute('app_login');
//                } catch (\Exception $e) {
//                    error_log($user->getEmail() . ' : ' . $e->getMessage());
//                    $this->addFlash('error', 'Désolé, une erreur est survenue');
//                }
//
//            } else {
//                $this->addFlash('error', 'Utilisateur inconnu');
//            }
//        }
//
//        return $this->render('security/password_forgot.html.twig', [
//            'form' => $form->createView()
//        ]);
//    }
//
//    #[Route('/mot-de-passe/re-initialisation/{hash}', name: 'app_password_reset', methods: ['GET', 'POST'])]
//    public function passwordReset(Request $request, User $user): Response
//    {
//        if ($this->getUser()) {
//            return $this->redirectToRoute('app_homepage');
//        }
//
//        $password = (new PasswordReset())->setEmail($user->getEmail());
//        $form = $this->createForm(PasswordResetType::class, $password);
//        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
//
//            try {
//                $this->userService->passwordChange($user, $password->getPassword());
//                $this->addFlash('success', 'Votre mot de passe a été changé avec succès, vous pouvez maintenant vous connecter.');
//                return $this->redirectToRoute('app_login');
//            } catch (\Throwable $e) {
//                error_log($user->getEmail() . ' : ' . $e->getMessage());
//                $this->addFlash('error', 'Désolé, une erreur est survenue.');
//            }
//        }
//
//        return $this->render('security/password_reset.html.twig', [
//            'form' => $form->createView(),
//        ]);
//    }
//
    #[Route('/password/change', name: 'app_password_change', methods: ['GET', 'POST'])]
    public function passwordChange(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $password = new PasswordChange();
        $form = $this->createForm(PasswordChangeType::class, $password);
        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {

            if ($passwordHasher->isPasswordValid($user, $password->getCurrentPassword()) === false) {
                $form->get('currentPassword')->addError(new FormError($this->translator->trans('change_password.old_password_invalid')));
            } else {
                try {
                    $this->userService->passwordChange($user, $password->getPassword());
                    $this->addFlash('success', $this->translator->trans('change_password.success'));
                    return $this->redirectToRoute('app_homepage');
                } catch (\Throwable $e) {
                    error_log($user->getEmail() . ' : ' . $e->getMessage());
                    $this->addFlash('error', $this->translator->trans('change_password.error'));
                }
            }
        }

        return $this->render('security/password_change.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
