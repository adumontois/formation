<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 10:45
 */

namespace OCFram;


class PDOFactory
{
    static public function getMysqlConnexion()
    {
        $db = new \PDO('mysql:host=localhost;dbname=news', 'root', 'root');
        $db -> setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $db;
    }
}