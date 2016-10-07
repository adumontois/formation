<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 09:56
 */

namespace App\Backend\Modules\News;

use Entity\Comment;
use Entity\News;
use FormBuilder\CommentFormBuilder;
use FormBuilder\NewsFormBuilder;
use Model\CommentsManager;
use Model\NewsManager;
use OCFram\Application;
use OCFram\BackController;
use OCFram\FormHandler;
use OCFram\HTTPRequest;
use OCFram\HTTPResponse;

/**
 * Class NewsController
 *
 * Contrôleur du module News.
 *
 * @package App\Backend\Modules\News
 */
class NewsController extends BackController {
	/**
	 * NewsController constructor.
	 * Construit un backcontroller en spécifiant la DB news
	 *
	 * @param Application $App
	 * @param string      $module
	 * @param string      $action
	 */
	const DATABASE            = 'news';
	const BUILDINDEX_LOCATION = '/admin/';
	
	public function __construct( Application $App, $module, $action ) {
		parent::__construct( $App, $module, $action, self::DATABASE );
	}
	
	/**
	 * Récupère toutes les news disponibles en DB.
	 */
	public function executeBuildIndex() {
		/**
		 * @var $News_manager NewsManager
		 */
		$News_manager = $this->managers->getManagerOf();
		$this->page->addVar( 'title', 'Liste des news' );
		try {
			$this->page->addVar( 'News_list_a', $News_manager->getNewscSortByIdDesc() );
			$this->page->addVar( 'news_count', $News_manager->countNewscUsingNewscId() );
		}
		catch ( \PDOException $Db_error ) {
			$this->app->httpResponse()->redirectError( HTTPResponse::SERVICE_TEMPORARY_UNAVAILABLE, $Db_error );
		}
	}
	
	/**
	 * Gère l'insert ou l'update d'une news depuis un formulaire.
	 *
	 * Attention, cette action n'est pas destinée à être appelée directement par l'utilisateur.
	 *
	 * @param HTTPRequest $Request
	 */
	protected function executePutNews( HTTPRequest $Request ) {
		/**
		 * @var $News_manager NewsManager
		 */
		$News_manager = $this->managers->getManagerOf();
		if ( $Request->method() == HTTPRequest::POST_METHOD ) {
			$News = new News( array(
				'auteur'  => $Request->postData( 'auteur' ),
				'titre'   => $Request->postData( 'titre' ),
				'contenu' => $Request->postData( 'contenu' ),
			) );
			// S'il s'agit d'un update, il faut connaître l'id de la news qui est donné dans l'url
			if ( $Request->getExists( 'id' ) ) {
				$News->setId( $Request->getData( 'id' ) );
			}
		}
		else {
			if ( $Request->getExists( 'id' ) ) {
				// Afficher le commentaire en update
				$News = $News_manager->getNewscUsingNewscId( $Request->getData( 'id' ) );
			}
			else {
				$News = new News();
			}
		}
		// News qui n'existe pas : on redirige vers une erreur 404
		if ( null === $News ) {
			$this->app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \RuntimeException( 'La news à éditer n\'existe pas !' ) );
		}
		
		// Construction du formulaire
		$Form_builder = new NewsFormBuilder( $News );
		$Form_builder->build();
		$Form = $Form_builder->form();;
		// Sauvegarder avec le FormHandler
		$Form_handler = new FormHandler( $Form, $News_manager, $Request );
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
	 * Insère une news.
	 *
	 * @param HTTPRequest $Request
	 */
	public function executePutInsertNews( HTTPRequest $Request ) {
		$this->executePutNews( $Request );
	}
	
	/**
	 * Met à jour une news.
	 *
	 * Si la news n'existe pas, redirige vers une erreur 404.
	 *
	 * @param HTTPRequest $Request
	 */
	public function executePutUpdateNews( HTTPRequest $Request ) {
		$this->executePutNews( $Request );
	}
	
	/**
	 * Supprime une news et tous les commentaires qui lui sont associés.
	 *
	 * Si la news n'existe pas, redirige vers une erreur 404.
	 *
	 * @param HTTPRequest $Request
	 */
	public function executeClearNews( HTTPRequest $Request ) {
		/**
		 * @var $News_manager     NewsManager
		 * @var $Comments_manager CommentsManager
		 */
		$News_manager = $this->managers->getManagerOf();
		// Suppression des commentaires associés à la news
		$Comments_manager = $this->managers->getManagerOf( 'Comments' );
		$Comments_manager->deleteCommentcUsingNewscId( $Request->getData( 'id' ) );
		// Suppression de la news : si elle n'existe pas on redirige vers 404
		if ( !$News_manager->deleteNewscUsingNewscId( $Request->getData( 'id' ) ) ) {
			$this->app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \RuntimeException( 'La news à supprimer n\'existe pas !' ) );
		}
		$this->page->addVar( 'title', 'Suppression d\'une news' );
		$this->app->user()->setFlash( 'La news a été correctement supprimée.' );
		$this->app->httpResponse()->redirect( '.' );
	}
	
	/**
	 * Met à jour un commentaire.
	 *
	 * Si le commentaire n'existe pas, redirige vers une erreur 404.
	 *
	 * @param HTTPRequest $Request
	 */
	public function executePutUpdateComment( HTTPRequest $Request ) {
		/**
		 * @var $Comments_manager CommentsManager
		 */
		$Comments_manager = $this->managers->getManagerOf( 'Comments' );
		if ( $Request->method() == HTTPRequest::POST_METHOD ) {
			$Comment = new Comment( array(
				'id'      => $Request->getData( 'id' ),
				'news'    => $Request->postData( 'news' ),
				'auteur'  => $Request->postData( 'auteur' ),
				'contenu' => $Request->postData( 'contenu' ),
			) );
		}
		else {
			// Récupérer le commentaire en DB
			$Comment = $Comments_manager->getCommentcUsingCommentcId( $Request->getData( 'id' ) );
		}
		
		// News qui n'existe pas : on redirige vers une erreur 404
		if ( null === $Comment ) {
			$this->app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \RuntimeException( 'Le commentaire à éditer n\'existe pas !' ) );
		}
		
		// Construire le formulaire
		$Form_builder = new CommentFormBuilder( $Comment );
		$Form_builder->build();
		$Form = $Form_builder->form();
		
		// Sauvegarder avec le FormHandler
		$Form_handler = new FormHandler( $Form, $Comments_manager, $Request );
		if ( $Form_handler->process() ) {
			$this->app->user()->setFlash( 'Le commentaire a été correctement modifié.' );
			// Redirection vers l'accueil d'administration
			$this->app->httpResponse()->redirect( self::BUILDINDEX_LOCATION );
		}
		$this->page->addVar( 'title', 'Edition d\'un commentaire' );
		$this->page->addVar( 'form', $Form->createView() );
		$this->page->addVar( 'Comment', $Comment );
	}
	
	/**
	 * Supprime un commentaire.
	 *
	 * Si le commentaire n'existe pas, redirige vers une erreur 404.
	 *
	 * @param HTTPRequest $Request
	 */
	public function executeClearComment( HTTPRequest $Request ) {
		/**
		 * @var $Comments_manager CommentsManager
		 */
		$Comments_manager = $this->managers->getManagerOf( 'Comments' );
		if ( !$Comments_manager->deleteCommentcUsingCommentcId( $Request->getData( 'id' ) ) ) {
			$this->app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \RuntimeException( 'Le commentaire à supprimer n\'existe pas !' ) );
		}
		$this->app->user()->setFlash( 'Le commentaire a été correctement supprimé.' );
		$this->page->addVar( 'title', 'Suppression d\'un commentaire' );
		$this->app->httpResponse()->redirect( '.' );
	}
}