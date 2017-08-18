<?php

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add column `status` to `tests` table
 */
class Version20170818084124 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $sql = "ALTER TABLE tests
                ADD COLUMN status ENUM ('draft', 'published')
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
    }
}
