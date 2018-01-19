<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161214161424 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, meta_description VARCHAR(160) NOT NULL, meta_keywords VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_64C19C1989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_product (category_source INT NOT NULL, category_target INT NOT NULL, INDEX IDX_149244D35062B508 (category_source), INDEX IDX_149244D34987E587 (category_target), PRIMARY KEY(category_source, category_target)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE county (id INT AUTO_INCREMENT NOT NULL, delivery_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, INDEX IDX_58E2FF2512136921 (delivery_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE delivery (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, amount VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discount (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, discount_percentage NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dropshipper (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, email VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_62F6CB95E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, discount_id INT DEFAULT NULL, status_id INT DEFAULT NULL, created_at DATE NOT NULL, order_description LONGTEXT NOT NULL, email VARCHAR(255) NOT NULL, delivery_amount NUMERIC(10, 2) NOT NULL, order_amount NUMERIC(10, 2) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, invoice_address LONGTEXT NOT NULL, delivery_address LONGTEXT NOT NULL, comment LONGTEXT DEFAULT NULL, token VARCHAR(200) NOT NULL, tracking_number VARCHAR(255) DEFAULT NULL, INDEX IDX_F5299398A76ED395 (user_id), INDEX IDX_F52993984C7C611F (discount_id), INDEX IDX_F52993986BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, dropshipper_id INT DEFAULT NULL, sku VARCHAR(255) NOT NULL, name VARCHAR(100) NOT NULL, meta_description VARCHAR(255) DEFAULT NULL, meta_keywords VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, slug VARCHAR(255) NOT NULL, display TINYINT(1) DEFAULT \'1\' NOT NULL, image VARCHAR(255) NOT NULL, short_description VARCHAR(200) NOT NULL, description LONGTEXT NOT NULL, dimension VARCHAR(255) NOT NULL, weight INT NOT NULL, featured TINYINT(1) DEFAULT \'0\', related_products LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_D34A04AD989D9B62 (slug), INDEX IDX_D34A04ADE149E6E5 (dropshipper_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, delivery_id INT DEFAULT NULL, created_at DATE NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, invoice_address LONGTEXT NOT NULL, delivery_address LONGTEXT NOT NULL, token VARCHAR(255) DEFAULT NULL, newsletter TINYINT(1) DEFAULT \'1\', discount NUMERIC(10, 2) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D64912136921 (delivery_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE whishlist_products (user_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_5494C4DAA76ED395 (user_id), INDEX IDX_5494C4DA4584665A (product_id), PRIMARY KEY(user_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_product ADD CONSTRAINT FK_149244D35062B508 FOREIGN KEY (category_source) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_product ADD CONSTRAINT FK_149244D34987E587 FOREIGN KEY (category_target) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE county ADD CONSTRAINT FK_58E2FF2512136921 FOREIGN KEY (delivery_id) REFERENCES delivery (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993984C7C611F FOREIGN KEY (discount_id) REFERENCES discount (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993986BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADE149E6E5 FOREIGN KEY (dropshipper_id) REFERENCES dropshipper (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64912136921 FOREIGN KEY (delivery_id) REFERENCES delivery (id)');
        $this->addSql('ALTER TABLE whishlist_products ADD CONSTRAINT FK_5494C4DAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE whishlist_products ADD CONSTRAINT FK_5494C4DA4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category_product DROP FOREIGN KEY FK_149244D35062B508');
        $this->addSql('ALTER TABLE category_product DROP FOREIGN KEY FK_149244D34987E587');
        $this->addSql('ALTER TABLE county DROP FOREIGN KEY FK_58E2FF2512136921');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64912136921');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993984C7C611F');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADE149E6E5');
        $this->addSql('ALTER TABLE whishlist_products DROP FOREIGN KEY FK_5494C4DA4584665A');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993986BF700BD');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE whishlist_products DROP FOREIGN KEY FK_5494C4DAA76ED395');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_product');
        $this->addSql('DROP TABLE county');
        $this->addSql('DROP TABLE delivery');
        $this->addSql('DROP TABLE discount');
        $this->addSql('DROP TABLE dropshipper');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE whishlist_products');
    }
}
