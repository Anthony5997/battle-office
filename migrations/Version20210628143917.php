<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210628143917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD address_billing_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398439FD419 FOREIGN KEY (address_billing_id) REFERENCES address_billing (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F5299398439FD419 ON `order` (address_billing_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398439FD419');
        $this->addSql('DROP INDEX UNIQ_F5299398439FD419 ON `order`');
        $this->addSql('ALTER TABLE `order` DROP address_billing_id');
    }
}
