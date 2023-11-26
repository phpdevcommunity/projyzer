<?php

namespace App\Controller\Admin;

use App\Entity\ProjectCategoryReference;
use App\Entity\TaskStatusReference;
use App\Entity\User;
use App\Repository\OrganizationRepository;
use App\Repository\OrganizationUnitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProjectCategoryReferenceCrudController extends AbstractCrudController
{

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $db = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            return $db;
        }

        /**
         * @var User $user
         */
        $user = $this->getUser();

        return $db
            ->addSelect('organizationUnit')
            ->innerJoin('entity.organizationUnit', 'organizationUnit')
            ->andWhere('organizationUnit.id = :id')
            ->setParameter('id', $user->getOrganizationUnit());
    }

    public static function getEntityFqcn(): string
    {
        return ProjectCategoryReference::class;
    }

    public function configureFields(string $pageName): iterable
    {

        /**
         * @var User $user
         */
        $user = $this->getUser();

        $statuses = $this->em->getRepository(TaskStatusReference::class)->findBy([]);

        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('organizationUnit')
            ->setRequired(true)
            ->setFormTypeOptions([
                'query_builder' => function (OrganizationUnitRepository $er) use ($user) {
                    $db = $er->createQueryBuilder('entity');
                    if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
                        $db->andWhere('entity.id = :id')->setParameter('id', $user->getOrganizationUnit());
                    }
                    return $db;
                },
            ])
            ->setColumns(12);
        yield TextField::new('label')->setColumns(12);
        yield AssociationField::new('projectCategoryReferenceStatuses')
            ->hideOnForm()
            ->setColumns(12);
        yield ChoiceField::new('statuses')
            ->onlyOnForms()
            ->setChoices($statuses)
            ->setFormTypeOptions([
                "empty_data" => [],
                'multiple' => 'true',
                'choice_label' => 'label',
            ])
            ->setColumns(12);
        yield TextareaField::new('description')->setColumns(12);
        yield DateTimeField::new('createdAt')->hideOnForm();
        yield DateTimeField::new('updatedAt')->hideOnForm();

    }
}
