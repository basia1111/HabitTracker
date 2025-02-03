<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250202172746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users ADD google_calendar_id VARCHAR(255) DEFAULT NULL, ADD google_refresh_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users RENAME INDEX email_id TO email_idx');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users DROP google_calendar_id, DROP google_refresh_token');
        $this->addSql('ALTER TABLE users RENAME INDEX email_idx TO email_id');
    }
}
