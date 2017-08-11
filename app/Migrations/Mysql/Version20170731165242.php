<?php

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table variants
 */
class Version20170731165242 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE variants (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                answer VARCHAR(255) NOT NULL,
                is_correct ENUM ('yes', 'no') NOT NULL,
                question_id BIGINT UNSIGNED NOT NULL,
                FOREIGN KEY (question_id) REFERENCES questions (id)
                    ON UPDATE CASCADE ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE variants");
    }
}
