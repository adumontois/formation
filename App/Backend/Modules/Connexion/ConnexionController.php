<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 09:44
 */

namespace App\Backend\Modules\Connexion;


use Entity\User;
use Model\UserManager;
use OCFram\Application;
use OCFram\BackController;
use OCFram\HTTPRequest;
use OCFram\HTTPResponse;

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
		/**
		 * @var $User_manager UserManager
		 */
		if ( $Request->postExists( 'login' ) ) {
			$given_login = $Request->postData('login');
			$User_manager = $this->managers->getManagerOf('User');
			$User_stored = $User_manager->getUsercUsingUsercLogin($given_login);
			// On vérifie si le password passé à la requête crypté avec la même clé que le password en DB correspond au password de l'objet récupéré
			if (User::cryptWithKey($Request->postData('password'), $User_stored->cryptKey()) === $User_stored->password()) {
				$this->app->user()->setAuthenticated();
				$this->app->user()->setAttribute('authentication_level', $User_stored->type());
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
	public function executePutUser(HTTPRequest $Request) {
		/**
		 * @var $User_manager UserManager
		 */
		$User_manager = $this->managers->getManagerOf('User');
		if ($Request->method() == HTTPRequest::POST_METHOD ) {
			$User = new User( array(
				'login'  => $Request->postData( 'login' ),
				'password'   => $Request->postData( 'password' ),
				'email' => $Request->postData( 'email' ),
			) );
			// S'il s'agit d'un update, il faut connaître l'id du User qui est donné dans l'url
			if ( $Request->getExists( 'id' ) ) {
				$User->setId( $Request->getData( 'id' ) );
			}
		}
		else {
			if ( $Request->getExists( 'id' ) ) {
				// Afficher le commentaire en update
				$User = $User_manager->getUsercUsingUsercId( $Request->getData( 'id' ) );
			}
			else {
				$User = new User();
			}
		}
		// News qui n'existe pas : on redirige vers une erreur 404
		if ( null === $User ) {
			$this->app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \RuntimeException( 'Le User à mettre à jour n\'existe pas !' ) );
		}
		
		// Construction du formulaire
		$Form_builder = new UserFormBuilder( $User );
		$Form_builder->build();
		$Form = $Form_builder->form();;
		// Sauvegarder avec le FormHandler
		$Form_handler = new FormHandler( $Form, $User_manager, $Request );
		if ( $Form_handler->process() ) {
			if ( $News->objectNew() ) {
				$this->app->user()->setFlash( 'La news a été correctement ajoutée.' );
			}
			else {
				$this->app->user()->setFlash( 'La news a été correctement modifiée.' );
			};
			$this->app->httpResponse()->redirect( self::BUILDINDEX_LOCATION );
		}
		
		
		if ( !$Request->getExists( 'id' ) ) {
			$this->page->addVar( 'title', 'Insertion d\'une news' );
			$this->page->addVar( 'header', 'Ajouter une news' );
		}
		else {
			$this->page->addVar( 'title', 'Modification d\'une news' );
			$this->page->addVar( 'header', 'Modifier une news' );
		}
		$this->page->addVar( 'form', $Form->createView() );
		$this->page->addVar( 'News', $News );
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