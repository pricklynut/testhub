<?php

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table answers
 */
class Version20170731165519 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE answers (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                answer VARCHAR(255) NOT NULL,
                received TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                attempt_id INT UNSIGNED NOT NULL,
                question_id BIGINT UNSIGNED NOT NULL,
                FOREIGN KEY (attempt_id) REFERENCES attempts (id)
                    ON UPDATE CASCADE ON DELETE CASCADE,
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
        $this->addSql("DROP TABLE answers");
    }
}
