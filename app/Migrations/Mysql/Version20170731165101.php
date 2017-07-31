<?php

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table questions
 */
class Version20170731165101 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE questions (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                question TEXT NOT NULL,
                price TINYINT UNSIGNED NOT NULL DEFAULT 1,
                test_id INT UNSIGNED NOT NULL,
                type ENUM (
                    'string_typin',
                    'number_typin',
                    'single_variant',
                    'multiple_variants'
                ) NOT NULL,
                serial_number TINYINT UNSIGNED NOT NULL,
                FOREIGN KEY (test_id) REFERENCES tests (id)
                    ON UPDATE CASCADE ON DELETE CASCADE
            )
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE questions");
    }
}
