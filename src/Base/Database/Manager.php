<?php namespace Base\Database;


class Manager
{

    /**
    * Connect instance
    *
    * @var object
    */
    private static $instance;


    /**
	 * Maintains an array of the instances of all connections
	 * that have been created. Helps to keep track of all open
	 * connections for performance monitoring, logging, etc.
	 *
	 * @var array
	 */
	protected $connections = [];


    //--------------------------------------------------------------------


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


    //--------------------------------------------------------------------


    /**
	 * Parses the connection binds and returns an instance of
	 * the driver ready to go.
	 *
	 * @param array  $options
	 * @param string $handle
	 *
	 * @return object
	 */
	public function addConnection(string $handle, array $options = [])
	{
		$className = 'Base\\Database\\Driver\\' . $options['driver'] . '\\Connect';

		$this->connections[$handle] = new $className($options);

		return $this->connections[$handle];
	}


    //--------------------------------------------------------------------


    /**
	 * Parses the connection binds and returns an instance of
	 * the driver ready to go.
	 *
	 * @param string $handle
	 *
	 * @return object
	 */
	public function getConnection(string $handle)
	{
		return $this->connections[$handle] ?? false;
	}


    //--------------------------------------------------------------------

}
