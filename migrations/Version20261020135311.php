<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261020135311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Contenus ajoutés aux pages du site (site_page_content) : sections rattachées à une page Twig';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE site_page_content (id INT AUTO_INCREMENT NOT NULL, route_name VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_CF2F468FF3667F83 (route_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE page_block ADD site_page_content_id INT DEFAULT NULL, CHANGE page_id page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE page_block ADD CONSTRAINT FK_E59A68F481DA4C98 FOREIGN KEY (site_page_content_id) REFERENCES site_page_content (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_E59A68F481DA4C98 ON page_block (site_page_content_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE page_block DROP FOREIGN KEY FK_E59A68F481DA4C98');
        $this->addSql('DROP TABLE site_page_content');
        $this->addSql('DROP INDEX IDX_E59A68F481DA4C98 ON page_block');
        $this->addSql('ALTER TABLE page_block DROP site_page_content_id, CHANGE page_id page_id INT NOT NULL');
    }
}
