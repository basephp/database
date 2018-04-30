<?php namespace Base\Database\MySQLi;


/**
 * Extends mysqli and adds the ability to easily apc cache queries
 */
class Connect extends \mysqli
{
	private $return = false;


    /**
    * This Function overwrites the mysql query function but should return the same objects
    */
    public function query($query = '', $resultmode = NULL)
    {
        if (preg_match('/^\s*(INSERT|UPDATE|SELECT)\s/i',$query))
        {
            if ($query != '')
            {
                $this->return = parent::query($query, $resultmode);
            }
        }

        return $this;
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
