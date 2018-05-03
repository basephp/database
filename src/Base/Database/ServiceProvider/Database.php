<?php namespace Base\Database\ServiceProvider;

use Base\Database\Manager as DatabaseManager;

class Database
{

    public function __construct($app)
    {
        $db = DatabaseManager::getInstance();

        $db->addConnection('default',[
            'driver'   => config('db.driver'),
            'hostname' => config('db.host'),
            'database' => config('db.name'),
            'username' => config('db.user'),
            'password' => config('db.pass')
        ]);
    }

}
