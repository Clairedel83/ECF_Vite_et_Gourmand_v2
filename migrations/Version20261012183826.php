<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261012183826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD menu_nom VARCHAR(255) NOT NULL, ADD menu_composition LONGTEXT NOT NULL, ADD prix_per_pers NUMERIC(10, 2) NOT NULL, ADD montant_reduction NUMERIC(10, 2) NOT NULL, ADD prix_sous_total NUMERIC(10, 2) NOT NULL, ADD prix_materiel NUMERIC(10, 2) NOT NULL, ADD menu_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DCCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DCCD7E912 ON commande (menu_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DCCD7E912');
        $this->addSql('DROP INDEX IDX_6EEAA67DCCD7E912 ON commande');
        $this->addSql('ALTER TABLE commande DROP menu_nom, DROP menu_composition, DROP prix_per_pers, DROP montant_reduction, DROP prix_sous_total, DROP prix_materiel, DROP menu_id');
    }
}
