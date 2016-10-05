<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 12:03
 */

namespace OCFram;

// Besoin de démarrer la session USER systématiquement avec la classe
session_start();

/**
 * Class User
 *
 * Représente l'utilisateur par ses variables de session.
 *
 * @package OCFram
 */
class User extends ApplicationComponent {
	/**
	 * Récupère l'attribut de session $attr.
	 *
	 * @param $attr string
	 *
	 * @return null|string
	 */
	public function getAttribute( $attr ) {
		if ( isset( $_SESSION[ $attr ] ) ) {
			return $_SESSION[ $attr ];
		}
		
		return null;
	}
	
	/**
	 * Récupère les messages flash destinés à l'utilisateur et les détruit.
	 *
	 * @return null|string
	 */
	public function getFlash() {
		$flash = $this->getAttribute( 'flash' );
		unset( $_SESSION[ 'flash' ] );
		
		return $flash;
	}
	
	/**
	 * Vérifie si un message flash est à afficher.
	 *
	 * @return bool
	 */
	public function hasFlash() {
		return isset( $_SESSION[ 'flash' ] );
	}
	
	/**
	 * Vérifie si l'utilisateur est authentifié.
	 *
	 * @return bool
	 */
	public function isAuthenticated() {
		return isset( $_SESSION[ 'auth' ] ) AND $_SESSION[ 'auth' ] === true;
	}
	
	/**
	 * Setter pour les attributs de session.
	 *
	 * @param $attr  string Nom de l'attribut de session
	 * @param $value string Valeur de l'attribut de session
	 */
	public function setAttribute( $attr, $value ) {
		$_SESSION[ $attr ] = $value;
	}
	
	/**
	 * Setter pour l'authentification.
	 *
	 * @param bool $authenticated
	 */
	public function setAuthenticated( $authenticated = true ) {
		if ( !is_bool( $authenticated ) ) {
			throw new \InvalidArgumentException( 'Authentication value must be a true-or-false boolean' );
		}
		$this->setAttribute( 'auth', $authenticated );
	}
	
	/**
	 * Setter pour l'attribut de session flash
	 *
	 * @param $value
	 */
	public function setFlash( $value ) {
		$this->setAttribute( 'flash', $value );
	}
}