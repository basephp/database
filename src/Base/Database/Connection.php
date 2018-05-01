<?php namespace Base\Database;

use Base\Database\Query\Builder as QueryBuilder;

/**
* Database Builder
*
*/
class Connection
{

    protected $db;


    /**
    * Setting the database object
    *
    */
    public function setDatabase($db)
    {
        $this->db = $db;
    }


    /**
     * Run a query using the query builder
     *
     * @param  string $table
     * @return \Base\Database\Query\Builder
     */
    public function table($table)
    {
        return (new QueryBuilder($this->db))->from($table);
    }

}
