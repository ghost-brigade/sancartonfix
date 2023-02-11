<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230211160419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE password_forgot_token ADD expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE password_forgot_token ALTER token TYPE VARCHAR(50)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_86DC739B5F37A13B ON password_forgot_token (token)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_86DC739B5F37A13B');
        $this->addSql('ALTER TABLE password_forgot_token DROP expires_at');
        $this->addSql('ALTER TABLE password_forgot_token ALTER token TYPE VARCHAR(255)');
    }
}
