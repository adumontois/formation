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
use OCFram\BackController;
use OCFram\HTTPRequest;

class NewsController extends BackController {
	/**
	 * Récupère toutes les news disponibles en DB
	 */
	public function executeIndex() {
		$manager = $this->managers->getManagerOf( 'News' );
		$this->page->addVar( 'title', 'Liste des news' );
		$this->page->addVar( 'listeNews', $manager->getList() );
		$this->page->addVar( 'nbNews', $manager->count() );
	}
	
	/**
	 * @param HTTPRequest $request
	 * Gère l'insert ou l'update d'une news
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
			$manager = $this->managers->getManagerOf( 'News' );
			$manager->save( $news );
		}
		$this->page->addVar( 'news', $news );
	}
	
	/**
	 * @param HTTPRequest $request
	 * insère une news dans la DB
	 */
	public function executeInsert( HTTPRequest $request ) {
		if ( $request->postExists( 'auteur' ) ) {
			$this->processForm( $request );
		}
		$this->page->addVar( 'title', 'Insertion d\'une news' );
	}
	
	/**
	 * @param HTTPRequest $request
	 * met à jour une news
	 */
	public function executeUpdate( HTTPRequest $request ) {
		if ( $request->postExists( 'auteur' ) ) {
			$this->processForm( $request );
		}
		else {
			// Aller récupérer la news en DB
			$manager = $this->managers->getManagerOf( 'News' );
			$news    = $manager->getUnique( $request->getData( 'id' ) );
			$this->page->addVar( 'news', $news );
		}
		$this->page->addVar( 'title', 'Modification d\'une news' );
	}
	
	/**
	 * @param HTTPRequest $request
	 * Supprime une news
	 */
	public function executeDelete( HTTPRequest $request ) {
		if ( $request->getExists( 'id' ) ) {
			throw new \RuntimeException( 'Undefined news to delete' );
		}
		$manager = $this->managers->getManagerOf( 'News' );
		$news    = $manager->getUnique( $request->getData( 'id' ) );
		// Suppression des commentaires associés à la news
		$manager->deleteFromNews( $request->getExists( 'id' ) );
		// Suppression de la news
		$manager->delete( $news );
		$this->page->addVar( 'title', 'Suppression d\'une news' );
		$this->app->user()->setFlash( 'La news a été correctement supprimée' );
		$this->app->httpResponse()->redirect( '.' );
	}
	
	/**
	 * @param HTTPRequest $request
	 * Met à jour un commentaire
	 */
	public function executeUpdateComment( HTTPRequest $request ) {
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
	 * @param HTTPRequest $request
	 * Supprime un commentaire
	 */
	public function executeDeleteComment( HTTPRequest $request ) {
		$manager = $this->managers->getManagerOf( 'Comment' );
		$this->page->addVar( 'title', 'Suppression d\'un commentaire' );
		if ( $request->getExists( 'id' ) ) {
			throw new \RuntimeException( 'Undefined comment to delete' );
		}
		$comment = $manager->get( $request->getExists( 'id' ) );
		$manager->delete( $comment );
		$this->app->user()->setFlash( 'Le commentaire a été correctement supprimée' );
		$this->app->httpResponse()->redirect( '.' );
	}
}