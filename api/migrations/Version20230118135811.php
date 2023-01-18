<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230118135811 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE renting_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE report_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE category (id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN category.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE housing (id UUID NOT NULL, owner_id UUID NOT NULL, category_id UUID NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, latitude NUMERIC(10, 8) DEFAULT NULL, longitude NUMERIC(11, 8) DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FB8142C37E3C61F9 ON housing (owner_id)');
        $this->addSql('CREATE INDEX IDX_FB8142C312469DE2 ON housing (category_id)');
        $this->addSql('COMMENT ON COLUMN housing.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN housing.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN housing.category_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN housing.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "like" (id UUID NOT NULL, author_id UUID NOT NULL, housing_id UUID NOT NULL, liked BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AC6340B3F675F31B ON "like" (author_id)');
        $this->addSql('CREATE INDEX IDX_AC6340B3AD5873E3 ON "like" (housing_id)');
        $this->addSql('COMMENT ON COLUMN "like".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "like".author_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "like".housing_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE media (id UUID NOT NULL, housing_id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6A2CA10CAD5873E3 ON media (housing_id)');
        $this->addSql('COMMENT ON COLUMN media.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN media.housing_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE renting (id VARCHAR(255) NOT NULL, client_id UUID NOT NULL, housing_id UUID NOT NULL, date_start DATE NOT NULL, date_end DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_13533C0F19EB6921 ON renting (client_id)');
        $this->addSql('CREATE INDEX IDX_13533C0FAD5873E3 ON renting (housing_id)');
        $this->addSql('COMMENT ON COLUMN renting.client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN renting.housing_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN renting.date_start IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN renting.date_end IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE report (id INT NOT NULL, renting_id VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL, status BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C42F7784EC8CFBAF ON report (renting_id)');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE user_registration_token (id UUID NOT NULL, account_id UUID NOT NULL, token VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CD9E7289B6B5FBA ON user_registration_token (account_id)');
        $this->addSql('COMMENT ON COLUMN user_registration_token.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_registration_token.account_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_registration_token.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE housing ADD CONSTRAINT FK_FB8142C37E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE housing ADD CONSTRAINT FK_FB8142C312469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "like" ADD CONSTRAINT FK_AC6340B3F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "like" ADD CONSTRAINT FK_AC6340B3AD5873E3 FOREIGN KEY (housing_id) REFERENCES housing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CAD5873E3 FOREIGN KEY (housing_id) REFERENCES housing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE renting ADD CONSTRAINT FK_13533C0F19EB6921 FOREIGN KEY (client_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE renting ADD CONSTRAINT FK_13533C0FAD5873E3 FOREIGN KEY (housing_id) REFERENCES housing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784EC8CFBAF FOREIGN KEY (renting_id) REFERENCES renting (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_registration_token ADD CONSTRAINT FK_7CD9E7289B6B5FBA FOREIGN KEY (account_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE renting_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE report_id_seq CASCADE');
        $this->addSql('ALTER TABLE housing DROP CONSTRAINT FK_FB8142C37E3C61F9');
        $this->addSql('ALTER TABLE housing DROP CONSTRAINT FK_FB8142C312469DE2');
        $this->addSql('ALTER TABLE "like" DROP CONSTRAINT FK_AC6340B3F675F31B');
        $this->addSql('ALTER TABLE "like" DROP CONSTRAINT FK_AC6340B3AD5873E3');
        $this->addSql('ALTER TABLE media DROP CONSTRAINT FK_6A2CA10CAD5873E3');
        $this->addSql('ALTER TABLE renting DROP CONSTRAINT FK_13533C0F19EB6921');
        $this->addSql('ALTER TABLE renting DROP CONSTRAINT FK_13533C0FAD5873E3');
        $this->addSql('ALTER TABLE report DROP CONSTRAINT FK_C42F7784EC8CFBAF');
        $this->addSql('ALTER TABLE user_registration_token DROP CONSTRAINT FK_7CD9E7289B6B5FBA');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE housing');
        $this->addSql('DROP TABLE "like"');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE renting');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_registration_token');
    }
}
