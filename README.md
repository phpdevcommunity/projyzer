# ProJyser
<div style="text-align:center;">
  <img src="./public/images/logo.png" alt="ProJyser Logo">
</div>

ProJyser is a project management software designed for simplicity and efficiency, eliminating the need for complex tools with unused features.

## 📖 Documentation

- [English](#english)
- [Français](#français)

## English

Projyser is a project management application developed using Symfony 6 and PHP 8.2. Designed to meet the needs of businesses, Projyser provides a simple and effective solution for managing Internet and client projects.

## Key Features

### Project Management
- Create and manage Internet or client projects.
- Define project categories for maximum customization.
- Add stakeholders to each project with specific rights.

### User Management
- Associate users with companies for simplified management.
- Assign members to projects and define their roles.
- Configure specific rights for each team member.

### Task/Ticket Management
- Categorize tasks/tickets according to your needs.
- Customize statuses for each project category.
- Assign tasks to team members or clients.

### Collaboration and Comments
- Enable real-time collaboration on tasks and projects.
- Add comments for each task, facilitating communication.

### Custom Configuration
- Configure project and task categories according to your specifications.
- Customize access rights for each user and project.

## System Requirements
- PHP 8.2
- Symfony 6
- MariaDB 10
- Composer
- Yarn
- Node.js 18

## Installation

1. Clone the repository:
```bash
git clone https://github.com/devcoder-xyz/projyzer.git
```

2. Install dependencies with Composer:
```bash
composer install
```

3. Install JavaScript dependencies with Yarn and compile assets:
```bash
yarn install
yarn encore prod
```
4. Copy the `.env` file to `.env.local` and modify the necessary parameters:
   - Update the `DATABASE_URL` line with your MariaDB parameters.
   - Update the `MAILER_DSN` line with the default SMTP server.
   - Update the `MAILER_FROM` line with the default email address.
   - Update the `APP_DEFAULT_URI` line with the default URL of your application.

## Usage with Docker

Pojyser can be used with Docker for easy deployment. Follow these steps to use Docker with a preconfigured environment:

1. Build and launch Docker containers:
```bash
docker compose up -d
```

2. If you modified the `.env.local` file, use the following command to apply the changes:
```bash
docker compose --env-file .env.local up -d
```

3. Access the application in your browser at [http://localhost:8045](http://localhost:8045).

**Note:** The default port is 8045, but you can modify it in the `.env.local` file by adjusting the `DOCKER_PORT_NGINX` variable. Make sure to use the appropriate Docker command to apply these changes:
```bash
docker compose --env-file .env.local up -d
```

## License
ProJyser is an open-source software under the GNU General Public License v3.0.

## Français

Projyser est une application de gestion de projet développée en utilisant Symfony 6 et PHP 8.2. Conçu pour répondre aux besoins des entreprises, Projyser offre une solution simple et efficace pour la gestion de projets Internet et clients.

## Fonctionnalités Principales

### Gestion des Projets
- Créez et gérez des projets Internet ou clients.
- Définissez des catégories de projets pour une personnalisation maximale.
- Ajoutez des intervenants à chaque projet avec des droits spécifiques.

### Gestion des Utilisateurs
- Associez des utilisateurs à des sociétés pour une gestion simplifiée.
- Affectez des membres à des projets et définissez leurs rôles.
- Configurez des droits spécifiques pour chaque membre de l'équipe.

### Gestion des Tâches/Tickets
- Catégorisez les tâches/tickets selon vos besoins.
- Personnalisez les statuts pour chaque catégorie de projet.
- Assignez des tâches à des membres de l'équipe ou à des clients.

### Collaborations et Commentaires
- Permettez la collaboration en temps réel sur les tâches et les projets.
- Ajoutez des commentaires pour chaque tâche, favorisant la communication.

### Configuration Personnalisée
- Configurez les catégories de projets et de tâches selon vos spécifications.
- Personnalisez les droits d'accès pour chaque utilisateur et projet.

## Configuration Requise
- PHP 8.2
- Symfony 6
- MariaDB 10
- Composer
- Yarn
- Node.js 18

## Installation

1. Clonez le dépôt :
```bash
git clone https://github.com/devcoder-xyz/projyzer.git
```
2. Installez les dépendances avec Composer :
```bash
composer install
```
3. Installez les dépendances JavaScript avec Yarn et compilez les assets :
```bash
yarn install
yarn encore prod
```
4. Copiez le fichier `.env` en `.env.local` et modifiez les paramètres nécessaires :
    - Modifiez la ligne `DATABASE_URL` avec vos paramètres MariaDB.
    - Modifiez la ligne `MAILER_DSN` serveur smtp par défaut.
    - Modifiez la ligne `MAILER_FROM` avec l'adresse e-mail par défaut.
    - Modifiez la ligne `APP_DEFAULT_URI` avec l'URL par défaut de votre application.

## Utilisation avec Docker

Pojyser peut être utilisé avec Docker pour un déploiement facile. Suivez ces étapes pour utiliser Docker avec un environnement préconfiguré :

1. Construisez et lancez les conteneurs Docker :
```bash
docker compose up -d
```

2. Si vous avez modifié le fichier `.env.local`, utilisez la commande suivante pour prendre en compte les modifications :
```bash
docker compose --env-file .env.local up -d
```

3. Accédez à l'application dans votre navigateur à l'adresse [http://localhost:8045](http://localhost:8045).

**Note :** Le port par défaut est le 8045, mais vous pouvez le modifier dans le fichier `.env.local` en ajustant la variable `DOCKER_PORT_NGINX`. Assurez-vous d'utiliser la commande Docker appropriée pour prendre en compte ces modifications :
```bash
docker compose --env-file .env.local up -d
```
## Licence
ProJyser est un logiciel open source sous licence GNU General Public License v3.0.
