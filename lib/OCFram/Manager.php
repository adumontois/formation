<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 10:53
 */

namespace OCFram;

/**
 * Class Manager
 * Classe représentant un manager type pour un module.
 *
 * @package OCFram
 */
abstract class Manager {
	/**
	 * @var $dao \PDO Data Access Object for DB queries
	 */
	protected $dao;
	
	/**
	 * Manager constructor.
	 *
	 * @param $dao mixed
	 */
	public function __construct( $dao ) {
		$this->dao = $dao;
	}
	
	/**
	 * Sauvegarde (insert ou update) l'entité passée en paramètre.
	 *
	 * @param Entity $object L'objet à sauvegarder
	 */
	abstract public function save(Entity $object);
}