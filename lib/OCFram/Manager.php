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
	 * @var $dao mixed Data Access Object for DB queries
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
	 * Insère l'objet paramètre en DB.
	 *
	 * @param Entity $object
	 */
	abstract protected function add(Entity $object);
	
	/**
	 * Modifie l'objet paramètre en DB.
	 *
	 * @param Entity $object
	 */
	abstract protected function modify(Entity $object);
	
	/**
	 * Insère ou met à jour la news en DB selon qu'il existe déjà ou non en base.
	 *
	 * @param Entity $object
	 *
	 */
	final public function save( Entity $object ) {
		
		if ( $object->isValid() ) {
			if ( $object->object_new() ) {
				
				$this->add( $object );
			}
			else {
				$this->modify( $object );
			}
		}
	}
}