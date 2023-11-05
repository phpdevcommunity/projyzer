<?php

namespace App\Controller\Admin;

use App\Entity\TaskCategoryReference;
use App\Entity\User;
use App\Repository\OrganizationUnitRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TaskCategoryReferenceCrudController extends AbstractCrudController
{
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $db = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        if ($this->isGranted('ROLE_ADMIN')) {
            return $db;
        }

        /**
         * @var User $user
         */
        $user = $this->getUser();

        return $db
            ->addSelect('organizationUnit')
            ->innerJoin('entity.organizationUnit', 'organizationUnit')
            ->andWhere('organizationUnit.id = :id)')
            ->setParameter('id', $user->getOrganizationUnit());
    }

    public static function getEntityFqcn(): string
    {
        return TaskCategoryReference::class;
    }

    public function configureFields(string $pageName): iterable
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

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
        yield TextareaField::new('description')->setColumns(12);
        yield DateTimeField::new('createdAt')->hideOnForm();
        yield DateTimeField::new('updatedAt')->hideOnForm();

    }
}
