<?php
namespace App\Config;

/**
 * 数据库配置
 * Class Db
 * @package App\Config
 */
class Db
{
    public static function init()
    {
        \CloverSwoole\Databases\DbConfig::getInterface() -> setGlobal() -> addConnectionItem([
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'database'  => 'chat',
            'username'  => 'root',
            'password'  => '123456',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);
    }
}