<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261020131410 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Jours de consultation : semaine type (opening_day), périodes exceptionnelles (schedule_period), précision dans information';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE opening_day (id INT AUTO_INCREMENT NOT NULL, day_of_week SMALLINT NOT NULL, is_open TINYINT(1) DEFAULT 0 NOT NULL, hours VARCHAR(100) DEFAULT NULL, UNIQUE INDEX uniq_opening_day_day_of_week (day_of_week), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE schedule_period (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, start_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', end_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', closed TINYINT(1) DEFAULT 1 NOT NULL, message LONGTEXT DEFAULT NULL, show_banner TINYINT(1) DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE information ADD consultation_note VARCHAR(255) DEFAULT NULL');

        // Reprise de la semaine codée en dur sur la page Cabinet : lundi, mardi, jeudi, vendredi
        foreach ([1 => 1, 2 => 1, 3 => 0, 4 => 1, 5 => 1, 6 => 0, 7 => 0] as $day => $open) {
            $this->addSql('INSERT INTO opening_day (day_of_week, is_open) VALUES (?, ?)', [$day, $open]);
        }
        $this->addSql(
            'UPDATE information SET consultation_note = ? WHERE consultation_note IS NULL',
            ['Ouvert également pendant les vacances scolaires'],
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE opening_day');
        $this->addSql('DROP TABLE schedule_period');
        $this->addSql('ALTER TABLE information DROP consultation_note');
    }
}
