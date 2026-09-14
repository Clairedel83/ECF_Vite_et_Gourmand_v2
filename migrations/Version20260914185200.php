<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260914185200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE menu_dessert');
        $this->addSql('DROP TABLE menu_entree');
        $this->addSql('DROP TABLE menu_plat');
        $this->addSql('ALTER TABLE dessert_allergene ADD CONSTRAINT FK_A356437F745B52FD FOREIGN KEY (dessert_id) REFERENCES dessert (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dessert_allergene ADD CONSTRAINT FK_A356437F4646AB2 FOREIGN KEY (allergene_id) REFERENCES allergene (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE entree_allergene ADD CONSTRAINT FK_85C2712FAF7BD910 FOREIGN KEY (entree_id) REFERENCES entree (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE entree_allergene ADD CONSTRAINT FK_85C2712F4646AB2 FOREIGN KEY (allergene_id) REFERENCES allergene (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu ADD entree_id INT DEFAULT NULL, ADD plat_id INT DEFAULT NULL, ADD dessert_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93AF7BD910 FOREIGN KEY (entree_id) REFERENCES entree (id)');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93D73DB560 FOREIGN KEY (plat_id) REFERENCES plat (id)');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93745B52FD FOREIGN KEY (dessert_id) REFERENCES dessert (id)');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A9359027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('CREATE INDEX IDX_7D053A93AF7BD910 ON menu (entree_id)');
        $this->addSql('CREATE INDEX IDX_7D053A93D73DB560 ON menu (plat_id)');
        $this->addSql('CREATE INDEX IDX_7D053A93745B52FD ON menu (dessert_id)');
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
        $this->addSql('CREATE TABLE menu_dessert (menu_id INT NOT NULL, dessert_id INT NOT NULL, INDEX IDX_F1F20628CCD7E912 (menu_id), INDEX IDX_F1F20628745B52FD (dessert_id), PRIMARY KEY (menu_id, dessert_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE menu_entree (menu_id INT NOT NULL, entree_id INT NOT NULL, INDEX IDX_8AC42F7EAF7BD910 (entree_id), INDEX IDX_8AC42F7ECCD7E912 (menu_id), PRIMARY KEY (menu_id, entree_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE menu_plat (menu_id INT NOT NULL, plat_id INT NOT NULL, INDEX IDX_E8775249D73DB560 (plat_id), INDEX IDX_E8775249CCD7E912 (menu_id), PRIMARY KEY (menu_id, plat_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('ALTER TABLE dessert_allergene DROP FOREIGN KEY FK_A356437F745B52FD');
        $this->addSql('ALTER TABLE dessert_allergene DROP FOREIGN KEY FK_A356437F4646AB2');
        $this->addSql('ALTER TABLE entree_allergene DROP FOREIGN KEY FK_85C2712FAF7BD910');
        $this->addSql('ALTER TABLE entree_allergene DROP FOREIGN KEY FK_85C2712F4646AB2');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A93AF7BD910');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A93D73DB560');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A93745B52FD');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A9359027487');
        $this->addSql('DROP INDEX IDX_7D053A93AF7BD910 ON menu');
        $this->addSql('DROP INDEX IDX_7D053A93D73DB560 ON menu');
        $this->addSql('DROP INDEX IDX_7D053A93745B52FD ON menu');
        $this->addSql('ALTER TABLE menu DROP entree_id, DROP plat_id, DROP dessert_id');
        $this->addSql('ALTER TABLE menu_condition DROP FOREIGN KEY FK_A496EF77CCD7E912');
        $this->addSql('ALTER TABLE menu_condition DROP FOREIGN KEY FK_A496EF77887793B6');
        $this->addSql('ALTER TABLE menu_regime DROP FOREIGN KEY FK_79C112A4CCD7E912');
        $this->addSql('ALTER TABLE menu_regime DROP FOREIGN KEY FK_79C112A435E7D534');
        $this->addSql('ALTER TABLE plat_allergene DROP FOREIGN KEY FK_6FA44BBFD73DB560');
        $this->addSql('ALTER TABLE plat_allergene DROP FOREIGN KEY FK_6FA44BBF4646AB2');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D60322AC');
    }
}
