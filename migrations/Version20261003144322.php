<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261003144322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, numero_commande INT NOT NULL, date_commande DATETIME NOT NULL, date_prestation DATETIME NOT NULL, heure_livraison TIME NOT NULL, prix_menu VARCHAR(255) NOT NULL, nbre_pers INT NOT NULL, prix_livraison DOUBLE PRECISION NOT NULL, statut VARCHAR(255) NOT NULL, pret_materiel TINYINT NOT NULL, restitution_materiel TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE commande_historique (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, date_modification DATETIME NOT NULL, heure_modification TIME NOT NULL, commande_id INT NOT NULL, INDEX IDX_757DF90A82EA2E54 (commande_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE commande_historique ADD CONSTRAINT FK_757DF90A82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE dessert_allergene ADD CONSTRAINT FK_A356437F745B52FD FOREIGN KEY (dessert_id) REFERENCES dessert (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dessert_allergene ADD CONSTRAINT FK_A356437F4646AB2 FOREIGN KEY (allergene_id) REFERENCES allergene (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE entree_allergene ADD CONSTRAINT FK_85C2712FAF7BD910 FOREIGN KEY (entree_id) REFERENCES entree (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE entree_allergene ADD CONSTRAINT FK_85C2712F4646AB2 FOREIGN KEY (allergene_id) REFERENCES allergene (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93AF7BD910 FOREIGN KEY (entree_id) REFERENCES entree (id)');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93D73DB560 FOREIGN KEY (plat_id) REFERENCES plat (id)');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93745B52FD FOREIGN KEY (dessert_id) REFERENCES dessert (id)');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A9359027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('ALTER TABLE menu_regime ADD CONSTRAINT FK_79C112A4CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_regime ADD CONSTRAINT FK_79C112A435E7D534 FOREIGN KEY (regime_id) REFERENCES regime (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_condition ADD CONSTRAINT FK_A496EF77CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_condition ADD CONSTRAINT FK_A496EF77887793B6 FOREIGN KEY (condition_id) REFERENCES `condition` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plat_allergene ADD CONSTRAINT FK_6FA44BBFD73DB560 FOREIGN KEY (plat_id) REFERENCES plat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plat_allergene ADD CONSTRAINT FK_6FA44BBF4646AB2 FOREIGN KEY (allergene_id) REFERENCES allergene (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande_historique DROP FOREIGN KEY FK_757DF90A82EA2E54');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE commande_historique');
        $this->addSql('ALTER TABLE dessert_allergene DROP FOREIGN KEY FK_A356437F745B52FD');
        $this->addSql('ALTER TABLE dessert_allergene DROP FOREIGN KEY FK_A356437F4646AB2');
        $this->addSql('ALTER TABLE entree_allergene DROP FOREIGN KEY FK_85C2712FAF7BD910');
        $this->addSql('ALTER TABLE entree_allergene DROP FOREIGN KEY FK_85C2712F4646AB2');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A93AF7BD910');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A93D73DB560');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A93745B52FD');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A9359027487');
        $this->addSql('ALTER TABLE menu_condition DROP FOREIGN KEY FK_A496EF77CCD7E912');
        $this->addSql('ALTER TABLE menu_condition DROP FOREIGN KEY FK_A496EF77887793B6');
        $this->addSql('ALTER TABLE menu_regime DROP FOREIGN KEY FK_79C112A4CCD7E912');
        $this->addSql('ALTER TABLE menu_regime DROP FOREIGN KEY FK_79C112A435E7D534');
        $this->addSql('ALTER TABLE plat_allergene DROP FOREIGN KEY FK_6FA44BBFD73DB560');
        $this->addSql('ALTER TABLE plat_allergene DROP FOREIGN KEY FK_6FA44BBF4646AB2');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D60322AC');
    }
}
