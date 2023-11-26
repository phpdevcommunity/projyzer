<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\OrganizationUnitRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserCrudController extends AbstractCrudController
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
            ->addSelect('organizationUnit')
            ->innerJoin('entity.organizationUnit', 'organizationUnit')
            ->andWhere('organizationUnit.id = :id')
            ->setParameter('id', $user->getOrganizationUnit());
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $choices = [
            'ROLE_ADMIN' => 'ROLE_ADMIN'
        ];
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $choices['ROLE_SUPER_ADMIN'] = 'ROLE_SUPER_ADMIN';
        }

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
        yield TextField::new('username')->setColumns(6);
        yield EmailField::new('email')->setColumns(6);
        yield ChoiceField::new('roles')
            ->setChoices($choices)
            ->setRequired(false)
            ->renderExpanded()
            ->allowMultipleChoices()
            ->setColumns(12);
        yield BooleanField::new('active')
            ->hideWhenCreating()
            ->setColumns(12);
        yield BooleanField::new('sendActivationEmail')
            ->setLabel('Send activation information to the user')
            ->onlyOnForms()
            ->setColumns(12);
        yield DateTimeField::new('createdAt')->hideOnForm();
        yield DateTimeField::new('updatedAt')->hideOnForm();

    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN');
        return $actions;
    }
}
