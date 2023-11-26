<?php

namespace App\DataFixtures;

use App\Entity\Organization;
use App\Entity\OrganizationUnit;
use App\Entity\Project;
use App\Entity\ProjectCategoryReference;
use App\Entity\ProjectCategoryReferenceStatus;
use App\Entity\Setting;
use App\Entity\Task;
use App\Entity\TaskCategoryReference;
use App\Entity\TaskComment;
use App\Entity\TaskStatus;
use App\Entity\TaskStatusReference;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        $setting = (new Setting())
            ->setKey(Setting::KEY_INSTALLATION_REQUIRED)
            ->setValue(false);

        $manager->persist($setting);
        $manager->flush();

        $identifier = $faker->unique()->numerify('##############');

        $org = (new Organization())
            ->setName('DEVCODER')
            ->setIdentifier($identifier);

        $manager->persist($org);
        $manager->flush();

        $orgUnit = $manager->getRepository(OrganizationUnit::class)->findOneBy(['organization' => $org]);

        for ($i = 0; $i < 20; $i++) {
            $user = (new User())
                ->setUsername($faker->unique()->userName)
                ->setPassword('$2y$13$rLgSpVp2kKLIuDJ1w3HjJulUKKrXaYd/NA/RQlEjZlWCcIm9uUN.m')
                ->setActive(true)
                ->setRoles(['ROLE_USER'])
                ->setOrganizationUnit($orgUnit)
                ->setEmail($faker->unique()->email);
            $manager->persist($user);
        }

        $user = (new User())
            ->setUsername($faker->unique()->userName)
            ->setPassword('$2y$13$rLgSpVp2kKLIuDJ1w3HjJulUKKrXaYd/NA/RQlEjZlWCcIm9uUN.m')
            ->setActive(true)
            ->setRoles(['ROLE_ADMIN'])
            ->setOrganizationUnit($orgUnit)
            ->setEmail($faker->unique()->email);
        $manager->persist($user);

        $manager->flush();

        $user = (new User())
            ->setUsername('devcoder.xyz')
            ->setPassword('$2y$13$rLgSpVp2kKLIuDJ1w3HjJulUKKrXaYd/NA/RQlEjZlWCcIm9uUN.m')
            ->setActive(true)
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setOrganizationUnit($orgUnit)
            ->setEmail('dev@devcoder.xyz');

        $manager->persist($user);

        $manager->flush();

        $statuses = [];
        foreach ($this->getTaskStatuses() as $label => $description) {
            $status = (new TaskStatusReference())
                ->setOrganizationUnit($orgUnit)
                ->setLabel($label)
                ->setDescription($description);

            $manager->persist($status);
            $statuses[] = $status;
        }
        $manager->flush();


        $categories = [];
        foreach ($this->getProjectCategories() as $label => $description) {
            $category = (new ProjectCategoryReference())
                ->setOrganizationUnit($orgUnit)
                ->setLabel($label)
                ->setDescription($description);

            foreach ($statuses as $status) {
                $category->addProjectCategoryReferenceStatus(
                    (new ProjectCategoryReferenceStatus())
                    ->setTaskStatusReference($status)
                    ->setIsInitial($status->getLabel() == 'NOUVEAU')
                    ->setClosesTask($status->getLabel() == 'FERMÉE')
                );
            }
            $manager->persist($category);
            $categories[] = $category;
        }
        $manager->flush();

        $taskCategories = [];
        foreach ($this->getTaskCategories() as $label => $description) {
            $category = (new TaskCategoryReference())
                ->setOrganizationUnit($orgUnit)
                ->setLabel($label)
                ->setDescription($description);

            $manager->persist($category);
            $taskCategories[] = $category;
        }
        $manager->flush();

        for ($i = 0; $i < 25; $i++) {
            $project = (new Project())
                ->setProjectCategoryReference($faker->randomElement($categories))
                ->setName($faker->sentence())
                ->setOrganizationUnit($orgUnit)
                ->setCreatedBy($user)
                ->setDescription($faker->text(200))
                ->setActive(true);
            $manager->persist($project);

            for ($x = 0; $x < 15; $x++) {
                $task = (new Task())
                    ->setTaskCategoryReference($faker->randomElement($taskCategories))
                    ->setTitle($faker->sentence())
                    ->setDescription($faker->text(200))
                    ->addStatus(
                        (new TaskStatus())
                            ->setLabel($statuses[0]->getLabel())
                    )
                    ->setPriority($faker->randomElement([Task::PRIORITY_LOW, Task::PRIORITY_MEDIUM, Task::PRIORITY_HIGH]))
                    ->setProject($project)
                    ->setCreatedBy($user)
                    ->setAssignedTo($user)
                    ->setEstimatedTime($faker->numberBetween(2, 20))
                    ->setActualTime($faker->numberBetween(0, 20));
                $manager->persist($task);

                for ($c = 0; $c < 2; $c++) {
                    $comment = (new TaskComment())
                        ->setUser($user)
                        ->setTask($task)
                        ->setContent($faker->text(200));
                    $manager->persist($comment);
                }

            }
        }
        $manager->flush();

    }

    public function getProjectCategories(): array
    {
        return [
            'Développement' => 'Pour des projets de développement de logiciels, applications ou solutions informatiques.',
            'Administration Système' => "Pour des projets liés à la gestion et à l'administration des systèmes informatiques et des serveurs.",
            'Support Technique' => "Pour des projets de soutien technique et d'assistance aux utilisateurs ou clients dans le domaine informatique.",
            'Infrastructure' => "Pour des projets liés à la mise en place et à la gestion de l'infrastructure informatique.",
            'Sécurité' => 'Pour des projets de renforcement de la sécurité des systèmes informatiques et des données.',
            'Analyse' => "Pour des projets d'analyse de données, de marché ou de processus.",
            'Migration' => 'Pour des projets de transfert de données ou de systèmes.',
            'Nouveaux Produits' => 'Pour organiser des projets liés au développement de nouveaux produits ou services.',
            'Optimisation des Processus' => 'Pour des projets visant à améliorer l\'efficacité opérationnelle.',
            'Formation' => 'Pour des projets de formation et de développement des employés.',
            'Projets Internes' => 'Pour les projets internes à l\'entreprise qui ne sont pas liés à des clients ou à des produits.',
        ];
    }

    public function getTaskCategories(): array
    {
        return [
            'Développement de Fonctionnalités' => "Pour des tâches liées au développement de nouvelles fonctionnalités ou d'améliorations existantes.",
            'Résolution de Problèmes' => "Pour des tâches liées à la résolution de problèmes techniques ou d'erreurs.",
            'Maintenance' => 'Pour des tâches de maintenance, telles que mises à jour ou correctifs.',
            'Tests et Qualité' => 'Pour des tâches liées aux tests de qualité, vérifications et validation.',
            'Formation des Utilisateurs' => "Pour des tâches liées à la formation des utilisateurs sur l'utilisation du logiciel ou des produits.",
            'Planification de Projets' => 'Pour des tâches liées à la planification et à la gestion de projets.',
            'Documentation' => 'Pour des tâches liées à la création ou à la mise à jour de la documentation.',
            'Gestion des Ressources' => 'Pour des tâches liées à la gestion des ressources, comme la gestion des effectifs.',
            'Communication' => 'Pour des tâches liées à la communication interne ou externe.',
            'Recherche et Analyse' => "Pour des tâches de recherche, d'analyse de données ou d'études de marché.",
            'Gestion des Risques' => 'Pour des tâches liées à la gestion des risques et à la sécurité.',
            'Évaluation des Performances' => "Pour des tâches d'évaluation des performances ou des indicateurs de performance.",
            'Événements et Réunions' => "Pour des tâches liées à l'organisation d'événements ou de réunions.",
            'Suivi de Projet' => 'Pour des tâches de suivi de projet et de gestion de projet.',
            'Reporting' => "Pour des tâches de création de rapports ou d'analyses.",
            'Amélioration Continue' => "Pour des tâches liées à l'amélioration continue des processus ou des produits.",
            'Conception Graphique' => "Pour des tâches liées à la conception graphique ou à l'interface utilisateur.",
            'Intégration et Migration' => "Pour des tâches liées à l'intégration de systèmes ou à la migration de données.",
            'Infrastructure et Matériel' => "Pour des tâches liées à l'infrastructure informatique et au matériel.",
            'Sécurité et Confidentialité' => 'Pour des tâches liées à la sécurité des systèmes et à la confidentialité des données.',
        ];
    }

    public function getTaskStatuses(): array
    {
        return [
            'Nouveau' => "La tâche ou le projet est nouvellement créé ou ajouté au système, mais n'a pas encore été attribué ou planifié pour être réalisé.",
            'À Faire' => 'La tâche ou le projet est à planifier et à réaliser.',
            'Terminée - Production' => 'La phase de production de la tâche ou du projet est terminée.',
            'En Cours - Développement' => 'La tâche ou le projet est actuellement en phase de développement.',
            'À Faire - Développement' => 'La tâche ou le projet est à planifier et à réaliser dans la phase de développement.',
            'Terminée - Développement' => 'La phase de développement de la tâche ou du projet est terminée.',
            'En Cours - Recette' => 'La tâche ou le projet est en cours de recette ou de test.',
            'À Faire - Recette' => 'La tâche ou le projet est à planifier et à réaliser dans la phase de recette.',
            'Terminée - Recette' => 'La phase de recette de la tâche ou du projet est terminée.',
            'En Cours - Pré-Production' => 'La tâche ou le projet est en cours de préparation pour la production.',
            'À Faire - Pré-Production' => 'La tâche ou le projet est à planifier et à réaliser dans la phase de pré-production.',
            'Terminée - Pré-Production' => 'La phase de pré-production de la tâche ou du projet est terminée.',
            'En Cours - Production' => 'La tâche ou le projet est en cours de production ou d\'exécution.',
            'À Faire - Production' => 'La tâche ou le projet est à planifier et à réaliser dans la phase de production.',
            'Abandonnée' => 'La tâche ou le projet a été abandonnée ou annulée.',
            'Reportée' => 'La tâche ou le projet a été reportée à une date ultérieure.',
            'Bloquée' => 'La tâche ou le projet est bloquée en raison de problèmes ou de contraintes.',
            'Validée' => 'La tâche ou le projet a été validée et approuvée.',
            'Fermée' => 'La tâche ou le projet est clôturée et considérée comme terminée.'
        ];
    }
}
