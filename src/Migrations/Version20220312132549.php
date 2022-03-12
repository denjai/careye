<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220312132549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE car ALTER updated TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE car ALTER updated DROP DEFAULT');
        $this->addSql('ALTER TABLE car ALTER created TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE car ALTER created DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN car.updated IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN car.created IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE car_history ALTER advert_updated TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE car_history ALTER advert_updated DROP DEFAULT');
        $this->addSql('ALTER TABLE car_history ALTER created TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE car_history ALTER created DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN car_history.advert_updated IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN car_history.created IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE car ALTER updated TYPE DATE');
        $this->addSql('ALTER TABLE car ALTER updated DROP DEFAULT');
        $this->addSql('ALTER TABLE car ALTER created TYPE DATE');
        $this->addSql('ALTER TABLE car ALTER created DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN car.updated IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN car.created IS \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE car_history ALTER advert_updated TYPE DATE');
        $this->addSql('ALTER TABLE car_history ALTER advert_updated DROP DEFAULT');
        $this->addSql('ALTER TABLE car_history ALTER created TYPE DATE');
        $this->addSql('ALTER TABLE car_history ALTER created DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN car_history.advert_updated IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN car_history.created IS \'(DC2Type:date_immutable)\'');
    }
}
