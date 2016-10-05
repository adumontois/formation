<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 15:13
 */

namespace OCFram;

/**
 * Class MaxLengthValidator
 * Validateur vérifiant la contrainte de longueur maximale d'un champ.
 *
 * @package OCFram
 */
class MaxLengthValidator extends Validator {
	/**
	 * @var $maxLength int Entier strictement positif
	 */
	protected $maxLength;
	
	/**
	 * MaxLengthValidator constructor.
	 *
	 * @param string     $errorMessage Message d'erreur à afficher si la contrainte n'est pas vérifiée
	 * @param int|string $maxLength    Longueur maximale du champ
	 */
	public function __construct( $errorMessage, $maxLength ) {
		parent::__construct( $errorMessage );
		$this->setMaxLength( $maxLength );
	}
	
	/**
	 * Vérifie si la valeur passée en paramètre vérifie la propriété de longueur maximale.
	 *
	 * @param $value string
	 *
	 * @return bool
	 */
	public function isValid( $value ) {
		return strlen( $value ) <= $this->maxLength;
	}
	
	/**
	 * Setter pour l'attribut maxLength
	 *
	 * @param $value int|string Longueur maximale du champ
	 */
	public function setMaxLength( $value ) {
		$maxLength = (int)$value;
		if ( $maxLength > 0 ) {
			$this->maxLength = $maxLength;
		}
		else {
			throw new \RuntimeException( 'MaxLength must be a positive integer' );
		}
	}
}