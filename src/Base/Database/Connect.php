<?php namespace Base\Database;

use Base\Database\MySQLi\Connect AS MySQLi;

/**
* This class handles multiple Database connections and allows you to utilise lazy connections
*/
class Connect
{

    /**
    * Connect instance
    *
    * @var object
    */
    private static $instance;


    /**
    * Connection settings
    *
    * @var array
    */
    private $connection = [];


    /**
    * gets the instance via lazy initialization (created on first usage)
    *
    * @return self
    */
    public static function getInstance()
    {
        if (NULL === static::$instance)
        {
            static::$instance = new static();
        }

        return static::$instance;
    }


    /**
    * is not allowed to call from outside: private!
    *
    */
    private function __construct()
    {

    }


    /**
    * prevent the instance from being cloned
    *
    * @return void
    */
    private function __clone()
    {

    }


    /**
    * prevent from being unserialized
    *
    * @return void
    */
    private function __wakeup()
    {

    }


    /**
    * createConnection
    *
    */
    public function setConnection($driver = 'MySQLi', $handle = 'default', $host = '127.0.0.1', $database = '', $user = NULL, $pass = NULL)
    {
        $this->connection[$handle] = [
            'host' 	   => $host,
            'database' => $database,
            'user'     => $userName,
            'pass'     => $password
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
