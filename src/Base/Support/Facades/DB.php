<?php

namespace Base\Support\Facades;

class DB extends \Base\Support\Facades\Facade
{
    protected static function getClass()
    {
        return \Base\Database\Connect::class;
    }
}
