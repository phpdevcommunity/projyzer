<?php

namespace App\Form;

use App\Entity\ProjectUser;
use App\Entity\TaskUser;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'group_by' => function(User $user, $key, $value) {
                    return $user->getOrganizationUnit()->getName();
                },
            ])
            ->add('permissions', ChoiceType::class, [
                'choices' => [
                    TaskUser::FULL_PRIVILEGE => TaskUser::FULL_PRIVILEGE,
                    TaskUser::CAN_EDIT_TASK => TaskUser::CAN_EDIT_TASK,
                    TaskUser::CAN_COMMENT => TaskUser::CAN_COMMENT,
                ],
                'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TaskUser::class,
        ]);
    }
}
