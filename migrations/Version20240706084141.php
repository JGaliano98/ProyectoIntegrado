<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240706084141 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pedido (id INT AUTO_INCREMENT NOT NULL, fecha DATE NOT NULL, total NUMERIC(10, 0) NOT NULL, estado VARCHAR(255) NOT NULL, fecha_pago DATE NOT NULL, nombre_usuario VARCHAR(255) NOT NULL, email_usuario VARCHAR(255) NOT NULL, calle_direccion VARCHAR(255) NOT NULL, numero_direccion INT NOT NULL, localidad_direccion VARCHAR(255) NOT NULL, provincia_direccion VARCHAR(255) NOT NULL, codigo_postal_direccion INT NOT NULL, pais_direccion VARCHAR(255) NOT NULL, otra_info_direccion VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE pedido');
    }
}
