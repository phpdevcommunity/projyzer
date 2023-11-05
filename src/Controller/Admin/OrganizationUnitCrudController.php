<?php

namespace App\Controller\Admin;

use App\Entity\OrganizationUnit;
use App\Entity\User;
use App\Repository\OrganizationRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OrganizationUnitCrudController extends AbstractCrudController
{

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
            ->andWhere('entity.id = :id')
            ->setParameter('id', $user->getOrganizationUnit());
    }

    public static function getEntityFqcn(): string
    {
        return OrganizationUnit::class;
    }

    public function configureFields(string $pageName): iterable
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('organization')
            ->setRequired(true)
            ->setFormTypeOptions([
                'query_builder' => function (OrganizationRepository $er) use ($user) {
                    $db = $er->createQueryBuilder('entity');
                    if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
                        $db->where('entity.id = :id')
                            ->setParameter('id', $user->getOrganizationUnit()->getOrganization());
                    }
                    return $db;
                },
            ])
            ->setColumns(12);
        yield TextField::new('name')->setColumns(12);
        yield TextField::new('identifier')->setColumns(6);
        yield TextField::new('code')->setColumns(6);
        yield DateTimeField::new('createdAt')->hideOnForm();

    }

    public function configureActions(Actions $actions): Actions
    {
        $actions
            ->setPermission(Action::NEW, 'ROLE_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_ADMIN');

        return $actions;
    }
}
