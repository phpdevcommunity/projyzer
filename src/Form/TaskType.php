<?php

namespace App\Form;

use App\Entity\OrganizationUnit;
use App\Entity\ProjectCategoryReference;
use App\Entity\ProjectCategoryReferenceStatus;
use App\Entity\Task;
use App\Entity\TaskCategoryReference;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        /**
         * @var ProjectCategoryReference $projectCategoryReference
         * @var Task|null $task
         */
        $projectCategoryReference = $options['project_category_reference'];
        $task = $options['task'];
        $project = $task->getProject();
        $users = array_merge($project->getUsers()->toArray(), $task->getUsers()->toArray());

        $builder
            ->add('taskStatusReference', ChoiceType::class, [
                'label' => 'Status',
                'choice_label' => 'label',
                'choices' => $projectCategoryReference->getStatuses()->map(function (ProjectCategoryReferenceStatus $projectCategoryReferenceStatus) {
                    return $projectCategoryReferenceStatus->getTaskStatusReference();
                }),
                'required' => true,
            ])
            ->add('title', TextType::class, [
                'label' => 'title'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'description',
                "attr" => [
                    'rows' => 8
                ]
            ])
            ->add('priority', ChoiceType::class, [
                'choices' => [
                    Task::PRIORITY_LOW => Task::PRIORITY_LOW,
                    Task::PRIORITY_MEDIUM => Task::PRIORITY_MEDIUM,
                    Task::PRIORITY_HIGH => Task::PRIORITY_HIGH,
                ]
            ])
            ->add('estimatedTime', NumberType::class, [
                'required' => false,
                'html5' => true,
                'attr' => [
                    'step' => 1,
                    'min' => 0
                ]
            ])
            ->add('actualTime', NumberType::class, [
                'required' => false,
                'html5' => true,
                'attr' => [
                    'step' => 1,
                    'min' => 0
                ]
            ])
            ->add('percentageCompleted', ChoiceType::class, [
                'choices' => [
                    '0 %' => 0,
                    '25 %' => 25,
                    '50 %' => 50,
                    '75 %' => 75,
                    '100 %' => 100,
                ]
            ])
            ->add('assignedTo', ChoiceType::class, [
                'choice_label' => 'username',
                'choices' => $users,
                'group_by' => function(User $user, $key, $value) {
                    return $user->getOrganizationUnit()->getName();
                },
                'required' => false
            ])
            ->add('taskCategoryReference', EntityType::class, [
                'label' => 'Category',
                'class' => TaskCategoryReference::class,
                'choice_label' => 'label',
                'required' => true
            ])
            ->add('taskUsers', CollectionType::class, [
                'label' => false,
                'entry_type' => TaskUserType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->add('files', FileType::class, [
                'mapped' => false,
                'required' => false,
                'multiple' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'task' => null,
            'project_category_reference' => null,
            'data_class' => Task::class,
        ]);
    }
}
