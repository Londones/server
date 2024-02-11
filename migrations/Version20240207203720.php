<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240207203720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_critere (category_id INT NOT NULL, critere_id INT NOT NULL, PRIMARY KEY(category_id, critere_id))');
        $this->addSql('CREATE INDEX IDX_B8B2C02D12469DE2 ON category_critere (category_id)');
        $this->addSql('CREATE INDEX IDX_B8B2C02D9E5F45AB ON category_critere (critere_id)');
        $this->addSql('ALTER TABLE category_critere ADD CONSTRAINT FK_B8B2C02D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_critere ADD CONSTRAINT FK_B8B2C02D9E5F45AB FOREIGN KEY (critere_id) REFERENCES critere (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category DROP criteres');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE category_critere DROP CONSTRAINT FK_B8B2C02D12469DE2');
        $this->addSql('ALTER TABLE category_critere DROP CONSTRAINT FK_B8B2C02D9E5F45AB');
        $this->addSql('DROP TABLE category_critere');
        $this->addSql('ALTER TABLE category ADD criteres TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN category.criteres IS \'(DC2Type:simple_array)\'');
    }
}
