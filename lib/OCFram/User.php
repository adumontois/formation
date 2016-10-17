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
		return isset( $_SESSION[ 'auth' ] );
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
	 * Vérifie si l'attribut $attr est défini en session.
	 *
	 * @param string $attr
	 *
	 * @return bool
	 */
	public function hasAttribute($attr) {
		return isset($_SESSION[$attr]);
	}
	
	/**
	 * Setter pour l'authentification.
	 *
	 * @param int $authenticated
	 */
	public function setAuthenticationLevel( $authenticated ) {
		if ( !is_int( $authenticated ) ) {
			throw new \InvalidArgumentException( 'Authentication value must be an integer' );
		}
		$this->setAttribute( 'auth', $authenticated );
	}
	
	/**
	 * Supprime l'authentification.
	 */
	public function unsetAuthentication() {
		unset($_SESSION['user_id']);
		unset($_SESSION['user_name']);
		unset($_SESSION['auth']);
	}
	
	/**
	 * @param int $id ID de T_SIT_userc
	 */
	public function setUserId($id) {
		if ($this->isAuthenticated()) {
			$this->setAttribute('user_id', $id);
		}
	}
	
	/**
	 * Récupère l'ID du User connecté
	 *
	 * @return int|null
	 */
	public function userId() {
		return (int) $this->getAttribute('user_id');
	}
	
	/**
	 * Récupère le niveau d'authentification de l'utilisateur
	 */
	public function authenticationLevel() {
		return $this->getAttribute( 'auth' );
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