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
        $db->setConnection(config('db.driver'), 'default', config('db.host'), config('db.name'), config('db.user'), config('db.pass'));

        // add the database to the query builder
        DB::setDatabase($db->default);

        // add the database to the app instance
        app()->register('db',$db->default);

        // kill the connection to prevent us from waiting until our logic completes.
        // without this; it could severely slow down the database server and website.
        // DB::close();
    }
}
