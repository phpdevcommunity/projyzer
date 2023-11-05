<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231105153026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, uid VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, size INT DEFAULT NULL, mime_type VARCHAR(100) NOT NULL, content LONGBLOB NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organization (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, identifier VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_C1EE637C772E836A (identifier), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organization_unit (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(10) NOT NULL, identifier VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_E5B232CE772E836A (identifier), INDEX IDX_E5B232CE32C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, organization_unit_id INT NOT NULL, project_category_reference_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, active TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_2FB3D0EE989D9B62 (slug), INDEX IDX_2FB3D0EEA76ED395 (user_id), INDEX IDX_2FB3D0EE356FF84E (organization_unit_id), INDEX IDX_2FB3D0EE8ADDA071 (project_category_reference_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_category_reference (id INT AUTO_INCREMENT NOT NULL, organization_unit_id INT NOT NULL, label VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_DF916542356FF84E (organization_unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_category_reference_status (id INT AUTO_INCREMENT NOT NULL, project_category_reference_id INT DEFAULT NULL, task_status_reference_id INT NOT NULL, is_initial TINYINT(1) DEFAULT 0 NOT NULL, closes_task TINYINT(1) DEFAULT 0 NOT NULL, `order` INT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_9BCFDF498ADDA071 (project_category_reference_id), INDEX IDX_9BCFDF49B1331163 (task_status_reference_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_file (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, file_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B50EFE08166D1F9C (project_id), UNIQUE INDEX UNIQ_B50EFE0893CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_user (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, user_id INT NOT NULL, permissions JSON NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B4021E51166D1F9C (project_id), INDEX IDX_B4021E51A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, updated_by_id INT DEFAULT NULL, assigned_to_id INT DEFAULT NULL, project_id INT NOT NULL, task_category_reference_id INT DEFAULT NULL, last_status_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, priority VARCHAR(255) NOT NULL, estimated_time NUMERIC(5, 2) DEFAULT NULL, actual_time NUMERIC(5, 2) DEFAULT NULL, start_date DATETIME DEFAULT NULL, due_date DATETIME DEFAULT NULL, percentage_completed INT DEFAULT 0 NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_527EDB25A76ED395 (user_id), INDEX IDX_527EDB25896DBBDE (updated_by_id), INDEX IDX_527EDB25F4BD7827 (assigned_to_id), INDEX IDX_527EDB25166D1F9C (project_id), INDEX IDX_527EDB2576141532 (task_category_reference_id), UNIQUE INDEX UNIQ_527EDB257C38DFBB (last_status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_category_reference (id INT AUTO_INCREMENT NOT NULL, organization_unit_id INT NOT NULL, label VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_7CF04536356FF84E (organization_unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_comment (id INT AUTO_INCREMENT NOT NULL, task_id INT NOT NULL, user_id INT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_8B9578868DB60186 (task_id), INDEX IDX_8B957886A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_comment_file (id INT AUTO_INCREMENT NOT NULL, task_comment_id INT NOT NULL, file_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6238B0D1E47A36BC (task_comment_id), UNIQUE INDEX UNIQ_6238B0D193CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_file (id INT AUTO_INCREMENT NOT NULL, task_id INT NOT NULL, file_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_FF2CA26B8DB60186 (task_id), UNIQUE INDEX UNIQ_FF2CA26B93CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_status (id INT AUTO_INCREMENT NOT NULL, task_id INT NOT NULL, user_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_40A9E1CF8DB60186 (task_id), INDEX IDX_40A9E1CFA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_status_reference (id INT AUTO_INCREMENT NOT NULL, organization_unit_id INT NOT NULL, label VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B6CFA154356FF84E (organization_unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_user (id INT AUTO_INCREMENT NOT NULL, task_id INT NOT NULL, user_id INT NOT NULL, permissions JSON NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_FE2042328DB60186 (task_id), INDEX IDX_FE204232A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, organization_unit_id INT NOT NULL, username VARCHAR(180) NOT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) DEFAULT NULL, hash VARCHAR(255) DEFAULT NULL, active TINYINT(1) DEFAULT 0 NOT NULL, send_activation_email TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649D1B862B8 (hash), INDEX IDX_8D93D649356FF84E (organization_unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE organization_unit ADD CONSTRAINT FK_E5B232CE32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE356FF84E FOREIGN KEY (organization_unit_id) REFERENCES organization_unit (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE8ADDA071 FOREIGN KEY (project_category_reference_id) REFERENCES project_category_reference (id)');
        $this->addSql('ALTER TABLE project_category_reference ADD CONSTRAINT FK_DF916542356FF84E FOREIGN KEY (organization_unit_id) REFERENCES organization_unit (id)');
        $this->addSql('ALTER TABLE project_category_reference_status ADD CONSTRAINT FK_9BCFDF498ADDA071 FOREIGN KEY (project_category_reference_id) REFERENCES project_category_reference (id)');
        $this->addSql('ALTER TABLE project_category_reference_status ADD CONSTRAINT FK_9BCFDF49B1331163 FOREIGN KEY (task_status_reference_id) REFERENCES task_status_reference (id)');
        $this->addSql('ALTER TABLE project_file ADD CONSTRAINT FK_B50EFE08166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE project_file ADD CONSTRAINT FK_B50EFE0893CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2576141532 FOREIGN KEY (task_category_reference_id) REFERENCES task_category_reference (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB257C38DFBB FOREIGN KEY (last_status_id) REFERENCES task_status (id)');
        $this->addSql('ALTER TABLE task_category_reference ADD CONSTRAINT FK_7CF04536356FF84E FOREIGN KEY (organization_unit_id) REFERENCES organization_unit (id)');
        $this->addSql('ALTER TABLE task_comment ADD CONSTRAINT FK_8B9578868DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE task_comment ADD CONSTRAINT FK_8B957886A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE task_comment_file ADD CONSTRAINT FK_6238B0D1E47A36BC FOREIGN KEY (task_comment_id) REFERENCES task_comment (id)');
        $this->addSql('ALTER TABLE task_comment_file ADD CONSTRAINT FK_6238B0D193CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_file ADD CONSTRAINT FK_FF2CA26B8DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE task_file ADD CONSTRAINT FK_FF2CA26B93CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_status ADD CONSTRAINT FK_40A9E1CF8DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE task_status ADD CONSTRAINT FK_40A9E1CFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE task_status_reference ADD CONSTRAINT FK_B6CFA154356FF84E FOREIGN KEY (organization_unit_id) REFERENCES organization_unit (id)');
        $this->addSql('ALTER TABLE task_user ADD CONSTRAINT FK_FE2042328DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE task_user ADD CONSTRAINT FK_FE204232A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649356FF84E FOREIGN KEY (organization_unit_id) REFERENCES organization_unit (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE organization_unit DROP FOREIGN KEY FK_E5B232CE32C8A3DE');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEA76ED395');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE356FF84E');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE8ADDA071');
        $this->addSql('ALTER TABLE project_category_reference DROP FOREIGN KEY FK_DF916542356FF84E');
        $this->addSql('ALTER TABLE project_category_reference_status DROP FOREIGN KEY FK_9BCFDF498ADDA071');
        $this->addSql('ALTER TABLE project_category_reference_status DROP FOREIGN KEY FK_9BCFDF49B1331163');
        $this->addSql('ALTER TABLE project_file DROP FOREIGN KEY FK_B50EFE08166D1F9C');
        $this->addSql('ALTER TABLE project_file DROP FOREIGN KEY FK_B50EFE0893CB796C');
        $this->addSql('ALTER TABLE project_user DROP FOREIGN KEY FK_B4021E51166D1F9C');
        $this->addSql('ALTER TABLE project_user DROP FOREIGN KEY FK_B4021E51A76ED395');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25A76ED395');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25896DBBDE');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25F4BD7827');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25166D1F9C');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB2576141532');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB257C38DFBB');
        $this->addSql('ALTER TABLE task_category_reference DROP FOREIGN KEY FK_7CF04536356FF84E');
        $this->addSql('ALTER TABLE task_comment DROP FOREIGN KEY FK_8B9578868DB60186');
        $this->addSql('ALTER TABLE task_comment DROP FOREIGN KEY FK_8B957886A76ED395');
        $this->addSql('ALTER TABLE task_comment_file DROP FOREIGN KEY FK_6238B0D1E47A36BC');
        $this->addSql('ALTER TABLE task_comment_file DROP FOREIGN KEY FK_6238B0D193CB796C');
        $this->addSql('ALTER TABLE task_file DROP FOREIGN KEY FK_FF2CA26B8DB60186');
        $this->addSql('ALTER TABLE task_file DROP FOREIGN KEY FK_FF2CA26B93CB796C');
        $this->addSql('ALTER TABLE task_status DROP FOREIGN KEY FK_40A9E1CF8DB60186');
        $this->addSql('ALTER TABLE task_status DROP FOREIGN KEY FK_40A9E1CFA76ED395');
        $this->addSql('ALTER TABLE task_status_reference DROP FOREIGN KEY FK_B6CFA154356FF84E');
        $this->addSql('ALTER TABLE task_user DROP FOREIGN KEY FK_FE2042328DB60186');
        $this->addSql('ALTER TABLE task_user DROP FOREIGN KEY FK_FE204232A76ED395');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649356FF84E');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE organization_unit');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_category_reference');
        $this->addSql('DROP TABLE project_category_reference_status');
        $this->addSql('DROP TABLE project_file');
        $this->addSql('DROP TABLE project_user');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE task_category_reference');
        $this->addSql('DROP TABLE task_comment');
        $this->addSql('DROP TABLE task_comment_file');
        $this->addSql('DROP TABLE task_file');
        $this->addSql('DROP TABLE task_status');
        $this->addSql('DROP TABLE task_status_reference');
        $this->addSql('DROP TABLE task_user');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
