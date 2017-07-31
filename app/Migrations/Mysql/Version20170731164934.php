<?php

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table test_to_tag
 */
class Version20170731164934 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE test_to_tag (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                test_id INT UNSIGNED NOT NULL,
                tag_id INT UNSIGNED NOT NULL,
                FOREIGN KEY (test_id) REFERENCES tests (id)
                    ON UPDATE CASCADE ON DELETE CASCADE,
                FOREIGN KEY (tag_id) REFERENCES tags (id)
                    ON UPDATE CASCADE ON DELETE CASCADE
            )
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE test_to_tag");
    }
}
