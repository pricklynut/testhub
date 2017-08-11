<?php

namespace App\Migrations\Postgres;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table questions
 */
class Version20170731161558 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TYPE question_type
            AS ENUM (
                'string_typein',
                'number_typein',
                'single_variant',
                'multiple_variants'
            );
        ");

        $this->addSql("
            CREATE TABLE questions (
                id BIGSERIAL PRIMARY KEY,
                question TEXT NOT NULL,
                price SMALLINT NOT NULL DEFAULT 1,
                test_id INT NOT NULL,
                type question_type NOT NULL,
                \"precision\" SMALLINT NOT NULL DEFAULT 0,
                serial_number SMALLINT NOT NULL,
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
        $this->addSql("DROP TABLE questions");
        $this->addSql("DROP TYPE question_type");
    }
}
