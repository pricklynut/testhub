<?php

namespace App\Migrations\Postgres;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table test_to_tag
 */
class Version20170731161412 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE test_to_tag (
                id SERIAL PRIMARY KEY,
                test_id INT NOT NULL,
                tag_id INT NOT NULL,
                FOREIGN KEY (test_id) REFERENCES tests (id)
                    ON UPDATE CASCADE ON DELETE CASCADE,
                FOREIGN KEY (tag_id) REFERENCES tags (id)
                    ON UPDATE CASCADE ON DELETE CASCADE
            );
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
