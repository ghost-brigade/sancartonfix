<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230118135158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE housing ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE housing ALTER category_id SET NOT NULL');
        $this->addSql('ALTER TABLE housing ALTER description TYPE TEXT');
        $this->addSql('ALTER TABLE housing ALTER description DROP NOT NULL');
        $this->addSql('ALTER TABLE housing ALTER latitude TYPE NUMERIC(10, 8)');
        $this->addSql('ALTER TABLE housing ALTER longitude TYPE NUMERIC(11, 8)');
        $this->addSql('ALTER TABLE housing ALTER price TYPE NUMERIC(10, 2)');
        $this->addSql('COMMENT ON COLUMN housing.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE housing DROP created_at');
        $this->addSql('ALTER TABLE housing ALTER category_id DROP NOT NULL');
        $this->addSql('ALTER TABLE housing ALTER description TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE housing ALTER description SET NOT NULL');
        $this->addSql('ALTER TABLE housing ALTER latitude TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE housing ALTER longitude TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE housing ALTER price TYPE DOUBLE PRECISION');
    }
}
