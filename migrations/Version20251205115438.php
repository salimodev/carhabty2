<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251205120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la colonne date_inscription à la table users';
    }

    public function up(Schema $schema): void
    {
        // Ajout de la colonne date_inscription avec valeur par défaut CURRENT_TIMESTAMP
        $this->addSql('ALTER TABLE users ADD date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');

        // Mise à jour des anciennes lignes si besoin (optionnel si tu as déjà des données)
        $this->addSql("UPDATE users SET date_inscription = NOW() WHERE date_inscription IS NULL");
    }

    public function down(Schema $schema): void
    {
        // Suppression de la colonne en cas de rollback
        $this->addSql('ALTER TABLE users DROP COLUMN date_inscription');
    }
}
