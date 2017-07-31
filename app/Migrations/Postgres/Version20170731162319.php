<?php

namespace App\Migrations\Postgres;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table attempts
 */
class Version20170731162319 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TYPE attempt_status
            AS ENUM ('underway', 'failed', 'finished')
        ");

        $this->addSql("
            CREATE TABLE attempts (
                id SERIAL PRIMARY KEY,
                user_id INT NOT NULL,
                test_id INT NOT NULL,
                started TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                finished TIMESTAMP,
                status attempt_status NOT NULL DEFAULT 'underway',
                FOREIGN KEY (user_id) REFERENCES users (id)
                    ON UPDATE CASCADE ON DELETE CASCADE,
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
        $this->addSql("DROP TABLE attempts");
        $this->addSql("DROP TYPE attempt_status");
    }
}
