<?php

namespace App\Form;

use App\Model\Form\PasswordChange;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class PasswordChangeType extends AbstractType
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'label' => 'password.old',
                'attr' => [
                    'placeholder' => 'password.old',
                ]
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
                        'row_attr' => [
                            'class' => 'form-floating',
                        ],
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
                        'row_attr' => [
                            'class' => 'form-floating',
                        ],
                        'label' => 'register.password_confirm',
                        'attr' => [
                            'placeholder' => 'register.password_confirm',
                        ]
                    ],
                ]
            )
            ->add('save', SubmitType::class, [
                'label' => 'password.button_save'
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
                'data_class' => PasswordChange::class,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'passwordChangeForm';
    }
}