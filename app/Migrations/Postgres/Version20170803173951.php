<?php

namespace App\Migrations\Postgres;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add field fulltext_search, add index and trigger
 */
class Version20170803173951 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $sql = "ALTER TABLE tests ADD COLUMN fulltext_search tsvector";
        $this->addSql($sql);

        $sql = "CREATE INDEX ix_fulltext_search ON tests USING gin(fulltext_search)";
        $this->addSql($sql);

        $sql = "CREATE TRIGGER tests_fulltext_update BEFORE INSERT OR UPDATE
                ON tests FOR EACH ROW EXECUTE PROCEDURE
                tsvector_update_trigger(fulltext_search, 'pg_catalog.russian', title, description);";
        $this->addSql($sql);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $sql = "DROP TRIGGER tests_fulltext_update ON tests";
        $this->addSql($sql);

        $sql = "DROP INDEX ix_fulltext_search";
        $this->addSql($sql);

        $sql = "ALTER TABLE tests DROP COLUMN fulltext_search";
        $this->addSql($sql);
    }
}
