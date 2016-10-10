<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 15:10
 */

namespace OCFram;

/**
 * Class Validator
 *
 * Représente un objet permettant de validrer une condition dans un champ de formulaire.
 *
 * @package OCFram
 */
abstract class Validator {
	/**
	 * @var $errorMessage string Message d'erreur à afficher si la condition n'est pas vérifiée
	 */
	protected $errorMessage;
	
	/**
	 * Construit un validateur avec un message d'erreur vierge.
	 *
	 * @param string $errorMessage
	 */
	public function __construct( $errorMessage = '' ) {
		$this->setErrorMessage( $errorMessage );
	}
	
	/**
	 * Vérifie si la/les valeur/s du champ respecte/nt la condition spécifiée par le validateur.
	 *
	 * @param $value string|array
	 *
	 * @return bool
	 */
	abstract public function isValid( $value );
	
	/**
	 * @return string Message d'erreur à afficher si la condition n'est pas vérifiée
	 */
	public function errorMessage() {
		return $this->errorMessage;
	}
	
	/**
	 * Setter pour l'attribut errorMessage.
	 *
	 * @param $errorMessage string Message d'erreur à afficher si la condition n'est pas vérifiée
	 */
	public function setErrorMessage( $errorMessage ) {
		if ( is_string( $errorMessage ) ) {
			$this->errorMessage = $errorMessage;
		}
		else {
			throw new \InvalidArgumentException( 'ErrorMessage must be a string' );
		}
	}
}