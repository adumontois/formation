<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 10/10/2016
 * Time: 12:55
 */

namespace OCFram;


class MinLengthValidator extends Validator {
	/**
	 * @var $minLength int Entier strictement positif
	 */
	protected $minLength;
	
	/**
	 * MinLengthValidator constructor.
	 *
	 * @param string     $errorMessage Message d'erreur à afficher si la contrainte n'est pas vérifiée
	 * @param int|string $minLength    Longueur minimale du champ
	 */
	public function __construct( $errorMessage, $minLength ) {
		parent::__construct( $errorMessage );
		$this->setMinLength( $minLength );
	}
	
	/**
	 * Vérifie si la valeur passée en paramètre vérifie la propriété de longueur minimale.
	 *
	 * @param $value string
	 *
	 * @return bool
	 */
	public function isValid( $value ) {
		return strlen( $value ) >= $this->minLength;
	}
	
	/**
	 * Setter pour l'attribut minLength
	 *
	 * @param $value int|string Longueur minimale du champ
	 */
	public function setMinLength( $value ) {
		$minLength = (int)$value;
		if ( $minLength > 0 ) {
			$this->minLength = $minLength;
		}
		else {
			throw new \RuntimeException( 'MinLength must be a positive integer' );
		}
	}
}