<?php namespace Base\Database;

use Base\Database\MySQLi\Connect AS MySQLi;

/**
* This class handles multiple Database connections and allows you to utilise lazy connections
*/
class Connect
{

    /**
    * Connection settings
    *
    * @var array
    */
    private $connection = [];


    /**
    * createConnection
    *
    */
    public function setConnection($driver = 'MySQLi', $handle = 'default', $host = '127.0.0.1', $database = '', $user = NULL, $pass = NULL)
    {
        $this->connection[$handle] = [
            'driver'   => $driver,
            'host' 	   => $host,
            'database' => $database,
            'user'     => $user,
            'pass'     => $pass
        ];

        return $this;
    }


    /**
    * getDatabaseConnection
    *
    */
    public function getDatabaseConnection($handle)
    {
        if(isset($this->connection[$handle]))
        {
            if(isset($this->connection[$handle]['object']))
            {
                return $this->connection[$handle]['object'];
            }
            else
            {
                $connectObject = new MySQLi($this->connection[$handle]['host'], $this->connection[$handle]['user'], $this->connection[$handle]['pass'], $this->connection[$handle]['database']);

                // $object->options(MYSQLI_OPT_CONNECT_TIMEOUT,5);
                //This is a non OO method - only used for pre 5.2.9 compat.
                if(!mysqli_connect_errno())
                {
                    $this->connection[$handle]['object'] = $connectObject;

                    return $connectObject;
                }
                else
                {
                    throw new Exception('Database: Mysqli Connect Error: '.mysqli_connect_error());
                }
            }
        }

        throw new Exception('Database: Could not find a connection with the handle ' . $handle);
    }


    /**
    * __get
    *
    */
    public function __get($handle)
    {
        try
        {
            return $this->getDatabaseConnection($handle);
        }
        catch (Exception $e)
        {
            die($e->getMessage());
        }
    }
}
