<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231206092535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE demande_prestataire_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE demande_prestataire (id INT NOT NULL, prestataire_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C1853BF4BE3DB2B7 ON demande_prestataire (prestataire_id)');
        $this->addSql('ALTER TABLE demande_prestataire ADD CONSTRAINT FK_C1853BF4BE3DB2B7 FOREIGN KEY (prestataire_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE demande_prestataire_id_seq CASCADE');
        $this->addSql('ALTER TABLE demande_prestataire DROP CONSTRAINT FK_C1853BF4BE3DB2B7');
        $this->addSql('DROP TABLE demande_prestataire');
    }
}
