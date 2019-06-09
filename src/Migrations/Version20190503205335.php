<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190503205335 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE dialog DROP FOREIGN KEY FK_4561D86269CCBE9A');
        $this->addSql('DROP INDEX IDX_4561D86269CCBE9A ON dialog');
        $this->addSql('ALTER TABLE dialog CHANGE author_id_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dialog ADD CONSTRAINT FK_4561D862F675F31B FOREIGN KEY (author_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE dialog ADD CONSTRAINT FK_4561D862584598A3 FOREIGN KEY (operator_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_4561D862F675F31B ON dialog (author_id)');
        $this->addSql('CREATE INDEX IDX_4561D862584598A3 ON dialog (operator_id)');
        $this->addSql('ALTER TABLE dialog_messages DROP FOREIGN KEY FK_20FB43F3251935C');
        $this->addSql('ALTER TABLE dialog_messages DROP FOREIGN KEY FK_20FB43F3DC2902E0');
        $this->addSql('ALTER TABLE dialog_messages DROP FOREIGN KEY FK_20FB43F3FC0E374F');
        $this->addSql('DROP INDEX IDX_20FB43F3251935C ON dialog_messages');
        $this->addSql('DROP INDEX IDX_20FB43F3FC0E374F ON dialog_messages');
        $this->addSql('DROP INDEX IDX_20FB43F3DC2902E0 ON dialog_messages');
        $this->addSql('ALTER TABLE dialog_messages ADD client_id INT DEFAULT NULL, ADD operator_id INT DEFAULT NULL, ADD dialog_id INT DEFAULT NULL, DROP client_id_id, DROP operator_id_id, DROP dialog_id_id');
        $this->addSql('ALTER TABLE dialog_messages ADD CONSTRAINT FK_20FB43F319EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE dialog_messages ADD CONSTRAINT FK_20FB43F3584598A3 FOREIGN KEY (operator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dialog_messages ADD CONSTRAINT FK_20FB43F35E46C4E2 FOREIGN KEY (dialog_id) REFERENCES dialog (id)');
        $this->addSql('CREATE INDEX IDX_20FB43F319EB6921 ON dialog_messages (client_id)');
        $this->addSql('CREATE INDEX IDX_20FB43F3584598A3 ON dialog_messages (operator_id)');
        $this->addSql('CREATE INDEX IDX_20FB43F35E46C4E2 ON dialog_messages (dialog_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE dialog DROP FOREIGN KEY FK_4561D862F675F31B');
        $this->addSql('ALTER TABLE dialog DROP FOREIGN KEY FK_4561D862584598A3');
        $this->addSql('DROP INDEX IDX_4561D862F675F31B ON dialog');
        $this->addSql('DROP INDEX IDX_4561D862584598A3 ON dialog');
        $this->addSql('ALTER TABLE dialog CHANGE author_id author_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dialog ADD CONSTRAINT FK_4561D86269CCBE9A FOREIGN KEY (author_id_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_4561D86269CCBE9A ON dialog (author_id_id)');
        $this->addSql('ALTER TABLE dialog_messages DROP FOREIGN KEY FK_20FB43F319EB6921');
        $this->addSql('ALTER TABLE dialog_messages DROP FOREIGN KEY FK_20FB43F3584598A3');
        $this->addSql('ALTER TABLE dialog_messages DROP FOREIGN KEY FK_20FB43F35E46C4E2');
        $this->addSql('DROP INDEX IDX_20FB43F319EB6921 ON dialog_messages');
        $this->addSql('DROP INDEX IDX_20FB43F3584598A3 ON dialog_messages');
        $this->addSql('DROP INDEX IDX_20FB43F35E46C4E2 ON dialog_messages');
        $this->addSql('ALTER TABLE dialog_messages ADD client_id_id INT DEFAULT NULL, ADD operator_id_id INT DEFAULT NULL, ADD dialog_id_id INT DEFAULT NULL, DROP client_id, DROP operator_id, DROP dialog_id');
        $this->addSql('ALTER TABLE dialog_messages ADD CONSTRAINT FK_20FB43F3251935C FOREIGN KEY (operator_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dialog_messages ADD CONSTRAINT FK_20FB43F3DC2902E0 FOREIGN KEY (client_id_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE dialog_messages ADD CONSTRAINT FK_20FB43F3FC0E374F FOREIGN KEY (dialog_id_id) REFERENCES dialog (id)');
        $this->addSql('CREATE INDEX IDX_20FB43F3251935C ON dialog_messages (operator_id_id)');
        $this->addSql('CREATE INDEX IDX_20FB43F3FC0E374F ON dialog_messages (dialog_id_id)');
        $this->addSql('CREATE INDEX IDX_20FB43F3DC2902E0 ON dialog_messages (client_id_id)');
    }
}
