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
			$this->page->addVar( 'T_NEWS_BUILDINDEX_NEWS_LIST', $News_manager->getList() );
			$this->page->addVar( 'T_NEWS_BUILDINDEX_NEWS_COUNT', $News_manager->count() );
		}
		catch ( \PDOException $Db_error ) {
			$this->app->httpResponse()->redirectError( HTTPResponse::SERVICE_TEMPORARY_UNAVAILABLE, $Db_error );
		}
	}
	
	/**
	 * Gère l'insert ou l'update d'une news depuis un formulaire.
	 *
	 * @param HTTPRequest $Request
	 */
	public function processForm( HTTPRequest $Request ) {
		/**
		 * @var $Manager NewsManager
		 */
		$Manager = $this->managers->getManagerOf();
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
				$News = $Manager->getUnique( $Request->getData( 'id' ) );
			}
			else {
				$News = new News();
			}
		}
		
		// Construction du formulaire
		$Form_builder = new NewsFormBuilder( $News );
		$Form_builder->build();
		$Form = $Form_builder->form();;
		// Sauvegarder avec le FormHandler
		$FormHandler = new FormHandler( $Form, $Manager, $Request );
		if ( $FormHandler->process() ) {
			if ( $News->object_new() ) {
				$this->app->user()->setFlash( 'La news a été correctement ajoutée' );
			}
			else {
				$this->app->user()->setFlash( 'La news a été correctement modifiée' );
			};
			$this->app->httpResponse()->redirect( self::BUILDINDEX_LOCATION );
		}
		
		
		if ( !$Request->getExists( 'id' ) ) {
			$page_prefix = 'T_NEWS_PUTINSERTNEWS_';
			$this->page->addVar( 'title', 'Insertion d\'une news' );
			$this->page->addVar( $page_prefix.'HEAD', 'Ajouter une news' );
		}
		else {
			$page_prefix = 'T_NEWS_PUTUPDATENEWS_';
			$this->page->addVar( 'title', 'Modification d\'une news' );
			$this->page->addVar( $page_prefix.'HEAD', 'Modifier une news' );
		}
		$this->page->addVar( $page_prefix.'FORM', $Form->createView() );
		$this->page->addVar( $page_prefix.'NEWS', $News );
	}
	
	/**
	 * Insère une news.
	 *
	 * @param HTTPRequest $Request
	 */
	public function executePutInsertNews( HTTPRequest $Request ) {
		$this->processForm( $Request );
	}
	
	/**
	 * Met à jour une news.
	 *
	 * @param HTTPRequest $request
	 */
	public function executePutUpdateNews( HTTPRequest $request ) {
		$this->processForm( $request );
	}
	
	/**
	 * Supprime une news.
	 *
	 * @param HTTPRequest $request
	 */
	public function executeDelete( HTTPRequest $request ) {
		/**
		 * @var $news_manager     NewsManager
		 * @var $comments_manager CommentsManager
		 */
		if ( !$request->getExists( 'id' ) ) {
			throw new \RuntimeException( 'Undefined news to delete' );
		}
		$news_manager = $this->managers->getManagerOf();
		// Suppression des commentaires associés à la news
		$comments_manager = $this->managers->getManagerOf( 'Comments' );
		$comments_manager->deleteFromNews( $request->getData( 'id' ) );
		// Suppression de la news
		$news_manager->delete( $request->getData( 'id' ) );
		$this->page->addVar( 'title', 'Suppression d\'une news' );
		$this->app->user()->setFlash( 'La news a été correctement supprimée' );
		$this->app->httpResponse()->redirect( '.' );
	}
	
	/**
	 * Met à jour un commentaire.
	 *
	 * @param HTTPRequest $request
	 */
	public function executeUpdateComment( HTTPRequest $request ) {
		/**
		 * @var $manager CommentsManager
		 */
		$manager = $this->managers->getManagerOf( 'Comments' );
		$this->page->addVar( 'title', 'Edition d\'un commentaire' );
		if ( $request->method() == 'POST' ) {
			$comment = new Comment( array(
				'id'      => $request->getData( 'id' ),
				'news'    => $request->postData( 'news' ),
				'auteur'  => $request->postData( 'auteur' ),
				'contenu' => $request->postData( 'contenu' ),
			) );
		}
		else {
			// Récupérer le commentaire en DB
			$comment = $manager->get( $request->getData( 'id' ) );
		}
		
		// Construire le formulaire
		$FormBuilder = new CommentFormBuilder( $comment );
		$FormBuilder->build();
		$Form = $FormBuilder->form();
		
		// Sauvegarder avec le FormHandler
		$FormHandler = new FormHandler( $Form, $manager, $request );
		if ( $FormHandler->process() ) {
			$this->app->user()->setFlash( 'Le commentaire a été correctement modifié' );
			// Redirection vers l'accueil d'administration
			$this->app->httpResponse()->redirect( '/admin/' );
		}
		$this->page->addVar( 'form', $Form->createView() );
		$this->page->addVar( 'comment', $comment );
	}
	
	/**
	 * Supprime un commentaire.
	 *
	 * @param HTTPRequest $request
	 */
	public function executeDeleteComment( HTTPRequest $request ) {
		/**
		 * @var $manager CommentsManager
		 */
		$manager = $this->managers->getManagerOf( 'Comments' );
		$this->page->addVar( 'title', 'Suppression d\'un commentaire' );
		if ( !$request->getExists( 'id' ) ) {
			throw new \RuntimeException( 'Undefined comment to delete' );
		}
		$manager->delete( $request->getData( 'id' ) );
		$this->app->user()->setFlash( 'Le commentaire a été correctement supprimé' );
		$this->app->httpResponse()->redirect( '.' );
	}
}