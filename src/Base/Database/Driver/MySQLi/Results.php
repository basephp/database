<?php namespace Base\Database\Driver\MySQLi;

/**
 * Extends mysqli and adds the ability to easily apc cache queries
 */
class Results
{

    protected $return;

    /**
	 * Saves our connection settings.
	 *
	 * @param array $params
	 */
	public function __construct($return)
	{
		$this->return = $return;
        print_r($return);
	}



    /**
    * row
    */
    public function row()
    {
        if ($this->return)
        {
            return $this->return->fetch_object();
        }

        return false;
    }


    /**
    * results
    */
    public function results($returnType = 'object')
    {
        if ($this->return)
        {
            if ($returnType == 'object')
            {
                return $this->resultsObject($this->return);
            }

            if ($returnType == 'array')
            {
                return $this->resultsArray($this->return);
            }
        }

        return false;
    }


    /**
    * This function loops through the results and returns them as an array of objects
    */
    private function resultsArray($result)
    {
        $array = array();

        while($obj = $result->fetch_object() && $assoc = $result->fetch_assoc())
        {
            $array[$assoc] = $obj;
        }

        return $array;
    }


    /**
    * This function loops through the results and returns them as an array of objects
    */
    private function resultsObject($result)
    {
        $array = array();

        while($obj = $result->fetch_object())
        {
            $array[] = $obj;
        }

        return $array;
    }


 }
