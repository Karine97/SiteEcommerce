<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230705141518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice ADD utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_90651744FB88E14F ON invoice (utilisateur_id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993982454BA75 FOREIGN KEY (invoices_id) REFERENCES invoice (id)');
        $this->addSql('CREATE INDEX IDX_F52993982454BA75 ON `order` (invoices_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744FB88E14F');
        $this->addSql('DROP INDEX IDX_90651744FB88E14F ON invoice');
        $this->addSql('ALTER TABLE invoice DROP utilisateur_id');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993982454BA75');
        $this->addSql('DROP INDEX IDX_F52993982454BA75 ON `order`');
    }
}
