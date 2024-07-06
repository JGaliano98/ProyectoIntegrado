<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240706092524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE detalle_pedido ADD producto_id INT DEFAULT NULL, ADD pedido_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE detalle_pedido ADD CONSTRAINT FK_A834F5697645698E FOREIGN KEY (producto_id) REFERENCES producto (id)');
        $this->addSql('ALTER TABLE detalle_pedido ADD CONSTRAINT FK_A834F5694854653A FOREIGN KEY (pedido_id) REFERENCES pedido (id)');
        $this->addSql('CREATE INDEX IDX_A834F5697645698E ON detalle_pedido (producto_id)');
        $this->addSql('CREATE INDEX IDX_A834F5694854653A ON detalle_pedido (pedido_id)');
        $this->addSql('ALTER TABLE direccion ADD pedido_id INT DEFAULT NULL, ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE direccion ADD CONSTRAINT FK_F384BE954854653A FOREIGN KEY (pedido_id) REFERENCES pedido (id)');
        $this->addSql('ALTER TABLE direccion ADD CONSTRAINT FK_F384BE95A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F384BE954854653A ON direccion (pedido_id)');
        $this->addSql('CREATE INDEX IDX_F384BE95A76ED395 ON direccion (user_id)');
        $this->addSql('ALTER TABLE metodo_pago ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE metodo_pago ADD CONSTRAINT FK_8A0E8868A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8A0E8868A76ED395 ON metodo_pago (user_id)');
        $this->addSql('ALTER TABLE pedido ADD direccion_id INT DEFAULT NULL, ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pedido ADD CONSTRAINT FK_C4EC16CED0A7BD7 FOREIGN KEY (direccion_id) REFERENCES direccion (id)');
        $this->addSql('ALTER TABLE pedido ADD CONSTRAINT FK_C4EC16CEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C4EC16CED0A7BD7 ON pedido (direccion_id)');
        $this->addSql('CREATE INDEX IDX_C4EC16CEA76ED395 ON pedido (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE detalle_pedido DROP FOREIGN KEY FK_A834F5697645698E');
        $this->addSql('ALTER TABLE detalle_pedido DROP FOREIGN KEY FK_A834F5694854653A');
        $this->addSql('DROP INDEX IDX_A834F5697645698E ON detalle_pedido');
        $this->addSql('DROP INDEX IDX_A834F5694854653A ON detalle_pedido');
        $this->addSql('ALTER TABLE detalle_pedido DROP producto_id, DROP pedido_id');
        $this->addSql('ALTER TABLE pedido DROP FOREIGN KEY FK_C4EC16CED0A7BD7');
        $this->addSql('ALTER TABLE pedido DROP FOREIGN KEY FK_C4EC16CEA76ED395');
        $this->addSql('DROP INDEX UNIQ_C4EC16CED0A7BD7 ON pedido');
        $this->addSql('DROP INDEX IDX_C4EC16CEA76ED395 ON pedido');
        $this->addSql('ALTER TABLE pedido DROP direccion_id, DROP user_id');
        $this->addSql('ALTER TABLE metodo_pago DROP FOREIGN KEY FK_8A0E8868A76ED395');
        $this->addSql('DROP INDEX IDX_8A0E8868A76ED395 ON metodo_pago');
        $this->addSql('ALTER TABLE metodo_pago DROP user_id');
        $this->addSql('ALTER TABLE direccion DROP FOREIGN KEY FK_F384BE954854653A');
        $this->addSql('ALTER TABLE direccion DROP FOREIGN KEY FK_F384BE95A76ED395');
        $this->addSql('DROP INDEX UNIQ_F384BE954854653A ON direccion');
        $this->addSql('DROP INDEX IDX_F384BE95A76ED395 ON direccion');
        $this->addSql('ALTER TABLE direccion DROP pedido_id, DROP user_id');
    }
}
