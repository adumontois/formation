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
		/**
		 * @var $manager NewsManager
		 */
		$manager = $this->managers->getManagerOf();
		if ( $request->method() == 'POST' ) {
			$news = new News( array(
				'auteur'  => $request->postData( 'auteur' ),
				'titre'   => $request->postData( 'titre' ),
				'contenu' => $request->postData( 'contenu' ),
			) );
			// S'il s'agit d'un update, il faut connaître l'id de la news qui est donné dans l'url
			if ( $request->getExists( 'id' ) ) {
				$news->setId( $request->getData( 'id' ) );
			}
		}
		else {
			if ($request -> getExists('id'))
			{
				// Afficher le commentaire en update
				$news = $manager->getUnique('id');
			}
			else {
				$news = new News();
			}
		}
		
		// Construction du formulaire
		$FormBuilder = new NewsFormBuilder($news);
		$FormBuilder->build();
		$form = $FormBuilder->form();
		
		if ( $request->method() == 'POST' AND $form->isValid() ) {
			if ( $news->object_new() ) {
				$this->app->user()->setFlash( 'La news a été correctement ajoutée' );
			}
			else {
				$this->app->user()->setFlash( 'La news a été correctement modifiée' );
			};
			$manager->save( $news );
			$this->app->httpResponse()->redirect('/admin');
		}
		$this->page->addVar('form', $form -> createView());
	}
	
	/**
	 * Insère une news.
	 *
	 * @param HTTPRequest $request
	 */
	public function executeInsert( HTTPRequest $request ) {
		$this->processForm( $request );
		$this->page->addVar( 'title', 'Insertion d\'une news' );
	}
	
	/**
	 * Met à jour une news.
	 *
	 * @param HTTPRequest $request
	 */
	public function executeUpdate( HTTPRequest $request ) {
		$this->processForm( $request );
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
		if ($request->method() == 'POST') {
			$comment = new Comment( array(
				'id'      => $request->getData( 'id' ),
				'auteur'  => $request->postData( 'auteur' ),
				'contenu' => $request->postData( 'contenu' ),
			) );
		}
		else {
			// Récupérer le commentaire en DB
			$comment = $manager->get( $request->getData( 'id' ) );
		}
		
		// Construire le formulaire
		$formBuilder = new CommentFormBuilder($comment);
		$formBuilder ->build();
		$form = $formBuilder->form();
		if ( $request->method() == 'POST' AND $form->isValid() ) {
			$manager->save( $comment );
			$this->app->user()->setFlash('Le commentaire a été correctement modifié');
			// Redirection vers l'accueil d'administration
			$this->app->httpResponse()->redirect( '/admin' );
		}
		$this->page->addVar( 'form', $form ->createView() );
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
}