<?php

namespace App\Form\Install;

use App\Model\Install\InstallationData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class InstallationDataType extends AbstractType
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('organizationName', TextType::class, [
                'label' => 'organizationName',
                'attr' => [
                    'placeholder' => 'Google'
                ]
            ])
            ->add('organizationIdentifier', TextType::class, [
                'label' => 'organizationIdentifier',
                'attr' => [
                    'placeholder' => '12345678900012'
                ]
            ])
            ->add('username', TextType::class, [
                'label' => 'username_connexion'
            ])
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => $this->translator->trans('register.password_mismatch'),
                    'required' => true,
                    'help' => 'register.password_help',
                    'first_options' => [
                        'label' => 'register.password_new',
                        'attr' => [
                            'pattern' => "(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%;:^&*.?]).{8,}",
                            'help' => 'register.password_help',
                            'minlength' => 8,
                            'placeholder' => 'register.password_new',
                            'autocomplete' => "current-password"
                        ]
                    ],
                    'second_options' => [
                        'label' => 'register.password_confirm',
                        'attr' => [
                            'placeholder' => 'register.password_confirm',
                        ]
                    ],
                ]
            )
            ->add('email', EmailType::class, [
                'label' => 'installation.email',
                'attr' => [
                    'placeholder' => 'example@example.com'
                ]
            ])
            ->add('lastname', TextType::class, [
                'required' => false,
                'label' => 'lastname',
                'attr' => [
                    'placeholder' => 'Doe'
                ]
            ])
            ->add('firstname', TextType::class, [
                'required' => false,
                'label' => 'firstname',
                'attr' => [
                    'placeholder' => 'John'
                ]
            ])
            ->add('projectCategoriesInitialisation', TextareaType::class, [

                'help' => 'Format .ini',
                'required' => false,
                'label' => 'projectCategoriesInitialisation',
                "attr" => [
                    'rows' => 8,
                    'placeholder' => "Category1 = \"Description de la catégorie 1\"\nCategory2 = \"Description de la catégorie 2\"",
                ]
            ])
            ->add('taskCategoriesInitialisation', TextareaType::class, [
                'help' => 'Format .ini',
                'required' => false,
                'label' => 'taskCategoriesInitialisation',
                "attr" => [
                    'rows' => 8,
                    'placeholder' => "TaskCategory1 = \"Description de la catégorie de tâches 1\"\nTaskCategory2 = \"Description de la catégorie de tâches 2\"",
                ]
            ])
            ->add('taskStatusesInitialisation', TextareaType::class, [
                'help' => 'Format .ini',
                'required' => false,
                'label' => 'taskStatusesInitialisation',
                "attr" => [
                    'rows' => 8,
                    'placeholder' => "Status1 = \"Description du statut 1\"\nStatus2 = \"Description du statut 2\"",
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'installation.button_save'
            ]);
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => InstallationData::class,
            ]
        );
    }

}