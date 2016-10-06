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
	 * @param Application $app
	 * @param string      $module
	 * @param string      $action
	 */
	public function __construct( Application $app, $module, $action ) {
		parent::__construct( $app, $module, $action, 'news' );
	}
	
	/**
	 * Vérifie si les identifiants de connexion sont corrects, et redirige vers l'accueil d'administration si c'est le cas.
	 *
	 * @param HTTPRequest $request
	 */
	public function executeIndex( HTTPRequest $request ) {
		if ( $request->postExists( 'login' ) ) {
			if ( $request->postData( 'login' ) === $this->app->config()->get( 'login' ) AND $request->postData( 'login' ) === $this->app->config()->get( 'password' ) ) {
				$this->app->user()->setAuthenticated();
				$this->app->httpResponse()->redirect( '.' );
			}
			else {
				$this->app->user()->setFlash( 'Login-password combination is incorrect.' );
			}
		}
	}
}