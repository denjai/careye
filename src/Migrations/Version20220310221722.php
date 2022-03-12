<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220310221722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE car (id INT NOT NULL, remote_id VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, amount VARCHAR(255) NOT NULL, currency VARCHAR(255) NOT NULL, updated DATE DEFAULT NULL, created DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_773DE69D2A3E9C94 ON car (remote_id)');
        $this->addSql('COMMENT ON COLUMN car.updated IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN car.created IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE car_history (id INT NOT NULL, car_id INT DEFAULT NULL, amount VARCHAR(255) NOT NULL, currency VARCHAR(255) NOT NULL, advert_updated DATE DEFAULT NULL, created DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_74118B97C3C6F69F ON car_history (car_id)');
        $this->addSql('COMMENT ON COLUMN car_history.advert_updated IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN car_history.created IS \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE car_history ADD CONSTRAINT FK_74118B97C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE car_history DROP CONSTRAINT FK_74118B97C3C6F69F');
        $this->addSql('DROP TABLE car');
        $this->addSql('DROP TABLE car_history');
    }
}
