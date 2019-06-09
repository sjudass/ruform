<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190503210415 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE dialog_messages DROP FOREIGN KEY FK_20FB43F319EB6921');
        $this->addSql('ALTER TABLE dialog_messages DROP FOREIGN KEY FK_20FB43F3584598A3');
        $this->addSql('DROP INDEX IDX_20FB43F3584598A3 ON dialog_messages');
        $this->addSql('DROP INDEX IDX_20FB43F319EB6921 ON dialog_messages');
        $this->addSql('ALTER TABLE dialog_messages DROP client_id, DROP operator_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE dialog_messages ADD client_id INT DEFAULT NULL, ADD operator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dialog_messages ADD CONSTRAINT FK_20FB43F319EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE dialog_messages ADD CONSTRAINT FK_20FB43F3584598A3 FOREIGN KEY (operator_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_20FB43F3584598A3 ON dialog_messages (operator_id)');
        $this->addSql('CREATE INDEX IDX_20FB43F319EB6921 ON dialog_messages (client_id)');
    }
}
