<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230211160539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE password_forgot_token DROP CONSTRAINT fk_86dc739ba76ed395');
        $this->addSql('DROP INDEX idx_86dc739ba76ed395');
        $this->addSql('ALTER TABLE password_forgot_token RENAME COLUMN user_id TO account_id');
        $this->addSql('ALTER TABLE password_forgot_token ADD CONSTRAINT FK_86DC739B9B6B5FBA FOREIGN KEY (account_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_86DC739B9B6B5FBA ON password_forgot_token (account_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE password_forgot_token DROP CONSTRAINT FK_86DC739B9B6B5FBA');
        $this->addSql('DROP INDEX IDX_86DC739B9B6B5FBA');
        $this->addSql('ALTER TABLE password_forgot_token RENAME COLUMN account_id TO user_id');
        $this->addSql('ALTER TABLE password_forgot_token ADD CONSTRAINT fk_86dc739ba76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_86dc739ba76ed395 ON password_forgot_token (user_id)');
    }
}
