<?php

namespace Base\Database;

use Base\Database\Query\Builder as QueryBuilder;
use Base\Database\Manager as DatabaseManager;
use Base\Support\Collection;

/**
* Database
*
*/
class Database
{

    /**
     * The current connection
     *
     */
    protected $connection = false;


    //--------------------------------------------------------------------


    /**
     * Get a connection from the database manager
     *
     * @param string $handle
     */
    public function connect($handle)
    {
        $db = DatabaseManager::getInstance();
        $this->connection = $db->getConnection($handle);

        if ($this->connection == false) throw new \Exception('Database connection: '.$handle.' not found.');

        return $this;
    }


    //--------------------------------------------------------------------


    /**
     * Access the Query Builder
     *
     * @param string $table
     */
    public function table($table)
    {
        if ($this->connection == false) $this->connect('default');

        return (new QueryBuilder($this->connection))->from($table);
    }


    //--------------------------------------------------------------------


    /**
     * Run a raw query
     *
     * @param string $sql
     */
    public function query($sql)
    {
        if ($this->connection == false) $this->connect('default');

        if (!$this->isWrite($sql))
        {
            return new Collection($this->connection->query($sql)->results());
        }

        if ($this->isInsert($sql))
        {
            $r = $this->connection->query($sql);

            if ($r)
            {
                return $this->connection->insertId();
            }
        }
        else
        {
            return $this->connection->query($sql);
        }

        return false;
    }


    //--------------------------------------------------------------------


    /**
    * Checks whether a SQL statement is a "WRITE" query.
    *
    * @param string $str
    * @return bool
    */
    public function isWrite($sql)
    {
        return (bool) preg_match(
            '/^\s*"?(SET|INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|TRUNCATE|LOAD|COPY|ALTER|RENAME|GRANT|REVOKE|LOCK|UNLOCK|REINDEX)\s/i', $sql);
    }


    //--------------------------------------------------------------------


    /**
    * Checks whether a SQL statement is a "WRITE" query.
    *
    * @param string $str
    * @return bool
    */
    public function isInsert($sql)
    {
        return (bool) preg_match(
            '/^\s*"?(INSERT)\s/i', $sql);
    }


    //--------------------------------------------------------------------


}
