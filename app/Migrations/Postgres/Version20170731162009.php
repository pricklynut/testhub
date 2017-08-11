<?php

namespace App\Migrations\Postgres;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table variants
 */
class Version20170731162009 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TYPE variant_correct AS ENUM ('yes', 'no')");

        $this->addSql("
            CREATE TABLE variants (
                id BIGSERIAL PRIMARY KEY,
                answer VARCHAR(255) NOT NULL,
                is_correct variant_correct NOT NULL,
                question_id BIGINT NOT NULL,
                FOREIGN KEY (question_id) REFERENCES questions (id)
                    ON UPDATE CASCADE ON DELETE CASCADE
            )
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE variants");
        $this->addSql("DROP TYPE variant_correct");
    }
}
