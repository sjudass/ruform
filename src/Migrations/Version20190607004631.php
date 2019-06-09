<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190607004631 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE dialog DROP FOREIGN KEY FK_4561D862F675F31B');
        $this->addSql('ALTER TABLE dialog ADD CONSTRAINT FK_4561D862F675F31B FOREIGN KEY (author_id) REFERENCES client (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE dialog DROP FOREIGN KEY FK_4561D862F675F31B');
        $this->addSql('ALTER TABLE dialog ADD CONSTRAINT FK_4561D862F675F31B FOREIGN KEY (author_id) REFERENCES client (id)');
    }
}
