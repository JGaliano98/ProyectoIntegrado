<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241028154621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE detalle_pedido DROP FOREIGN KEY FK_A834F5694854653A');
        $this->addSql('ALTER TABLE detalle_pedido ADD CONSTRAINT FK_A834F5694854653A FOREIGN KEY (pedido_id) REFERENCES pedido (id)');
        $this->addSql('ALTER TABLE pedido CHANGE fecha_pago fecha_pago DATE NOT NULL');
        $this->addSql('ALTER TABLE resena DROP FOREIGN KEY FK_50A7E40A7645698E');
        $this->addSql('ALTER TABLE resena ADD likes INT NOT NULL, ADD dislikes INT NOT NULL');
        $this->addSql('ALTER TABLE resena ADD CONSTRAINT FK_50A7E40A7645698E FOREIGN KEY (producto_id) REFERENCES producto (id)');
        $this->addSql('ALTER TABLE user CHANGE nombre nombre VARCHAR(255) NOT NULL, CHANGE apellido1 apellido1 VARCHAR(255) NOT NULL, CHANGE apellido2 apellido2 VARCHAR(255) NOT NULL, CHANGE telefono telefono INT NOT NULL, CHANGE is_verified is_verified TINYINT(1) NOT NULL, CHANGE activation_token activation_token VARCHAR(36) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE detalle_pedido DROP FOREIGN KEY FK_A834F5694854653A');
        $this->addSql('ALTER TABLE detalle_pedido ADD CONSTRAINT FK_A834F5694854653A FOREIGN KEY (pedido_id) REFERENCES pedido (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pedido CHANGE fecha_pago fecha_pago DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE resena DROP FOREIGN KEY FK_50A7E40A7645698E');
        $this->addSql('ALTER TABLE resena DROP likes, DROP dislikes');
        $this->addSql('ALTER TABLE resena ADD CONSTRAINT FK_50A7E40A7645698E FOREIGN KEY (producto_id) REFERENCES producto (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user CHANGE nombre nombre VARCHAR(255) DEFAULT NULL, CHANGE apellido1 apellido1 VARCHAR(255) DEFAULT NULL, CHANGE apellido2 apellido2 VARCHAR(255) DEFAULT NULL, CHANGE telefono telefono INT DEFAULT NULL, CHANGE is_verified is_verified TINYINT(1) DEFAULT NULL, CHANGE activation_token activation_token VARCHAR(255) DEFAULT NULL');
    }
}
