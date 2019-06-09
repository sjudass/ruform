<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190609111355 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE applications (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, client_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, date_create DATETIME NOT NULL, status VARCHAR(50) NOT NULL, date_process DATETIME DEFAULT NULL, INDEX IDX_F7C966F0A76ED395 (user_id), INDEX IDX_F7C966F019EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, phone VARCHAR(30) NOT NULL, lastname VARCHAR(50) NOT NULL, firstname VARCHAR(50) NOT NULL, middlename VARCHAR(50) DEFAULT NULL, date_application DATETIME NOT NULL, UNIQUE INDEX UNIQ_C7440455E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dialog (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, operator_id INT DEFAULT NULL, time_create DATETIME NOT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_4561D862F675F31B (author_id), INDEX IDX_4561D862584598A3 (operator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dialog_messages (id INT AUTO_INCREMENT NOT NULL, dialog_id INT DEFAULT NULL, is_operator TINYINT(1) NOT NULL, message_text LONGTEXT NOT NULL, author_ip VARCHAR(30) NOT NULL, is_read TINYINT(1) NOT NULL, message_time DATETIME NOT NULL, INDEX IDX_20FB43F35E46C4E2 (dialog_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F7C966F0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F7C966F019EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dialog ADD CONSTRAINT FK_4561D862F675F31B FOREIGN KEY (author_id) REFERENCES client (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE dialog ADD CONSTRAINT FK_4561D862584598A3 FOREIGN KEY (operator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dialog_messages ADD CONSTRAINT FK_20FB43F35E46C4E2 FOREIGN KEY (dialog_id) REFERENCES dialog (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_F7C966F019EB6921');
        $this->addSql('ALTER TABLE dialog DROP FOREIGN KEY FK_4561D862F675F31B');
        $this->addSql('ALTER TABLE dialog_messages DROP FOREIGN KEY FK_20FB43F35E46C4E2');
        $this->addSql('DROP TABLE applications');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE dialog');
        $this->addSql('DROP TABLE dialog_messages');
    }
}
