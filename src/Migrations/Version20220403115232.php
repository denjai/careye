<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220403115232 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_773de69d2a3e9c94');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_773DE69D2A3E9C94A76ED395 ON car (remote_id, user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_773DE69D2A3E9C94A76ED395');
        $this->addSql('CREATE UNIQUE INDEX uniq_773de69d2a3e9c94 ON car (remote_id)');
    }
}
