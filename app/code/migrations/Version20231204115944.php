<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231204115944 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'init';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<EOL
        create table if not exists Categories (
            id bigint unsigned not null auto_increment,
            title varchar(12) not null,
            eId bigint unsigned null,
            constraint pk_categories primary key (id),
            constraint chk_categories check (
                length(title) >= 3 and length(title) <= 12
            )
        );
        
        create table if not exists Products (
            id bigint unsigned not null auto_increment,
            title varchar(12) not null,
            price decimal(5, 2) not null,
            eId bigint unsigned null,
            constraint pk_categories primary key (id),
            constraint chk_categories check (
                length(title) >= 3 and length(title) <= 12
                and price >= 0 and price <= 200
            )
        );
        
        create table if not exists CategoryProduct (
            productId bigint unsigned not null,
            categoryEId bigint unsigned not null,
            constraint fk_category_product_product_id
                foreign key (productId) references Products(id)
                on delete cascade
        );
        EOL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<EOL
        drop table if exists CategoryProduct;
        drop table if exists Products;
        drop table if exists Categories;
        EOL);
    }
}
