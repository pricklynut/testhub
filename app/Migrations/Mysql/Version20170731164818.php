<?php

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table tests
 */
class Version20170731164818 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE tests (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                time_limit INT UNSIGNED,
                author_id INT UNSIGNED NOT NULL,
                show_answers ENUM ('yes', 'no') NOT NULL DEFAULT 'no',
                created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (author_id) REFERENCES users (id)
                    ON UPDATE CASCADE ON DELETE CASCADE
            )
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE tests");
    }
}
