<?php namespace Base\Database;

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
     *...
     *
     */
    protected $connection = false;


    //--------------------------------------------------------------------


    /**
     *...
     *
     * @param  string $handle
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
     * ...
     *
     * @param  string $table
     */
    public function table($table)
    {
        if ($this->connection == false) $this->connect('default');

        return (new QueryBuilder($this->connection))->from($table);
    }


    //--------------------------------------------------------------------


    /**
     * ...
     *
     * @param  string $table
     */
    public function query($sql)
    {
        if ($this->connection == false) $this->connect('default');

        if (!$this->connection->isWrite($sql))
        {
            return new Collection($this->connection->query($sql)->results());
        }

        return $this->connection->query($sql);
    }


    //--------------------------------------------------------------------


}
