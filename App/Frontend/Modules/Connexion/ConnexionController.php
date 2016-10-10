<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 09:44
 */

namespace App\Frontend\Modules\Connexion;


use Entity\User;
use FormBuilder\SubscriptionFormBuilder;
use Model\UserManager;
use OCFram\Application;
use OCFram\BackController;
use OCFram\FormHandler;
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
	 * Vérifie si les identifiants de connexion sont corrects, et redirige vers l'accueil du site si c'est le cas.
	 *
	 * @param HTTPRequest $Request
	 */
	public function executeGetConnection( HTTPRequest $Request ) {
		/**
		 * @var $User_manager UserManager
		 */
		if ( $Request->postExists( 'login' ) ) {
			$given_login  = $Request->postData( 'login' );
			$User_manager = $this->managers->getManagerOf( 'User' );
			$User_stored  = $User_manager->getUsercUsingUsercLogin( $given_login );
			var_dump($User_stored->cryptKey());
			// On vérifie si le password passé à la requête crypté avec la même clé que le password en DB correspond au password de l'objet récupéré
			if ( User::cryptWithKey( $Request->postData( 'password' ), $User_stored->cryptKey() ) === $User_stored->password() ) {
				$this->app->user()->setAuthenticationLevel((int) $User_stored->type());
				$this->app->user()->setFlash('Vous êtes connecté.');
				$this->app->httpResponse()->redirect( '.' );
			}
			else {
				$this->app->user()->setFlash( self::REFUSED_CONNECTION );
			}
		}
	}
	
	/**
	 * Affiche un formulaire de création de compte utilisateur (niveau writer_only)
	 *
	 * @param HTTPRequest $Request
	 */
	public function executePutUser( HTTPRequest $Request ) {
		/**
		 * @var $User_manager UserManager
		 */
		$User_manager = $this->managers->getManagerOf( 'User' );
		if ( $Request->method() == HTTPRequest::POST_METHOD ) {
			$User = new User( array(
				'login'    => $Request->postData( 'login' ),
				'password' => $Request->postData( 'password' ),
				'email'    => $Request->postData( 'email' ),
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
			$this->app->user()->setFlash( 'Vous avez été correctement inscrit.' );
			$this->app->httpResponse()->redirect( '.' );
		}
		
		$this->page->addVar( 'header', 'Formulaire d\'inscription' );
		$this->page->addVar( 'form', $Form->createView() );
	}
	
	/**
	 * Déconnecte un utilisateur adminstrateur et redirige vers l'accueil du site
	 */
	public function executeClearConnection() {
		$this->app->user()->unsetAuthentication();
		$this->app->user()->setFlash( self::DISCONNECTION_SUCCESSFUL );
		// On redirige vers la racine
		$this->app->httpResponse()->redirect( '../' );
	}
}