<?php

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table attempts
 */
class Version20170731165358 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE attempts (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                user_id INT UNSIGNED NOT NULL,
                test_id INT UNSIGNED NOT NULL,
                started TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                finished TIMESTAMP,
                status ENUM ('underway', 'failed', 'finished')
                    NOT NULL DEFAULT 'underway',
                FOREIGN KEY (user_id) REFERENCES users (id)
                    ON UPDATE CASCADE ON DELETE CASCADE,
                FOREIGN KEY (test_id) REFERENCES tests (id)
                    ON UPDATE CASCADE ON DELETE CASCADE
            );
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE attempts");
    }
}
