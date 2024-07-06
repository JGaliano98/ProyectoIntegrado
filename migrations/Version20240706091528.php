<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240706091528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE producto ADD categoria_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE producto ADD CONSTRAINT FK_A7BB06153397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)');
        $this->addSql('CREATE INDEX IDX_A7BB06153397707A ON producto (categoria_id)');
        $this->addSql('ALTER TABLE resena ADD producto_id INT DEFAULT NULL, ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE resena ADD CONSTRAINT FK_50A7E40A7645698E FOREIGN KEY (producto_id) REFERENCES producto (id)');
        $this->addSql('ALTER TABLE resena ADD CONSTRAINT FK_50A7E40AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_50A7E40A7645698E ON resena (producto_id)');
        $this->addSql('CREATE INDEX IDX_50A7E40AA76ED395 ON resena (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE resena DROP FOREIGN KEY FK_50A7E40A7645698E');
        $this->addSql('ALTER TABLE resena DROP FOREIGN KEY FK_50A7E40AA76ED395');
        $this->addSql('DROP INDEX IDX_50A7E40A7645698E ON resena');
        $this->addSql('DROP INDEX IDX_50A7E40AA76ED395 ON resena');
        $this->addSql('ALTER TABLE resena DROP producto_id, DROP user_id');
        $this->addSql('ALTER TABLE producto DROP FOREIGN KEY FK_A7BB06153397707A');
        $this->addSql('DROP INDEX IDX_A7BB06153397707A ON producto');
        $this->addSql('ALTER TABLE producto DROP categoria_id');
    }
}
