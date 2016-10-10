<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 10/10/2016
 * Time: 15:59
 */

namespace OCFram;

/**
 * Class NotExistsValidator
 *
 * Vérifie qu'un élément n'existe pas en DB
 *
 * @package OCFram
 */
class NotExistsValidator extends Validator {
	/**
	 * @var Manager $Manager
	 */
	protected $Manager;
	/**
	 * @var string $method Méthode à invoquer sur le manager
	 */
	protected $method;
	
	
	public function __construct( $errorMessage, $Manager, $method ) {
		parent::__construct( $errorMessage );
		$this->Manager = $Manager;
		$this->method = $method;
	}
	
	/**
	 * Appelle la méthode $method sur le Manager pour savoir si un objet n'existe pas.
	 * La méthode $method devrait renvoyer un booléen.
	 *
	 * @param string $value
	 *
	 * @return bool
	 */
	public function isValid( $value ) {
		$method = $this->method;
		return !((bool) $this->Manager->$method($value));
	}
}
	