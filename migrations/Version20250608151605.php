<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250608151605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE service_category (service_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_FF3A42FCED5CA9E6 (service_id), INDEX IDX_FF3A42FC12469DE2 (category_id), PRIMARY KEY(service_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service_category ADD CONSTRAINT FK_FF3A42FCED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_category ADD CONSTRAINT FK_FF3A42FC12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart DROP INDEX UNIQ_BA388B7A76ED395, ADD INDEX IDX_BA388B7A76ED395 (user_id)');
        $this->addSql('ALTER TABLE cart ADD product_id INT DEFAULT NULL, ADD service_id INT DEFAULT NULL, ADD quantity INT DEFAULT NULL, DROP product, DROP service, DROP quantity_product, DROP quantity_service');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B74584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_BA388B74584665A ON cart (product_id)');
        $this->addSql('CREATE INDEX IDX_BA388B7ED5CA9E6 ON cart (service_id)');
        $this->addSql('ALTER TABLE command DROP INDEX UNIQ_8ECAEAD4A76ED395, ADD INDEX IDX_8ECAEAD4A76ED395 (user_id)');
        $this->addSql('ALTER TABLE command ADD product_id INT DEFAULT NULL, ADD service_id INT DEFAULT NULL, ADD command_id INT NOT NULL, ADD quantity INT DEFAULT NULL, DROP product, DROP service, DROP quantity_product, DROP quantity_service');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD44584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD4ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_8ECAEAD44584665A ON command (product_id)');
        $this->addSql('CREATE INDEX IDX_8ECAEAD4ED5CA9E6 ON command (service_id)');
        $this->addSql('ALTER TABLE service DROP category');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL, CHANGE adress adress VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_category DROP FOREIGN KEY FK_FF3A42FCED5CA9E6');
        $this->addSql('ALTER TABLE service_category DROP FOREIGN KEY FK_FF3A42FC12469DE2');
        $this->addSql('DROP TABLE service_category');
        $this->addSql('ALTER TABLE cart DROP INDEX IDX_BA388B7A76ED395, ADD UNIQUE INDEX UNIQ_BA388B7A76ED395 (user_id)');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B74584665A');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7ED5CA9E6');
        $this->addSql('DROP INDEX IDX_BA388B74584665A ON cart');
        $this->addSql('DROP INDEX IDX_BA388B7ED5CA9E6 ON cart');
        $this->addSql('ALTER TABLE cart ADD product LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', ADD service LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', ADD quantity_product INT DEFAULT NULL, ADD quantity_service INT DEFAULT NULL, DROP product_id, DROP service_id, DROP quantity');
        $this->addSql('ALTER TABLE product_category DROP FOREIGN KEY FK_CDFC73564584665A');
        $this->addSql('ALTER TABLE product_category DROP FOREIGN KEY FK_CDFC735612469DE2');
        $this->addSql('ALTER TABLE command DROP INDEX IDX_8ECAEAD4A76ED395, ADD UNIQUE INDEX UNIQ_8ECAEAD4A76ED395 (user_id)');
        $this->addSql('ALTER TABLE command DROP FOREIGN KEY FK_8ECAEAD44584665A');
        $this->addSql('ALTER TABLE command DROP FOREIGN KEY FK_8ECAEAD4ED5CA9E6');
        $this->addSql('DROP INDEX IDX_8ECAEAD44584665A ON command');
        $this->addSql('DROP INDEX IDX_8ECAEAD4ED5CA9E6 ON command');
        $this->addSql('ALTER TABLE command ADD product LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', ADD service LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', ADD quantity_product INT DEFAULT NULL, ADD quantity_service INT DEFAULT NULL, DROP product_id, DROP service_id, DROP command_id, DROP quantity');
        $this->addSql('ALTER TABLE service ADD category LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user DROP roles, CHANGE adress adress VARCHAR(255) DEFAULT NULL');
    }
}
