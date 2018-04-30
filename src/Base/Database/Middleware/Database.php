<?php namespace Base\Database\Middleware;

use Base\Routing\Middleware;
use Base\Database\Connect;
use Base\Support\Facades\DB;

class Database extends Middleware
{
    public function request()
    {
        // create the database instance
        $db = Connect::getInstance();
        $db->setConnection(config('db.driver'), config('db.name'), config('db.host'), config('db.name'), config('db.user'), config('db.pass'));

        // add the database to the query builder
        DB::setDatabase($db->{config('db.name')});

        // add the database to the app instance
        app()->register('db',$db);
    }
}
