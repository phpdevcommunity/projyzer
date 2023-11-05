<?php

namespace App\Controller\Admin;

use App\Entity\ProjectCategoryReferenceStatus;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProjectCategoryReferenceStatusCrudController extends AbstractCrudController
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
            ->addSelect('projectCategoryReference')
            ->addSelect('organizationUnit')
            ->innerJoin('entity.projectCategoryReference', 'projectCategoryReference')
            ->innerJoin('projectCategoryReference.organizationUnit', 'organizationUnit')
            ->andWhere('organizationUnit.id = :id)')
            ->setParameter('id', $user->getOrganizationUnit());
    }

    public static function getEntityFqcn(): string
    {
        return ProjectCategoryReferenceStatus::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('projectCategoryReference')->setColumns(6),
            AssociationField::new('taskStatusReference')->setColumns(6),
            IntegerField::new('order')->setColumns(12),
            BooleanField::new('isInitial')->setColumns(12),
            BooleanField::new('closesTask')->setColumns(12),
            DateTimeField::new('createdAt')->hideOnForm(),
            DateTimeField::new('updatedAt')->hideOnForm(),
        ];
    }
}
