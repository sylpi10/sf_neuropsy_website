<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261020143301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Références : diplômes (diploma) et expériences (experience) + reprise du contenu ; immatriculations en texte modifiable';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE diploma (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, show_icon TINYINT(1) DEFAULT 0 NOT NULL, wide TINYINT(1) DEFAULT 0 NOT NULL, position INT DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE experience (id INT AUTO_INCREMENT NOT NULL, label LONGTEXT NOT NULL, is_current TINYINT(1) DEFAULT 0 NOT NULL, position INT DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB');

        // Reprise de templates/pages/references.html.twig
        // [ordre, titre, texte (HTML), lieu, icône diplôme, carte large]
        $diplomas = [
            [10, 'Psychologue Clinicienne', '<p>Master de Sciences Humaines et Sociales, Mention Psychologie Clinique, Major de promo (Spécialité ATIE – Accompagnement des Troubles cognitifs et socio-cognitifs, des Inadaptations et des Exclusions).</p>', 'Clermont-Ferrand', 1, 0],
            [20, 'Psychothérapeute en Thérapie Comportementale et Cognitive', '<p>Diplôme Inter-Universitaire de Thérapie Comportementale et Cognitive.</p>', 'Clermont-Ferrand et Saint-Étienne', 1, 0],
            [30, 'Formations supplémentaires', '<ul><li>Diplôme universitaire de neuropsychopathologie des apprentissages scolaires.</li><li>EMDR Niveau 1 et 2 (Institut d\'EMDR FRANCE)</li></ul>', 'Lyon', 0, 0],
            [40, 'Associations et réseaux', '<ul><li>Présidente de l\'association Positiv\'être</li><li>Membre de l\'AFTCC</li></ul>', null, 0, 0],
            [50, 'Thème de recherche', '<p>Outils destinés aux enseignant·e·s et aux AVS du 1er degré intervenant auprès d\'enfants présentant un trouble du déficit de l\'attention avec ou sans hyperactivité (TDA/H).</p>', null, 0, 1],
        ];
        foreach ($diplomas as [$position, $title, $content, $location, $showIcon, $wide]) {
            $this->addSql(
                'INSERT INTO diploma (position, title, content, location, show_icon, wide) VALUES (?, ?, ?, ?, ?, ?)',
                [$position, $title, $content, $location, $showIcon, $wide],
            );
        }

        // [actuelle, ordre, texte]
        $experiences = [
            [1, 10, 'Psychologue en libérale et formatrice.'],
            [1, 20, 'Gérante et fondatrice de l\'organisme de formations « Psycho Parent Alp »'],
            [0, 10, 'Problématiques de délinquances, addictives et sociales. Secteur social et judiciaire. (ADSEA 05)'],
            [0, 20, 'Supervision d\'équipe intervenant auprès d\'un public présentant un trouble du spectre de l\'autisme. (Association Positiv\'être)'],
            [0, 30, 'Psychologue au CEFP, adolescents et jeunes adultes (16-20 ans) placés/orientés par la PJJ et l\'ASE.'],
            [0, 40, 'Psychologue centre Jean Cluzel (5 ans).'],
            [0, 50, 'Services ITEP, IME, SESSAD.'],
            [0, 60, 'Public 3/20 ans. Profils déficience intellectuelle, trouble du comportement, des apprentissages, TSA.'],
            [0, 70, 'Unité maternelle autisme. PEPADS. Secteur médico-social.'],
        ];
        foreach ($experiences as [$current, $position, $label]) {
            $this->addSql(
                'INSERT INTO experience (is_current, position, label) VALUES (?, ?, ?)',
                [$current, $position, $label],
            );
        }

        $this->addSql(
            'INSERT INTO editable_text (text_key, route_name, label, position, content) VALUES (?, ?, ?, ?, ?)',
            ['references.immatriculations', 'app_references', 'Immatriculations (ADELI, SIRET)', 10, '<p>Numéro ADELI : 059301895</p><p>SIRET : 812 767 143 000 41</p>'],
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE diploma');
        $this->addSql('DROP TABLE experience');
        $this->addSql("DELETE FROM editable_text WHERE text_key = 'references.immatriculations'");
    }
}
