<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261020133327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Menu (menu_item) et pages admin (page, page_block) + reprise du menu existant';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE menu_item (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, page_id INT DEFAULT NULL, label VARCHAR(100) NOT NULL, position INT DEFAULT 0 NOT NULL, visible TINYINT(1) DEFAULT 1 NOT NULL, highlighted TINYINT(1) DEFAULT 0 NOT NULL, target_type VARCHAR(10) DEFAULT \'route\' NOT NULL, route_name VARCHAR(100) DEFAULT NULL, anchor VARCHAR(100) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, INDEX IDX_D754D550727ACA70 (parent_id), INDEX IDX_D754D550C4663E4 (page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, hero_image_name VARCHAR(255) DEFAULT NULL, seo_title VARCHAR(255) DEFAULT NULL, seo_description VARCHAR(255) DEFAULT NULL, published TINYINT(1) DEFAULT 0 NOT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_140AB620989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page_block (id INT AUTO_INCREMENT NOT NULL, page_id INT NOT NULL, position INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, content LONGTEXT DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_width INT DEFAULT NULL, image_height INT DEFAULT NULL, image_alt VARCHAR(255) DEFAULT NULL, image_position VARCHAR(10) DEFAULT \'right\' NOT NULL, INDEX IDX_E59A68F4C4663E4 (page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE menu_item ADD CONSTRAINT FK_D754D550727ACA70 FOREIGN KEY (parent_id) REFERENCES menu_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_item ADD CONSTRAINT FK_D754D550C4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE page_block ADD CONSTRAINT FK_E59A68F4C4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE');

        // Reprise du menu codé en dur dans base.html.twig (ids explicites : la table vient d'être créée)
        // [id, parent, position, libellé, route, ancre, mis en avant]
        $items = [
            [1, null, 10, 'Accueil', 'app_home', null, false],
            [2, null, 20, 'Neuropsychologie', null, null, false],
            [3, 2, 10, 'Définition', 'app_neuropsy', null, false],
            [4, 2, 20, 'Pour qui ?', 'app_neuropsy', 'pour-qui', false],
            [5, 2, 30, 'Les bilans', 'app_neuropsy', 'bilans', false],
            [6, 2, 40, 'Le déroulement', 'app_neuropsy', 'deroulement', false],
            [7, 2, 50, 'La durée', 'app_neuropsy', 'durée', false],
            [8, 2, 60, 'La remédiation cognitive', 'app_neuropsy', 'remédiation-cognitive', false],
            [9, null, 30, 'Les T.C.C', null, null, false],
            [10, 9, 10, 'Définition des T.C.C', 'app_tcc', 'definition', false],
            [11, 9, 20, 'Les techniques', 'app_tcc', 'les-techniques', false],
            [12, 9, 30, 'Pour qui ?', 'app_tcc', 'pour-qui', false],
            [13, 9, 40, 'Le déroulement', 'app_tcc', 'déroulement', false],
            [14, null, 40, 'Honoraires', 'app_honoraires', null, false],
            [15, null, 50, 'Se former', null, null, true],
            [16, 15, 10, 'Organisme de formation', 'app_organisme_de_formation', 'organisme-de-formation', false],
            [17, 15, 20, 'Les formations', 'app_organisme_de_formation', 'formations', false],
            [18, 15, 30, 'Inscriptions', 'app_organisme_de_formation', 'inscriptions', false],
            [19, 15, 40, 'Informations pratiques', 'app_organisme_de_formation', 'conditions-generales', false],
            [20, null, 60, 'Références', 'app_references', null, false],
            [21, null, 70, 'Le cabinet', 'app_cabinet', null, false],
        ];

        foreach ($items as [$id, $parent, $position, $label, $route, $anchor, $highlighted]) {
            $this->addSql(
                'INSERT INTO menu_item (id, parent_id, position, label, target_type, route_name, anchor, highlighted, visible) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)',
                [$id, $parent, $position, $label, $route ? 'route' : 'none', $route, $anchor, (int) $highlighted],
            );
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu_item DROP FOREIGN KEY FK_D754D550727ACA70');
        $this->addSql('ALTER TABLE menu_item DROP FOREIGN KEY FK_D754D550C4663E4');
        $this->addSql('ALTER TABLE page_block DROP FOREIGN KEY FK_E59A68F4C4663E4');
        $this->addSql('DROP TABLE menu_item');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE page_block');
    }
}
