<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\OrganizationUnit;
use App\Entity\ProjectCategoryReference;
use App\Entity\Setting;
use App\Entity\TaskCategoryReference;
use App\Entity\TaskStatusReference;
use App\Entity\User;

use App\Form\Install\InstallationDataType;
use App\Form\PasswordChangeType;
use App\Form\RegisterType;
use App\Model\Form\PasswordChange;
use App\Model\Form\Register;
use App\Model\Install\InstallationData;
use App\Repository\OrganizationRepository;
use App\Repository\SettingRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly SettingRepository   $settingRepository,
        private readonly UserService         $userService,
        private readonly TranslatorInterface $translator
    )
    {
    }

    #[Route('/installation', name: 'app_installation')]
    public function installation(
        Request                     $request,
        EntityManagerInterface      $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $setting = $this->settingRepository->findOneBy(['key' => Setting::KEY_INSTALLATION_REQUIRED]);
        if ((bool)$setting->getValue() === false) {
            throw new AccessDeniedHttpException();
        }
        $install = new InstallationData();
        $form = $this->createForm(InstallationDataType::class, $install);
        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {

            $data = [];
            $error = false;
            if ($install->getProjectCategoriesInitialisation()) {
                $data['projectCategoriesInitialisation'] = parse_ini_string($install->getProjectCategoriesInitialisation());
            }
            if ($install->getTaskCategoriesInitialisation()) {
                $data['taskCategoriesInitialisation'] = parse_ini_string($install->getTaskCategoriesInitialisation());
            }
            if ($install->getTaskStatusesInitialisation()) {
                $data['taskStatusesInitialisation'] = parse_ini_string($install->getTaskStatusesInitialisation());
            }

            foreach ($data as $fieldName => $config) {
                if ($config === false || $config === []) {
                    $errorMessage = $this->translator->trans('The content is not a valid INI format.');
                    $form->get($fieldName)->addError(new FormError($errorMessage));
                    $error = true;
                }
            }

            if ($error === false) {
                $organization = (new Organization())
                    ->setName($install->getOrganizationName())
                    ->setIdentifier($install->getOrganizationIdentifier());

                $em->persist($organization);
                $em->flush($organization);

                $organizationUnit = $em->getRepository(OrganizationUnit::class)->findOneBy(['organization' => $organization]);

                $user = (new User())
                    ->setUsername($install->getUsername())
                    ->setEmail($install->getEmail())
                    ->setActive(true)
                    ->setLastname($install->getLastname())
                    ->setFirstname($install->getFirstname())
                    ->setRoles(['ROLE_ADMIN'])
                    ->setOrganizationUnit($organizationUnit);
                $user->setPassword($passwordHasher->hashPassword($user, $install->getPassword()));

                $em->persist($user);
                $setting->setValue(false);
                $em->flush();

                try {
                    foreach ($data['projectCategoriesInitialisation'] as $label => $description) {
                        $projectCategory = (new ProjectCategoryReference())
                            ->setOrganizationUnit($organizationUnit)
                            ->setLabel($label)
                            ->setDescription($description);
                        $em->persist($projectCategory);
                    }
                    $em->flush();

                    foreach ($data['taskCategoriesInitialisation'] as $label => $description) {
                        $taskCategory = (new TaskCategoryReference())
                            ->setOrganizationUnit($organizationUnit)
                            ->setLabel($label)
                            ->setDescription($description);
                        $em->persist($taskCategory);
                    }
                    $em->flush();

                    foreach ($data['taskStatusesInitialisation'] as $label => $description) {
                        $taskCategory = (new TaskStatusReference())
                            ->setOrganizationUnit($organizationUnit)
                            ->setLabel($label)
                            ->setDescription($description);
                        $em->persist($taskCategory);
                    }

                    $em->flush();
                } catch (Throwable $e) {
                    $this->addFlash('error', $this->translator->trans('initialisation.error'));
                }

                $this->addFlash('success', '🚀 ' . $this->translator->trans('installation.ok'));
                return $this->redirectToRoute('app_homepage');
            }
        }


        return $this->render('security/installation.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils,): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_homepage');
        }

        $setting = $this->settingRepository->findOneBy(['key' => Setting::KEY_INSTALLATION_REQUIRED]);
        if ((bool)$setting->getValue() === true) {
            return $this->redirectToRoute('app_installation');
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

            } catch (Throwable $e) {
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
                } catch (Throwable $e) {
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
