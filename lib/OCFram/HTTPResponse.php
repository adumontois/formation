<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 30/09/2016
 * Time: 15:45
 */

namespace OCFram;

/**
 * Class HTTPResponse
 * Classe permettant d'envoyer la réponse du serveur au client (headers, redirections...).
 *
 * @package OCFram
 */
class HTTPResponse extends ApplicationComponent {
	/**
	 * @var $page Page
	 */
	protected $page;
	const NOT_FOUND                     = 404;
	const SERVICE_TEMPORARY_UNAVAILABLE = 503;
	
	/**
	 * Ajoute le header spécifié en paramètre
	 *
	 * @param $header string
	 */
	public function addHeader( $header ) {
		header( $header );
	}
	
	/**
	 * Crée une redirection vers la page $location
	 *
	 * @param $location string
	 */
	public function redirect( $location ) {
		$this->addHeader( 'Location: ' . $location );
		// Toujours faire un exit après un header de redirect
		// sinon le code suivant est exécuté.
		exit;
	}
	
	/**
	 * Crée une redirection vers une erreur
	 *
	 * @param $error_number int
	 */
	public function redirectError( $error_number, \Exception $error ) {
		$this->page = new Page( $this->app() );
		$this->page->setContentFile( __DIR__ . '\..\..\Errors\\' . $error_number . '.html' );
		$this->page->addVar( 'erreur', $error->getMessage() );
		switch ( $error_number ) {
			case 404:
			
			case 503:
				$this->addHeader( 'HTTP/1.0 503 Service Temporarily Unavailable' );
				break;
			default:
				$this->addHeader( 'HTTP/1.0 404 not found' );
				$this->page->addVar( 'inexistant', 'Page associated to error ' . $error_number . ' doesn\'t exists' );
				break;
		}
		// Envoyer le contenu de l'erreur à la page d'erreur
		$this->send();
	}
	
	/**
	 * Envoie la page calculée au client
	 */
	public function send() {
		exit( $this->page->getGeneratedPage() );
	}
	
	/**
	 * Crée un cookie avec setcookie. Attention les valeurs par défaut sont modifiées.
	 *
	 * @param string      $name     nom du cookie
	 * @param string      $value    valeur du cookie
	 * @param int         $expire   [time() + délai d'expiration] | [mktime date d'expiration]
	 * @param string|null $path     chemin serveur sur lequel le cookie est disponible (NULL = tout)
	 * @param string|null $domain   domaine serveur sur lequel le cookie est disponible (NULL = tout)
	 * @param bool        $secure   indique si le cookie doit être transmis en HTTPS
	 * @param bool        $httpOnly indique que le cookie n'est accessible que par HTTP
	 */
	public function setCookie( $name, $value = '', $expire = 0, $path = null, $domain = null, $secure = false, $httpOnly = true ) {
		setcookie( $name, $value, $expire, $path, $domain, $secure, $httpOnly );
	}
	
	/**
	 * Setter pour l'attribut page.
	 *
	 * @param $page
	 */
	public function setPage( Page $page ) {
		$this->page = $page;
	}
}