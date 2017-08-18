<?php

namespace App\Migrations\Postgres;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add column `status` to `tests` table
 */
class Version20170818083309 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $sql = "CREATE TYPE test_status_type AS ENUM ('draft', 'published')";
        $this->addSql($sql);

        $sql = "ALTER TABLE tests
                ADD COLUMN status test_status_type
                NOT NULL DEFAULT 'draft'";
        $this->addSql($sql);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $sql = "ALTER TABLE tests DROP COLUMN status";
        $this->addSql($sql);

        $sql = "DROP TYPE test_status_type";
        $this->addSql($sql);
    }
}
