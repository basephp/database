<?php namespace Base\Database\Builder;


/**
* Database Builder
*
*/
class Database
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
    * Calls into the database obbject
    *
    */
    public function __call($method, $parameters)
    {
        return $this->db->{$method}(...$parameters);
    }

}
