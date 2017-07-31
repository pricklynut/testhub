<?php

namespace App\Migrations\Postgres;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table tests
 */
class Version20170731160839 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TYPE show_answers_type AS ENUM ('yes', 'no');");

        $this->addSql("
            CREATE TABLE tests (
                id SERIAL PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                time_limit INT,
                author_id INT NOT NULL,
                show_answers show_answers_type NOT NULL DEFAULT 'no',
                created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (author_id) REFERENCES users (id)
                    ON UPDATE CASCADE ON DELETE CASCADE
            );
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE tests");
        $this->addSql("DROP TYPE show_answers_type");
    }
}
