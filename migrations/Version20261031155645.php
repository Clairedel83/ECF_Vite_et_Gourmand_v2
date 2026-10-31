<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261031155645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE telephone telephone VARCHAR(10) DEFAULT NULL, CHANGE ville ville VARCHAR(50) DEFAULT NULL, CHANGE adresse_postale adresse_postale VARCHAR(255) DEFAULT NULL, CHANGE code_postal code_postal VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE telephone telephone VARCHAR(10) NOT NULL, CHANGE ville ville VARCHAR(50) NOT NULL, CHANGE adresse_postale adresse_postale VARCHAR(255) NOT NULL, CHANGE code_postal code_postal VARCHAR(255) NOT NULL');
    }
}
