<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261020130600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Honoraires : table fee + reprise des tarifs existants';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fee (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, position INT DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB');

        // Reprise des tarifs jusque-là codés en dur dans pages/honoraires.html.twig
        $fees = [
            [10, 'Premier entretien', '70 euros entre 1h et 1h30 (questionnaire + entretien)'],
            [20, 'Consultation individuelle', '70 euros'],
            [30, 'Bilan d’efficience intellectuelle (enfant-adulte)', '300 euros - Paiement possible en plusieurs fois.'],
            [40, 'Bilan neuropsychologique (enfant-adulte)', '400 euros - Paiement possible en plusieurs fois.'],
            [50, 'Bilan neuropsychologique + bilan efficience intellectuelle', '500 euros - Paiement possible en plusieurs fois.'],
            [60, 'Bilan attentionnel', '250 euros - Paiement possible en plusieurs fois.'],
            [70, "Bilan attentionnel + bilan d'efficience intellectuelle", '500 euros - Paiement possible en plusieurs fois.'],
            [80, 'Groupe', "A partir de 3 personnes.\nTarif sur demande"],
            [90, 'Interventions', "A définir avec la structure demandeuse\nPossibilité de consultation à domicile, secteur Chorges (frais kilométriques selon barème URSSAF)."],
            [100, 'Remboursement', "Il n’existe pas à ce jour de convention avec la sécurité sociale.\nCependant certaines mutuelles sont susceptibles de prendre en charge un certain nombre de séances par an. A votre demande, une facture sera établit à chaque consultation afin de pouvoir bénéficier de vos remboursements."],
        ];

        foreach ($fees as [$position, $title, $text]) {
            $this->addSql(
                'INSERT INTO fee (position, title, text) VALUES (?, ?, ?)',
                [$position, $title, $text],
            );
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE fee');
    }
}
