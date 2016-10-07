<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 09:44
 */

namespace App\Backend\Modules\Connexion;


use OCFram\Application;
use OCFram\BackController;
use OCFram\HTTPRequest;

class ConnexionController extends BackController {
	/**
	 * ConnexionController constructor.
	 * Construit un backcontroller en spécifiant la DB news
	 *
	 * @param Application $App
	 * @param string      $module
	 * @param string      $action
	 */
	
	const DATABASE                 = 'news';
	const DISCONNECTION_SUCCESSFUL = 'Vous avez été déconnecté de l\'interface administrateur de Mon super site.';
	const REFUSED_CONNECTION       = 'La combinaison login-password entrée est incorrecte.';
	
	public function __construct( Application $App, $module, $action ) {
		parent::__construct( $App, $module, $action, self::DATABASE );
	}
	
	/**
	 * Vérifie si les identifiants de connexion sont corrects, et redirige vers l'accueil d'administration si c'est le cas.
	 *
	 * @param HTTPRequest $Request
	 */
	public function executeBuildIndex( HTTPRequest $Request ) {
		if ( $Request->postExists( 'login' ) ) {
			if ( $Request->postData( 'login' ) === $this->app->config()->get( 'login' ) AND $Request->postData( 'password' ) === $this->app->config()->get( 'password' ) ) {
				$this->app->user()->setAuthenticated();
				$this->app->httpResponse()->redirect( '.' );
			}
			else {
				$this->app->user()->setFlash( self::REFUSED_CONNECTION );
			}
		}
	}
	
	/**
	 * Déconnecte un utilisateur adminstrateur et redirige vers l'accueil du site
	 */
	public function executeClearConnection() {
		$this->app->user()->setAuthenticated( false );
		$this->app->user()->setFlash( self::DISCONNECTION_SUCCESSFUL );
		// On redirige vers la racine
		$this->app->httpResponse()->redirect( '../' );
	}
}