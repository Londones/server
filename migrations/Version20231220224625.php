<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231220224625 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE critere_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE demande_prestataire_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE critere (id INT NOT NULL, feedback_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, commentaire VARCHAR(255) DEFAULT NULL, note DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7F6A8053D249A887 ON critere (feedback_id)');
        $this->addSql('CREATE TABLE demande_prestataire (id INT NOT NULL, prestataire_id INT DEFAULT NULL, statut BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C1853BF4BE3DB2B7 ON demande_prestataire (prestataire_id)');
        $this->addSql('ALTER TABLE critere ADD CONSTRAINT FK_7F6A8053D249A887 FOREIGN KEY (feedback_id) REFERENCES feedback (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE demande_prestataire ADD CONSTRAINT FK_C1853BF4BE3DB2B7 FOREIGN KEY (prestataire_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE etablissement ADD latitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE etablissement ADD longitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE etablissement ADD ville VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE etablissement ADD code_postal VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE feedback DROP notes');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE critere_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE demande_prestataire_id_seq CASCADE');
        $this->addSql('ALTER TABLE critere DROP CONSTRAINT FK_7F6A8053D249A887');
        $this->addSql('ALTER TABLE demande_prestataire DROP CONSTRAINT FK_C1853BF4BE3DB2B7');
        $this->addSql('DROP TABLE critere');
        $this->addSql('DROP TABLE demande_prestataire');
        $this->addSql('ALTER TABLE etablissement DROP latitude');
        $this->addSql('ALTER TABLE etablissement DROP longitude');
        $this->addSql('ALTER TABLE etablissement DROP ville');
        $this->addSql('ALTER TABLE etablissement DROP code_postal');
        $this->addSql('ALTER TABLE feedback ADD notes JSON NOT NULL');
    }
}
