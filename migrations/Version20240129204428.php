<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240129204428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE prestation_employe (prestation_id INT NOT NULL, employe_id INT NOT NULL, PRIMARY KEY(prestation_id, employe_id))');
        $this->addSql('CREATE INDEX IDX_3B9220209E45C554 ON prestation_employe (prestation_id)');
        $this->addSql('CREATE INDEX IDX_3B9220201B65292 ON prestation_employe (employe_id)');
        $this->addSql('ALTER TABLE prestation_employe ADD CONSTRAINT FK_3B9220209E45C554 FOREIGN KEY (prestation_id) REFERENCES prestation (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prestation_employe ADD CONSTRAINT FK_3B9220201B65292 FOREIGN KEY (employe_id) REFERENCES employe (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE prestation_employe DROP CONSTRAINT FK_3B9220209E45C554');
        $this->addSql('ALTER TABLE prestation_employe DROP CONSTRAINT FK_3B9220201B65292');
        $this->addSql('DROP TABLE prestation_employe');
    }
}
