<?php namespace Base\Database\Middleware;

use Base\Routing\Middleware;
use Base\Database\Connect;

class Database extends Middleware
{
    public function request()
    {
        $db = Connect::getInstance();
        $db->setConnection(config('db.driver'), config('db.name'), config('db.host'), config('db.name'), config('db.user'), config('db.pass'));

        app()->register('db',$db);
    }
}
