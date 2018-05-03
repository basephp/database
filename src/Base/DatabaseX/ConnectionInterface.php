<?php namespace Base\Database;

interface ConnectionInterface
{

    public function query($table);

    public function results($returnType);

    public function row();

}
