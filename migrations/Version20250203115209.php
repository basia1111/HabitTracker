<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250203115209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE habit (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, time TIME NOT NULL, duration INT NOT NULL, description LONGTEXT DEFAULT NULL, frequency VARCHAR(50) NOT NULL, week_days LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', google_recurrence_rule VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, completions LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_44FE2172A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE habit ADD CONSTRAINT FK_44FE2172A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE habit DROP FOREIGN KEY FK_44FE2172A76ED395');
        $this->addSql('DROP TABLE habit');
    }
}
