<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240917003908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE imagen (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user CHANGE nombre nombre VARCHAR(255) NOT NULL, CHANGE apellido1 apellido1 VARCHAR(255) NOT NULL, CHANGE apellido2 apellido2 VARCHAR(255) NOT NULL, CHANGE telefono telefono INT NOT NULL, CHANGE is_verified is_verified TINYINT(1) NOT NULL, CHANGE activation_token activation_token VARCHAR(36) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE imagen');
        $this->addSql('ALTER TABLE user CHANGE nombre nombre VARCHAR(255) DEFAULT NULL, CHANGE apellido1 apellido1 VARCHAR(255) DEFAULT NULL, CHANGE apellido2 apellido2 VARCHAR(255) DEFAULT NULL, CHANGE telefono telefono INT DEFAULT NULL, CHANGE is_verified is_verified TINYINT(1) DEFAULT NULL, CHANGE activation_token activation_token VARCHAR(255) DEFAULT NULL');
    }
}
