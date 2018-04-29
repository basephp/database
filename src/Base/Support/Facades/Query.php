<?php

namespace Base\Support\Facades;

class Query extends \Base\Support\Facades\Facade
{
    protected static function getClass()
    {
        return \Base\Database\Query::class;
    }
}
