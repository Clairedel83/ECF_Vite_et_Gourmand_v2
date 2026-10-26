<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261026203207 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE intervention_commande (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(20) NOT NULL, date DATETIME NOT NULL, mode_contact VARCHAR(20) DEFAULT NULL, motif LONGTEXT DEFAULT NULL, commande_id INT NOT NULL, auteur_id INT NOT NULL, INDEX IDX_5379DDDE82EA2E54 (commande_id), INDEX IDX_5379DDDE60BB6FE6 (auteur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE intervention_commande ADD CONSTRAINT FK_5379DDDE82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE intervention_commande ADD CONSTRAINT FK_5379DDDE60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY `FK_6EEAA67DF1AC4E24`');
        $this->addSql('DROP INDEX IDX_6EEAA67DF1AC4E24 ON commande');
        $this->addSql('ALTER TABLE commande DROP motif_intervention, DROP mode_contact_intervention, DROP date_intervention, DROP modifiee_par_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE intervention_commande DROP FOREIGN KEY FK_5379DDDE82EA2E54');
        $this->addSql('ALTER TABLE intervention_commande DROP FOREIGN KEY FK_5379DDDE60BB6FE6');
        $this->addSql('DROP TABLE intervention_commande');
        $this->addSql('ALTER TABLE commande ADD motif_intervention LONGTEXT DEFAULT NULL, ADD mode_contact_intervention VARCHAR(255) DEFAULT NULL, ADD date_intervention DATETIME DEFAULT NULL, ADD modifiee_par_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT `FK_6EEAA67DF1AC4E24` FOREIGN KEY (modifiee_par_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_6EEAA67DF1AC4E24 ON commande (modifiee_par_id)');
    }
}
