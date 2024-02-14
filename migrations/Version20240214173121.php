<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240214173121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etablissement ADD jours_ouverture VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE etablissement ADD horraires_ouverture VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE etablissement ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE etablissement ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE etablissement DROP horaires_ouverture');
        $this->addSql('ALTER TABLE etablissement ALTER latitude TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE etablissement ALTER longitude TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE etablissement RENAME COLUMN kbis_name TO kbis');
        $this->addSql('ALTER TABLE "user" DROP created_at');
        $this->addSql('ALTER TABLE "user" DROP updated_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE etablissement ADD horaires_ouverture VARCHAR(1000) NOT NULL');
        $this->addSql('ALTER TABLE etablissement ADD kbis_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE etablissement DROP kbis');
        $this->addSql('ALTER TABLE etablissement DROP jours_ouverture');
        $this->addSql('ALTER TABLE etablissement DROP horraires_ouverture');
        $this->addSql('ALTER TABLE etablissement DROP created_at');
        $this->addSql('ALTER TABLE etablissement DROP updated_at');
        $this->addSql('ALTER TABLE etablissement ALTER latitude TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE etablissement ALTER longitude TYPE VARCHAR(255)');
    }
}
