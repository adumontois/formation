<?php

namespace OCFram;

/**
 * Class HTTPRequest
 * Classe permettant de récupérer les éléments de la requête du client.
 *
 * @package OCFram
 */
class HTTPRequest extends ApplicationComponent {
	/**
	 * Retourne le contenu du cookie $key, ou NULL s'il n'est pas défini.
	 *
	 * @param $key string
	 *
	 * @return string|null
	 */
	public function cookieData( $key ) {
		if ( $this->cookieExists( $key ) ) {
			return (string)$_COOKIE[ $key ];
		}
		
		return null;
	}
	
	/**
	 * Vérifie si le cookie $key est défini ou non.
	 *
	 * @param $key string
	 *
	 * @return bool
	 */
	public function cookieExists( $key ) {
		return isset( $_COOKIE[ $key ] );
	}
	
	/**
	 * Vérifie si $_GET[$key] existe.
	 *
	 * @param $key string
	 *
	 * @return bool
	 */
	public function getExists( $key ) {
		return isset( $_GET[ $key ] );
	}
	
	/**
	 * Retourne la variable passée en méthode GET demandée, ou NULL si elle n'est pas définie.
	 *
	 * @param $key string
	 *
	 * @return string|null
	 */
	public function getData( $key ) {
		if ( $this->getExists( $key ) ) {
			return (string)$_GET[ $key ];
		}
		
		return null;
	}
	
	/**
	 * Renvoie la méthode d'accès pour envoyer la requête (POST, GET...) ou NULL si elle n'est pas définie.
	 *
	 * @return string|null
	 */
	public function method() //
	{
		if ( isset( $_SERVER[ 'REQUEST_METHOD' ] ) ) {
			return $_SERVER[ 'REQUEST_METHOD' ];
		}
		
		return null;
	}
	
	/**
	 * Vérifie si $_POST[$key] existe.
	 *
	 * @param $key string
	 *
	 * @return bool
	 */
	public function postExists( $key ) //
	{
		return isset( $_POST[ $key ] );
	}
	
	/**
	 * Retourne la variable passée en méthode POST demandée, ou NULL si elle n'est pas définie.
	 *
	 * @param $key string
	 *
	 * @return string|null
	 */
	public function postData( $key ) {
		if ( $this->postExists( $key ) ) {
			return (string)$_POST[ $key ];
		}
		
		return null;
	}
	
	/**
	 * Renvoie l'URI associée à la requête ou NULL si elle n'est pas définie.
	 *
	 * @return string|null
	 */
	public function requestURI() {
		if ( isset( $_SERVER[ 'REQUEST_URI' ] ) ) {
			return $_SERVER[ 'REQUEST_URI' ];
		}
		
		return null;
	}
}

