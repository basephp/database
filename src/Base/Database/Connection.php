<?php namespace Base\Database;


/**
* Connection Class
*
* Do not call this class directly. This class is a parent of Driver Classes
*
*/
abstract class Connection
{
    /**
	 * Database port
	 *
	 * @var    int
	 */
	protected $port = 3306;


	/**
	 * Hostname
	 *
	 * @var    string
	 */
	protected $hostname;


	/**
	 * Username
	 *
	 * @var    string
	 */
	protected $username;


	/**
	 * Password
	 *
	 * @var    string
	 */
	protected $password;


	/**
	 * Database name
	 *
	 * @var    string
	 */
	protected $database;


	/**
	 * Database driver
	 *
	 * @var    string
	 */
	protected $driver = 'MySQLi';


    //--------------------------------------------------------------------


    /**
	 * Connection
	 *
	 * @var    object|resource
	 */
	public $connection = false;


    //--------------------------------------------------------------------


	/**
	 * Saves our connection settings.
	 *
	 * @param array $params
	 */
	public function __construct(array $params)
	{
		foreach ($params as $key => $value)
		{
			$this->$key = $value;
		}
	}


    //--------------------------------------------------------------------


    /**
	 * Initializes the database connection/settings.
	 *
	 * @return mixed
	 */
	public function initialize()
	{
        // check if the conncetion already exist.
        if ($this->connection)
        {
            return;
        }

        // Connect to the database and set the connection ID
        $this->connection = $this->connect();
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
	 * ...
	 *
	 */
	abstract function query($sql);


    //--------------------------------------------------------------------


    /**
	 * ...
	 *
	 */
	abstract function escape($str);


    //--------------------------------------------------------------------


}
