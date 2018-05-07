<?php

namespace Base\Database\Driver\MySQLi;

class Results extends \Base\Database\Results
{

    /**
    * ...
    */
    public function row()
    {
        if ($this->result)
        {
            return $this->result->fetch_object();
        }

        return false;
    }


    //--------------------------------------------------------------------


    /**
    * ...
    *
    */
    public function toArray()
    {
        $a = [];

        while($obj = $this->result->fetch_object() && $assoc = $this->result->fetch_assoc())
        {
            $a[$assoc] = $obj;
        }

        return $a;
    }


    //--------------------------------------------------------------------


    /**
    * ...
    *
    */
    public function toObject()
    {
        $a = [];

        while($obj = $this->result->fetch_object())
        {
            $a[] = $obj;
        }

        return $a;
    }


    //--------------------------------------------------------------------


 }
