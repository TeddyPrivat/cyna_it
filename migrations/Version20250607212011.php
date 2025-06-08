<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250607212011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart DROP INDEX IDX_BA388B7A76ED395, ADD UNIQUE INDEX UNIQ_BA388B7A76ED395 (user_id)');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B74584665A');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7ED5CA9E6');
        $this->addSql('DROP INDEX IDX_BA388B74584665A ON cart');
        $this->addSql('DROP INDEX IDX_BA388B7ED5CA9E6 ON cart');
        $this->addSql('ALTER TABLE cart ADD product LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', ADD service LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', ADD quantity_product INT DEFAULT NULL, ADD quantity_service INT DEFAULT NULL, DROP product_id, DROP service_id, DROP quantity');
        $this->addSql('ALTER TABLE command DROP FOREIGN KEY FK_8ECAEAD44584665A');
        $this->addSql('ALTER TABLE command DROP FOREIGN KEY FK_8ECAEAD4ED5CA9E6');
        $this->addSql('DROP INDEX IDX_8ECAEAD44584665A ON command');
        $this->addSql('DROP INDEX IDX_8ECAEAD4ED5CA9E6 ON command');
        $this->addSql('ALTER TABLE command ADD product LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', ADD service LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', ADD quantity_product INT DEFAULT NULL, ADD quantity_service INT DEFAULT NULL, DROP product_id, DROP service_id, DROP quantity, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE product DROP categories');
        $this->addSql('ALTER TABLE product_category MODIFY product_id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON product_category');
        $this->addSql('ALTER TABLE product_category CHANGE product_id product_id INT NOT NULL');
        $this->addSql('CREATE INDEX IDX_CDFC73564584665A ON product_category (product_id)');
        $this->addSql('CREATE INDEX IDX_CDFC735612469DE2 ON product_category (category_id)');
        $this->addSql('ALTER TABLE product_category ADD PRIMARY KEY (product_id, category_id)');
        $this->addSql('ALTER TABLE user DROP role, CHANGE adress adress VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart DROP INDEX UNIQ_BA388B7A76ED395, ADD INDEX IDX_BA388B7A76ED395 (user_id)');
        $this->addSql('ALTER TABLE cart ADD product_id INT DEFAULT NULL, ADD service_id INT DEFAULT NULL, ADD quantity INT DEFAULT NULL, DROP product, DROP service, DROP quantity_product, DROP quantity_service');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B74584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_BA388B74584665A ON cart (product_id)');
        $this->addSql('CREATE INDEX IDX_BA388B7ED5CA9E6 ON cart (service_id)');
        $this->addSql('ALTER TABLE product_category DROP FOREIGN KEY FK_CDFC73564584665A');
        $this->addSql('ALTER TABLE product_category DROP FOREIGN KEY FK_CDFC735612469DE2');
        $this->addSql('DROP INDEX IDX_CDFC73564584665A ON product_category');
        $this->addSql('DROP INDEX IDX_CDFC735612469DE2 ON product_category');
        $this->addSql('DROP INDEX `PRIMARY` ON product_category');
        $this->addSql('ALTER TABLE product_category CHANGE product_id product_id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE product_category ADD PRIMARY KEY (product_id)');
        $this->addSql('ALTER TABLE command ADD product_id INT DEFAULT NULL, ADD service_id INT DEFAULT NULL, ADD quantity INT DEFAULT NULL, DROP product, DROP service, DROP quantity_product, DROP quantity_service, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD44584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD4ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8ECAEAD44584665A ON command (product_id)');
        $this->addSql('CREATE INDEX IDX_8ECAEAD4ED5CA9E6 ON command (service_id)');
        $this->addSql('ALTER TABLE product ADD categories LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE user ADD role JSON NOT NULL, CHANGE adress adress VARCHAR(255) NOT NULL');
    }
}
