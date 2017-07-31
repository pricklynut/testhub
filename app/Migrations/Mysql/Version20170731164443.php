<?php

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table users
 */
class Version20170731164443 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE users (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255),
                guest_key VARCHAR(255) NOT NULL,
                registered TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE users");
    }
}
