<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230412161948 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE circuit MODIFY idC INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON circuit');
        $this->addSql('ALTER TABLE circuit ADD nom_c VARCHAR(255) NOT NULL, ADD liste_c INT NOT NULL, ADD nbrbus_c INT NOT NULL, ADD distance_c VARCHAR(255) NOT NULL, DROP nomC, DROP listeC, DROP nbrbusC, DROP distanceC, CHANGE idC id_c INT AUTO_INCREMENT NOT NULL, CHANGE horaireC horaire_c DATE NOT NULL');
        $this->addSql('ALTER TABLE circuit ADD PRIMARY KEY (id_c)');
        $this->addSql('ALTER TABLE station MODIFY idS INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON station');
        $this->addSql('ALTER TABLE station ADD nom_s VARCHAR(255) NOT NULL, ADD adresse_s VARCHAR(255) NOT NULL, ADD ligne_s VARCHAR(255) NOT NULL, ADD equipement_s VARCHAR(255) NOT NULL, ADD commentaire_s VARCHAR(255) NOT NULL, ADD idC INT DEFAULT NULL, DROP nomS, DROP adresseS, DROP ligneS, DROP equipementS, DROP commentaireS, CHANGE idS id_s INT AUTO_INCREMENT NOT NULL, CHANGE horaireS horaire_s DATE NOT NULL');
        $this->addSql('ALTER TABLE station ADD CONSTRAINT FK_9F39F8B156039734 FOREIGN KEY (idC) REFERENCES circuit (id_c)');
        $this->addSql('CREATE INDEX IDX_9F39F8B156039734 ON station (idC)');
        $this->addSql('ALTER TABLE station ADD PRIMARY KEY (id_s)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE circuit MODIFY id_c INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON circuit');
        $this->addSql('ALTER TABLE circuit ADD nomC VARCHAR(255) NOT NULL, ADD listeC INT NOT NULL, ADD nbrbusC INT NOT NULL, ADD distanceC VARCHAR(255) NOT NULL, DROP nom_c, DROP liste_c, DROP nbrbus_c, DROP distance_c, CHANGE id_c idC INT AUTO_INCREMENT NOT NULL, CHANGE horaire_c horaireC DATE NOT NULL');
        $this->addSql('ALTER TABLE circuit ADD PRIMARY KEY (idC)');
        $this->addSql('ALTER TABLE station MODIFY id_s INT NOT NULL');
        $this->addSql('ALTER TABLE station DROP FOREIGN KEY FK_9F39F8B156039734');
        $this->addSql('DROP INDEX IDX_9F39F8B156039734 ON station');
        $this->addSql('DROP INDEX `PRIMARY` ON station');
        $this->addSql('ALTER TABLE station ADD nomS VARCHAR(25) NOT NULL, ADD adresseS VARCHAR(255) NOT NULL, ADD ligneS VARCHAR(255) NOT NULL, ADD equipementS VARCHAR(255) NOT NULL, ADD commentaireS VARCHAR(255) NOT NULL, DROP nom_s, DROP adresse_s, DROP ligne_s, DROP equipement_s, DROP commentaire_s, DROP idC, CHANGE id_s idS INT AUTO_INCREMENT NOT NULL, CHANGE horaire_s horaireS DATE NOT NULL');
        $this->addSql('ALTER TABLE station ADD PRIMARY KEY (idS)');
    }
}
