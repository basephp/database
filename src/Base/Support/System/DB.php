<?php

namespace Base\Support\System;

use Base\Database\Database;
use Base\Support\System\BaseFacade;

class DB extends BaseFacade
{
    protected static function getClass()
    {
        return Database::class;
    }
}
