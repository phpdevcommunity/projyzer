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

class InstallationDataType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('organizationName', TextType::class, [
                'label' => 'organizationName'
            ])
            ->add('organizationIdentifier', TextType::class, [
                'label' => 'organizationIdentifier'
            ])
            ->add('username', TextType::class, [
                'label' => 'username'
            ])
            ->add('email', EmailType::class, [
                'label' => 'email'
            ])
            ->add('lastname', TextType::class, [
                'required' => false,
                'label' => 'lastname'
            ])
            ->add('firstname', TextType::class, [
                'required' => false,
                'label' => 'firstname'
            ])
            ->add('projectCategoriesInitialisation', TextareaType::class, [
                'required' => false,
                'label' => 'projectCategoriesInitialisation',
                "attr" => [
                    'rows' => 8
                ]
            ])
            ->add('taskCategoriesInitialisation', TextareaType::class, [
                'required' => false,
                'label' => 'taskCategoriesInitialisation',
                "attr" => [
                    'rows' => 8
                ]
            ])
            ->add('taskStatusesInitialisation', TextareaType::class, [
                'required' => false,
                'label' => 'taskCategoriesInitialisation',
                "attr" => [
                    'rows' => 8
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