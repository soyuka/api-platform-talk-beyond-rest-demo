<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190226140042 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__moment AS SELECT id, description, media FROM moment');
        $this->addSql('DROP TABLE moment');
        $this->addSql('CREATE TABLE moment (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , description CLOB DEFAULT NULL COLLATE BINARY, media VARCHAR(255) NOT NULL COLLATE BINARY, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO moment (id, description, media) SELECT id, description, media FROM __temp__moment');
        $this->addSql('DROP TABLE __temp__moment');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__moment AS SELECT id, description, media FROM moment');
        $this->addSql('DROP TABLE moment');
        $this->addSql('CREATE TABLE moment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, description CLOB DEFAULT NULL, media VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO moment (id, description, media) SELECT id, description, media FROM __temp__moment');
        $this->addSql('DROP TABLE __temp__moment');
    }
}
