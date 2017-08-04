<?php

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add fulltext index on tests (title, description)
 */
class Version20170804095313 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $sql = "CREATE FULLTEXT INDEX ix_fulltext ON tests (title, description)";
        $this->addSql($sql);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $sql = "ALTER TABLE tests DROP INDEX ix_fulltext";
        $this->addSql($sql);
    }
}
