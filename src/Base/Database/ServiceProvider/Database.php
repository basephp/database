<?php namespace Base\Database\ServiceProvider;

use Base\Database\Manager as DatabaseManager;
use Base\ServiceProvider;

class Database extends ServiceProvider
{

    public function boot()
    {
        $db = DatabaseManager::getInstance();

        $db->addConnection('default',[
            'driver'   => config('db.driver','MySQLi'),
            'hostname' => config('db.host','127.0.0.1'),
            'port'     => config('db.port',3306),
            'database' => config('db.name'),
            'username' => config('db.user'),
            'password' => config('db.pass')
        ]);
    }

}
