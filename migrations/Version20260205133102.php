<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260205133102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE annonce (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, reference VARCHAR(255) DEFAULT NULL, marque VARCHAR(255) NOT NULL, modele VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, description VARCHAR(255) DEFAULT NULL, images JSON DEFAULT NULL, image_principale VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL, date_modification DATETIME NOT NULL, publier TINYINT(1) NOT NULL, INDEX IDX_F65593E5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE banner_menu (id INT AUTO_INCREMENT NOT NULL, logo VARCHAR(255) NOT NULL, publier TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE demande (id INT AUTO_INCREMENT NOT NULL, offrecompte_id INT DEFAULT NULL, marque VARCHAR(255) NOT NULL, modele VARCHAR(255) NOT NULL, chassis VARCHAR(255) NOT NULL, energie VARCHAR(255) NOT NULL, etatmoteur VARCHAR(255) NOT NULL, photocartegrise VARCHAR(255) DEFAULT NULL, vendeurneuf INT NOT NULL, vendeuroccasion INT NOT NULL, zone VARCHAR(255) NOT NULL, offreemail VARCHAR(255) DEFAULT NULL, datecreate DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', code VARCHAR(50) NOT NULL, statut VARCHAR(255) NOT NULL, publier TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_2694D7A577153098 (code), INDEX IDX_2694D7A5E7C7D8FB (offrecompte_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE footer (id INT AUTO_INCREMENT NOT NULL, adresse VARCHAR(255) DEFAULT NULL, telephone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, facebook VARCHAR(255) DEFAULT NULL, instagram VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, logoheader VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE home_content (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, video_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invite_page_mecanicien (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, video_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invite_page_particulier (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, video_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invite_page_proprietaire (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, video_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invite_page_vendeur_neuf (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, video_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invite_page_vendeur_occasion (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, video_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE marque (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, receiver_id INT NOT NULL, annonce_id INT DEFAULT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_read TINYINT(1) NOT NULL, photo VARCHAR(255) DEFAULT NULL, INDEX IDX_B6BD307FF624B39D (sender_id), INDEX IDX_B6BD307FCD53EDB6 (receiver_id), INDEX IDX_B6BD307F8805AB2F (annonce_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE modele (id INT AUTO_INCREMENT NOT NULL, marque_id INT NOT NULL, nom VARCHAR(255) NOT NULL, INDEX IDX_100285584827B9B2 (marque_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, offre_id INT DEFAULT NULL, message VARCHAR(255) NOT NULL, is_read TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_BF5476CAA76ED395 (user_id), INDEX IDX_BF5476CA4CC8505A (offre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offre (id INT AUTO_INCREMENT NOT NULL, demande_id INT NOT NULL, user_id INT NOT NULL, numero_offre VARCHAR(50) NOT NULL, observation LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', validite_debut DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', validite_fin DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(20) NOT NULL, token VARCHAR(64) DEFAULT NULL, INDEX IDX_AF86866F80E95E18 (demande_id), INDEX IDX_AF86866FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offre_piece (id INT AUTO_INCREMENT NOT NULL, offre_id INT NOT NULL, piece_id INT NOT NULL, prix1 NUMERIC(10, 3) DEFAULT NULL, marque1 VARCHAR(100) DEFAULT NULL, prix2 NUMERIC(10, 3) DEFAULT NULL, marque2 VARCHAR(100) DEFAULT NULL, prix3 NUMERIC(10, 3) DEFAULT NULL, marque3 VARCHAR(100) DEFAULT NULL, INDEX IDX_13E0ECB84CC8505A (offre_id), INDEX IDX_13E0ECB8C40FCFA8 (piece_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pieces (id INT AUTO_INCREMENT NOT NULL, demande_id INT DEFAULT NULL, designation VARCHAR(255) NOT NULL, referance VARCHAR(255) NOT NULL, photo VARCHAR(255) DEFAULT NULL, observation VARCHAR(255) DEFAULT NULL, datecreate_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B92D747280E95E18 (demande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, tel1 INT NOT NULL, tel2 INT DEFAULT NULL, reset_token VARCHAR(255) DEFAULT NULL, is_blocked TINYINT(1) NOT NULL, date_inscription DATETIME NOT NULL, photo VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E91393C58D (tel1), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE visit (id INT AUTO_INCREMENT NOT NULL, ip VARCHAR(255) NOT NULL, visited_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A5E7C7D8FB FOREIGN KEY (offrecompte_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F8805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id)');
        $this->addSql('ALTER TABLE modele ADD CONSTRAINT FK_100285584827B9B2 FOREIGN KEY (marque_id) REFERENCES marque (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA4CC8505A FOREIGN KEY (offre_id) REFERENCES offre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866F80E95E18 FOREIGN KEY (demande_id) REFERENCES demande (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offre_piece ADD CONSTRAINT FK_13E0ECB84CC8505A FOREIGN KEY (offre_id) REFERENCES offre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offre_piece ADD CONSTRAINT FK_13E0ECB8C40FCFA8 FOREIGN KEY (piece_id) REFERENCES pieces (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pieces ADD CONSTRAINT FK_B92D747280E95E18 FOREIGN KEY (demande_id) REFERENCES demande (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E5A76ED395');
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_2694D7A5E7C7D8FB');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FCD53EDB6');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F8805AB2F');
        $this->addSql('ALTER TABLE modele DROP FOREIGN KEY FK_100285584827B9B2');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA4CC8505A');
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866F80E95E18');
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866FA76ED395');
        $this->addSql('ALTER TABLE offre_piece DROP FOREIGN KEY FK_13E0ECB84CC8505A');
        $this->addSql('ALTER TABLE offre_piece DROP FOREIGN KEY FK_13E0ECB8C40FCFA8');
        $this->addSql('ALTER TABLE pieces DROP FOREIGN KEY FK_B92D747280E95E18');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE annonce');
        $this->addSql('DROP TABLE banner_menu');
        $this->addSql('DROP TABLE demande');
        $this->addSql('DROP TABLE footer');
        $this->addSql('DROP TABLE home_content');
        $this->addSql('DROP TABLE invite_page_mecanicien');
        $this->addSql('DROP TABLE invite_page_particulier');
        $this->addSql('DROP TABLE invite_page_proprietaire');
        $this->addSql('DROP TABLE invite_page_vendeur_neuf');
        $this->addSql('DROP TABLE invite_page_vendeur_occasion');
        $this->addSql('DROP TABLE marque');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE modele');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE offre');
        $this->addSql('DROP TABLE offre_piece');
        $this->addSql('DROP TABLE pieces');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE visit');
    }
}
