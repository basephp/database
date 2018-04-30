<?php namespace Base\Database\Middleware;

use Base\Routing\Middleware;
use Base\Database\Connect;
use Base\Database\Query;

class Database extends Middleware
{
    public function request()
    {
        // $db = Connect::getInstance();
        DB::setConnection(config('db.driver'), config('db.name'), config('db.host'), config('db.name'), config('db.user'), config('db.pass'));
        // DB::queryBuilder( (new Query()) );
        // app()->register('db',$db);
    }
}
