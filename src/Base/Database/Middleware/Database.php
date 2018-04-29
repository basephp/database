<?php namespace Base\Database\Middleware\Database;

use Base\Routing\Middleware;
use Base\Database\Connect;

class Database extends Middleware
{
    public function request()
    {
        $db = Connect::getInstance();
        $db->createConnection(config('db.name'),config('db.host'),config('db.name'),config('db.user'),config('db.pass'));

        app()->register('db',$db);
    }
}
