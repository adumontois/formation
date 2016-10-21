<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 09:44
 */

namespace App\Frontend\Modules\Connection;


use App\Traits\AppController;
use Entity\User;
use FormBuilder\ConnectionFormBuilder;
use FormBuilder\SubscriptionFormBuilder;
use FormHandler\ConnectionFormHandler;
use Model\UserManager;
use OCFram\Application;
use OCFram\BackController;
use OCFram\FormHandler;
use OCFram\HTTPRequest;
use OCFram\HTTPResponse;
use OCFram\Router;

class ConnectionController extends BackController {
	use AppController;

	
	const DISCONNECTION_SUCCESSFUL = 'Vous avez été déconnecté de l\'interface de Mon super site.';
	const REFUSED_CONNECTION       = 'La combinaison login-password entrée est incorrecte.';

	
	/**
	 * Vérifie si les identifiants de connexion sont corrects, et redirige vers l'accueil du site si c'est le cas.
	 *
	 * @param HTTPRequest $Request
	 */
	public function executeGetConnection( HTTPRequest $Request ) {
		$this->run();
		/**
		 * @var $User_manager UserManager
		 */
		// Si l'utilisateur est connecté, on le déconnecte
		if ( self::$app->user()->isAuthenticated() ) {
			self::$app->user()->unsetAuthentication();
		}
		$User_manager = $this->managers->getManagerOf( 'User' );
		if ( $Request->method() == HTTPRequest::POST_METHOD ) {
			$User = new User ( array(
				'login'    => $Request->postData( 'login' ),
				'password' => $Request->postData( 'password' ),
			) );
		}
		else {
			$User = new User();
		}
		
		// Construction du formulaire
		$Form_builder = new ConnectionFormBuilder( $User, $User_manager );
		$Form_builder->build();
		$Form = $Form_builder->form();
		
		// Sauvegarder avec le FormHandler
		$Form_handler = new ConnectionFormHandler( $Form, $User_manager, $Request );
		$Form_handler->process();
		
		$this->page->addVar( 'header', 'Formulaire d\'inscription' );
		$this->page->addVar( 'form', $Form->createView() );
	}
	
	/**
	 * Affiche un formulaire de création de compte utilisateur (niveau writer_only)
	 *
	 * @param HTTPRequest $Request
	 */
	public function executePutUser( HTTPRequest $Request ) {
		$this->run();
		/**
		 * @var $User_manager UserManager
		 */
		if ( self::$app->user()->isAuthenticated() ) {
			self::$app->httpResponse()->redirectError( HTTPResponse::FORBIDDEN, new \Exception( 'Vous devez vous déconnecter avant de pouvoir inscrire un compte.' ) );
		}
		$User_manager = $this->managers->getManagerOf( 'User' );
		if ( $Request->method() == HTTPRequest::POST_METHOD ) {
			$User = new User( array(
				'login'            => $Request->postData( 'login' ),
				'password'         => $Request->postData( 'password' ),
				'email'            => $Request->postData( 'email' ),
				'password_confirm' => $Request->postData( 'password_confirm' ),
				'email_confirm'    => $Request->postData( 'email_confirm' ),
			) );
		}
		else {
			$User = new User();
		}
		
		// Construction du formulaire
		$Form_builder = new SubscriptionFormBuilder( $User, $User_manager );
		$Form_builder->build();
		$Form = $Form_builder->form();
		
		// Sauvegarder avec le FormHandler
		$Form_handler = new FormHandler( $Form, $User_manager, $Request );
		if ( $Form_handler->process() ) {
			self::$app->user()->setFlash( 'Vous avez été correctement inscrit.' );
			self::$app->httpResponse()->redirect( Router::getUrlFromModuleAndAction(self::$app->name(), 'News', 'buildIndex') );
		}
		else {
			$this->page->addVar( 'user_count', $User_manager->countUsercUsingUsercId() );
			$this->page->addVar( 'header', 'Formulaire d\'inscription' );
			$this->page->addVar( 'form', $Form->createView() );
		}
	}
	
	/**
	 * Déconnecte un utilisateur adminstrateur et redirige vers l'accueil du site
	 */
	public function executeClearConnection() {
		$this->access_authorized_to_a = [];
		$this->run();
		self::$app->user()->unsetAuthentication();
		self::$app->user()->setFlash( htmlspecialchars( self::DISCONNECTION_SUCCESSFUL ) );
		// On redirige vers la racine
		self::$app->httpResponse()->redirect( Router::getUrlFromModuleAndAction(self::$app->name(), 'News', 'buildIndex') );
	}
}