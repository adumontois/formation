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
use Model\CommentsManager;
use Model\NewsManager;
use OCFram\BackController;
use OCFram\HTTPRequest;

/**
 * Class NewsController
 *
 * Contrôleur du module News.
 *
 * @package App\Backend\Modules\News
 */
class NewsController extends BackController {
	/**
	 * Récupère toutes les news disponibles en DB.
	 */
	public function executeIndex() {
		/**
		 * @var $manager NewsManager
		 */
		$manager = $this->managers->getManagerOf();
		$this->page->addVar( 'title', 'Liste des news' );
		$this->page->addVar( 'listeNews', $manager->getList() );
		$this->page->addVar( 'nbNews', $manager->count() );
	}
	
	/**
	 * Gère l'insert ou l'update d'une news depuis un formulaire.
	 *
	 * @param HTTPRequest $request
	 */
	public function processForm( HTTPRequest $request ) {
		if ( $request->method() ) {
			$news = new News( array(
				'auteur'  => $request->postData( 'auteur' ),
				'titre'   => $request->postData( 'titre' ),
				'contenu' => $request->postData( 'contenu' ),
			) );
		}
		else {
			$news = new News();
		}
		// S'il s'agit d'un update, il faut connaître l'id de la news qui est donné dans l'url
		if ( $request->postExists( 'id' ) ) {
			$news->setId( $request->postData( 'id' ) );
		}
		if ( !$news->isValid() ) {
			$this->page->addVar( 'erreurs', $news->erreurs() );
			// On ajoute un message au user pour lui dire que la news a bien été ajoutée/modifiée
			if ( $news->object_new() ) {
				$this->app->user()->setFlash( 'La news a été correctement ajoutée' );
			}
			else {
				$this->app->user()->setFlash( 'La news a été correctement modifiée' );
			}
		}
		else {
			/**
			 * @var $manager NewsManager
			 */
			$manager = $this->managers->getManagerOf();
			$manager->save( $news );
		}
		$this->page->addVar( 'news', $news );
	}
	
	/**
	 * Insère une news.
	 *
	 * @param HTTPRequest $request
	 */
	public function executeInsert( HTTPRequest $request ) {
		if ( $request->postExists( 'auteur' ) ) {
			$this->processForm( $request );
		}
		$this->page->addVar( 'title', 'Insertion d\'une news' );
	}
	
	/**
	 * Met à jour une news.
	 *
	 * @param HTTPRequest $request
	 */
	public function executeUpdate( HTTPRequest $request ) {
		/**
		 * @var $manager NewsManager
		 */
		if ( $request->postExists( 'auteur' ) ) {
			$this->processForm( $request );
		}
		else {
			// Aller récupérer la news en DB
			$manager = $this->managers->getManagerOf();
			$news    = $manager->getUnique( $request->getData( 'id' ) );
			$this->page->addVar( 'news', $news );
		}
		$this->page->addVar( 'title', 'Modification d\'une news' );
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
		if ( $request->getExists( 'id' ) ) {
			throw new \RuntimeException( 'Undefined news to delete' );
		}
		$news_manager = $this->managers->getManagerOf();
		$news         = $news_manager->getUnique( $request->getData( 'id' ) );
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
		if ( $request->postExists( 'auteur' ) ) {
			$comment = new Comment( array(
				'id'      => $request->getData( 'id' ),
				'auteur'  => $request->postData( 'auteur' ),
				'contenu' => $request->postData( 'contenu' ),
			) );
			if ( !$comment->isValid() ) {
				$this->page->addVar( 'erreurs', $comment->erreurs() );
			}
			else {
				$manager->save( $comment );
				$this->app->httpResponse()->redirect( '/news-' . $request->getData( 'id' ) . '.html' );
			}
			$this->page->addVar( 'comment', $comment );
		}
		else {
			$this->page->addVar( 'comment', $manager->get( $request->getData( 'id' ) ) );
		}
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
		$manager = $this->managers->getManagerOf( 'Comment' );
		$this->page->addVar( 'title', 'Suppression d\'un commentaire' );
		if ( !$request->getExists( 'id' ) ) {
			throw new \RuntimeException( 'Undefined comment to delete' );
		}
		$manager->delete( $request->getData( 'id' ) );
		$this->app->user()->setFlash( 'Le commentaire a été correctement supprimé' );
		$this->app->httpResponse()->redirect( '.' );
	}
	
	/**
	 * Modifie un commentaire.
	 *
	 * @param HTTPRequest $request
	 */
	public function executeModifyComment( HTTPRequest $request ) {
		/**
		 * @var $manager CommentsManager
		 */
		$manager = $this->managers->getManagerOf( 'Comment' );
		$this->page->addVar( 'title', 'Modification d\'un commentaire' );
		if ( !$request->getExists( 'id' ) ) {
			throw new \RuntimeException( 'Undefined comment to edit' );
		}
		$comment = $manager->get( $request->getData( 'id' ) );
		$manager->save( $comment );
		$this->app->user()->setFlash( 'Le commentaire a été correctement modifié' );
		// Redirection vers la news
		$this->app->httpResponse()->redirect( 'news-' . $comment->news() . '.html' );
	}
}