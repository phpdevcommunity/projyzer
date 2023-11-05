<?php

namespace App\Form;

use App\Entity\OrganizationUnit;
use App\Entity\Project;
use App\Entity\ProjectCategoryReference;
use App\Entity\ProjectUser;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.id', 'ASC');
                },
                'group_by' => function(User $user, $key, $value) {
                    return $user->getOrganizationUnit()->getName();
                },
                'choice_label' => 'username',
            ])
            ->add('permissions', ChoiceType::class, [
                'choices' => [
                    ProjectUser::FULL_PRIVILEGE => ProjectUser::FULL_PRIVILEGE,
                    ProjectUser::CAN_CREATE_TASK => ProjectUser::CAN_CREATE_TASK,
                    ProjectUser::CAN_EDIT_PROJECT => ProjectUser::CAN_EDIT_PROJECT,
                    ProjectUser::CAN_CLOSE_PROJECT => ProjectUser::CAN_CLOSE_PROJECT,
                    ProjectUser::CAN_DELETE_PROJECT => ProjectUser::CAN_DELETE_PROJECT,
                ],
                'attr' => ['data-ea-widget' => 'ea-autocomplete'],
                'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjectUser::class,
            'organization_unit' => ProjectUser::class,
        ]);
    }
}
