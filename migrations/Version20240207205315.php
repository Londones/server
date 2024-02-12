<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240207205315 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE critere DROP CONSTRAINT fk_7f6a8053d249a887');
        $this->addSql('DROP INDEX idx_7f6a8053d249a887');
        $this->addSql('ALTER TABLE critere DROP feedback_id');
        $this->addSql('ALTER TABLE critere DROP commentaire');
        $this->addSql('ALTER TABLE critere DROP note');
        $this->addSql('ALTER TABLE feedback ADD critere_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE feedback DROP note_globale');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D22944589E5F45AB FOREIGN KEY (critere_id) REFERENCES critere (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D22944589E5F45AB ON feedback (critere_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE critere ADD feedback_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE critere ADD commentaire VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE critere ADD note DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE critere ADD CONSTRAINT fk_7f6a8053d249a887 FOREIGN KEY (feedback_id) REFERENCES feedback (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_7f6a8053d249a887 ON critere (feedback_id)');
        $this->addSql('ALTER TABLE feedback DROP CONSTRAINT FK_D22944589E5F45AB');
        $this->addSql('DROP INDEX IDX_D22944589E5F45AB');
        $this->addSql('ALTER TABLE feedback ADD note_globale INT NOT NULL');
        $this->addSql('ALTER TABLE feedback DROP critere_id');
    }
}
