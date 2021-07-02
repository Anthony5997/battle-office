<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210628140243 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD order_product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398F65E9B0F FOREIGN KEY (order_product_id) REFERENCES product (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F5299398F65E9B0F ON `order` (order_product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398F65E9B0F');
        $this->addSql('DROP INDEX UNIQ_F5299398F65E9B0F ON `order`');
        $this->addSql('ALTER TABLE `order` DROP order_product_id');
    }
}
