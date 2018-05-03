<?php namespace Base\Database\Driver\MySQLi;

use Base\Database\Driver\MySQLi\Results;

/**
 * Driver Connect Class
 */
class Connect extends \Base\Database\Connection
{

    /**
	 * MySQLi object
	 *
	 * Has to be preserved without being assigned to $conn_id.
	 *
	 * @var \MySQLi
	 */
	public $mysqli;


	//--------------------------------------------------------------------


	/**
	 * Connect to the database.
	 *
	 *
	 * @return mixed
	 */
	public function connect()
	{
        $this->mysqli = mysqli_init();

        mysqli_report(MYSQLI_REPORT_ALL & ~MYSQLI_REPORT_INDEX);

        $this->mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);

        if ($this->mysqli->real_connect($this->hostname, $this->username, $this->password, $this->database,  $this->port))
		{
            return $this->mysqli;
        }

        return false;
    }


    //--------------------------------------------------------------------


    /**
	 * Select a specific database table to use.
	 *
	 * @param string $databaseName
	 *
	 * @return mixed
	 */
	public function setDatabase(string $databaseName)
	{
		if ($databaseName === '')
		{
			$databaseName = $this->database;
		}

		if (!$this->connection)
		{
			$this->initialize();
		}

		if ($this->connection->select_db($databaseName))
		{
			$this->database = $databaseName;

			return true;
		}

		return false;
	}


    //--------------------------------------------------------------------


    /**
	 * ...
	 *
	 * @return mixed
	 */
	public function query(string $sql)
	{
        if (!$this->connection)
		{
            $this->initialize();
        }

        return new Results($this->connection, $this->connection->query($sql, null));
	}


    //--------------------------------------------------------------------


    /**
	 * ...
	 *
	 * @return mixed
	 */
	public function escape(string $str)
	{
        if (!$this->connection)
		{
            $this->initialize();
        }

        return $this->connection->real_escape_string($str);
	}


    //--------------------------------------------------------------------


	/**
	 * Insert ID
	 *
	 * @return	int
	 */
	public function insertId()
	{
		return $this->connection->insert_id;
	}


    //--------------------------------------------------------------------


    /**
	 * Close the database connection.
	 */
	protected function closeConnection()
	{
		$this->connection->close();
	}


    //--------------------------------------------------------------------

	/**
	 * Returns the last error code and message.
	 *
	 * Must return an array with keys 'code' and 'message':
	 *
	 *  return ['code' => null, 'message' => null);
	 *
	 * @return	array
	 */
	public function error()
	{
		if ( ! empty($this->mysqli->connect_errno))
		{
			return [
				'code'		 => $this->mysqli->connect_errno,
				'message'	 => $this->mysqli->connect_error
			];
		}

		return ['code' => $this->connection->errno, 'message' => $this->connection->error];
	}



}
