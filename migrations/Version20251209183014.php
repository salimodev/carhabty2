<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251209183014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FC40FCFA8');
        $this->addSql('DROP INDEX IDX_B6BD307FC40FCFA8 ON message');
        $this->addSql('ALTER TABLE message ADD photo VARCHAR(255) DEFAULT NULL, DROP piece_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message ADD piece_id INT DEFAULT NULL, DROP photo');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FC40FCFA8 FOREIGN KEY (piece_id) REFERENCES pieces (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FC40FCFA8 ON message (piece_id)');
    }
}
