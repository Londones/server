<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240130093551 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE indisponibilite_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE indisponibilite (id INT NOT NULL, employe_id INT DEFAULT NULL, creneau VARCHAR(255) DEFAULT NULL, jour VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8717036F1B65292 ON indisponibilite (employe_id)');
        $this->addSql('ALTER TABLE indisponibilite ADD CONSTRAINT FK_8717036F1B65292 FOREIGN KEY (employe_id) REFERENCES employe (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prestation_employe DROP CONSTRAINT fk_3b9220209e45c554');
        $this->addSql('ALTER TABLE prestation_employe DROP CONSTRAINT fk_3b9220201b65292');
        $this->addSql('DROP TABLE prestation_employe');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE indisponibilite_id_seq CASCADE');
        $this->addSql('CREATE TABLE prestation_employe (prestation_id INT NOT NULL, employe_id INT NOT NULL, PRIMARY KEY(prestation_id, employe_id))');
        $this->addSql('CREATE INDEX idx_3b9220201b65292 ON prestation_employe (employe_id)');
        $this->addSql('CREATE INDEX idx_3b9220209e45c554 ON prestation_employe (prestation_id)');
        $this->addSql('ALTER TABLE prestation_employe ADD CONSTRAINT fk_3b9220209e45c554 FOREIGN KEY (prestation_id) REFERENCES prestation (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prestation_employe ADD CONSTRAINT fk_3b9220201b65292 FOREIGN KEY (employe_id) REFERENCES employe (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE indisponibilite DROP CONSTRAINT FK_8717036F1B65292');
        $this->addSql('DROP TABLE indisponibilite');
    }
}
