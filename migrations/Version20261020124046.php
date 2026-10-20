<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261020124046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Slider page d\'accueil : table slide + reprise des 4 slides existantes';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE slide (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, mobile_image_name VARCHAR(255) DEFAULT NULL, position INT DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB');

        // Reprise des slides jusque-là codées en dur dans pages/index.html.twig (images copiées dans public/uploads/slides)
        $slides = [
            [10, "Cabinet de psychologie\nEmmanuelle Mir", "Psychologue, spécialisée en neuropsychologie, psychothérapeute TCC – EMDR\nÀ La Bâtie-Neuve", 'hero.webp', 'hero-mobile.webp'],
            [20, 'Spécialisée en neuropsychologie des troubles des apprentissages', null, 'girl.webp', null],
            [30, "Spécialisée en T.C.C\nThérapie Comportementale et Cognitive", null, 'unhappy.webp', null],
            [40, 'Prise en charge enfants, adultes et personnes âgées', 'Maison de santé, 133 rue des Écoles, 05230 La Bâtie-Neuve', 'beach.webp', null],
        ];

        foreach ($slides as [$position, $title, $text, $image, $mobileImage]) {
            $this->addSql(
                'INSERT INTO slide (position, title, text, image_name, mobile_image_name) VALUES (?, ?, ?, ?, ?)',
                [$position, $title, $text, $image, $mobileImage],
            );
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE slide');
    }
}
