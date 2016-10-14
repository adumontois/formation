<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 10:45
 */

namespace OCFram;

/**
 * Class PDOFactory
 * Construit un DAO PDO.
 *
 * @package OCFram
 */
class PDOFactory {
	/**
	 * Récupère un DAO de connexion à la base $dbname pour le SGBD Mysql.
	 *
	 * @return \PDO
	 */
	static public function getPDO( ) {
		$db = new \PDO( 'mysql:host=localhost;dbname=news', 'root', 'root' );
		$db->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
		
		return $db;
	}
}