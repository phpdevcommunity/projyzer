<?php

namespace App\Form;

use App\Entity\OrganizationUnit;
use App\Entity\Project;
use App\Entity\ProjectCategoryReference;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**
         * @var OrganizationUnit $organizationUnit
         */
        $organizationUnit = $options['organization_unit'];
        $builder
            ->add('projectCategoryReference', EntityType::class, [
                'label' => 'Category',
                'required' => true,
                'class' => ProjectCategoryReference::class,
                'query_builder' => function (EntityRepository $er) use ($organizationUnit): QueryBuilder {
                    return $er->createQueryBuilder('p')
                        ->innerJoin('p.organizationUnit', 'organizationUnit')
                        ->where('organizationUnit = :organizationUnit')
                        ->setParameter('organizationUnit', $organizationUnit)
                        ->orderBy('p.id', 'ASC');
                },
                'choice_label' => 'label',
            ])
            ->add('name', TextType::class, [
                'label' => 'name'
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'description',
                "attr" => [
                    'rows' => 8
                ]
            ])
            ->add('files', FileType::class, [
                'mapped' => false,
                'required' => false,
                'multiple' => true,
            ])
            ->add('projectUsersWithoutOwner', CollectionType::class, [
                'required' => false,
                'label' => false,
                'entry_type' => ProjectUserType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'organization_unit' => null,
            'data_class' => Project::class,
        ]);
    }
}
