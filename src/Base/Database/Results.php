<?php namespace Base\Database;

/**
 * Extends mysqli and adds the ability to easily apc cache queries
 */
abstract class Results
{

    protected $connection;

    protected $query;


    //--------------------------------------------------------------------


    /**
	 * Saves the settings for connection and result object
	 *
	 * @param array $params
	 */
	public function __construct($connection, $queryResult)
	{
		$this->connection = $connection;

        $this->result = $queryResult;
	}


    //--------------------------------------------------------------------


    /**
    * ...
    *
    */
    public function results($returnType = 'object')
    {
        if ($this->result)
        {
            if ($returnType == 'object')
            {
                return $this->toObject();
            }

            if ($returnType == 'array')
            {
                return $this->toArray();
            }
        }

        return false;
    }


    //--------------------------------------------------------------------


    /**
    * ...
    *
    */
    abstract function row();


    //--------------------------------------------------------------------


    /**
    * ...
    *
    */
    abstract function toArray();


    //--------------------------------------------------------------------


    /**
    * ...
    *
    */
    abstract function toObject();


    //--------------------------------------------------------------------


 }
