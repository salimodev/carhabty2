<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251101171004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pieces CHANGE demande_id demande_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pieces ADD CONSTRAINT FK_B92D747280E95E18 FOREIGN KEY (demande_id) REFERENCES demande (id)');
        $this->addSql('CREATE INDEX IDX_B92D747280E95E18 ON pieces (demande_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pieces DROP FOREIGN KEY FK_B92D747280E95E18');
        $this->addSql('DROP INDEX IDX_B92D747280E95E18 ON pieces');
        $this->addSql('ALTER TABLE pieces CHANGE demande_id demande_id INT NOT NULL');
    }
}
