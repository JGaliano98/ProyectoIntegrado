<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240706083750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE detalle_pedido (id INT AUTO_INCREMENT NOT NULL, cantidad INT NOT NULL, precio NUMERIC(10, 0) NOT NULL, nombre_producto VARCHAR(255) NOT NULL, descripcion_producto VARCHAR(255) NOT NULL, precio_producto NUMERIC(10, 0) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE direccion (id INT AUTO_INCREMENT NOT NULL, calle VARCHAR(255) NOT NULL, numero INT NOT NULL, letra VARCHAR(255) NOT NULL, otros VARCHAR(255) NOT NULL, localidad VARCHAR(255) NOT NULL, provincia VARCHAR(255) NOT NULL, codigo_postal INT NOT NULL, pais VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metodo_pago (id INT AUTO_INCREMENT NOT NULL, tipo VARCHAR(255) NOT NULL, detalles VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE producto (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, descripcion VARCHAR(255) NOT NULL, precio NUMERIC(10, 0) NOT NULL, stock INT NOT NULL, stock_min INT NOT NULL, stock_max INT NOT NULL, foto VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resena (id INT AUTO_INCREMENT NOT NULL, puntuacion INT NOT NULL, comentario VARCHAR(255) NOT NULL, fecha DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE detalle_pedido');
        $this->addSql('DROP TABLE direccion');
        $this->addSql('DROP TABLE metodo_pago');
        $this->addSql('DROP TABLE producto');
        $this->addSql('DROP TABLE resena');
    }
}
