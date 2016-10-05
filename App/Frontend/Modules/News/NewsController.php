<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 14:48
 */

namespace App\Frontend\Modules\News;

use Entity\Comment;
use Entity\News;
use FormBuilder\CommentFormBuilder;
use Model\CommentsManager;
use Model\NewsManager;
use \OCFram\BackController;
use \OCFram\HTTPRequest;

class NewsController extends BackController {
	/**
	 * Affiche les $nombre_news dernières news, $nombre_news est une constante déclarée dans le fichier app.xml
	 */
	
	public function executeIndex() {
		// Récupérer la config
		$nombre_news   = $this->app()->config()->get( 'nombre_news' );
		$longueur_news = $this->app()->config()->get( 'longueur_news' );
		
		// Ajouter un titre à la page
		$this->page->addVar( 'title', 'List of ' . $nombre_news . ' last news' );
		
		// Récupérer le manager des news
		/** @var NewsManager $manager */
		$manager = $this->managers->getManagerOf();
		
		// Récupérer la liste des news à afficher
		$listeNews = $manager->getList( 0, $nombre_news );
		
		//
		foreach ( $listeNews as $news ) {
			// Prendre le nombre de caractères nécessaires
			/**
			 * @var News $news
			 */
			$news->setContenu( substr( $news->contenu(), 0, $longueur_news ) );
			if ( strlen( $news ) == $longueur_news ) {
				$news->setContenu( substr( $news->contenu(), 0, strrpos( ' ', $news->contenu() ) ) . '...' );
			}
		}
		$this->page->addVar( 'listeNews', $listeNews );
	}
	
	/**
	 * @param HTTPRequest $request
	 * Affiche une news et les commentaires associés
	 */
	public function executeShow( HTTPRequest $request ) {
		/**
		 * @var $news_manager    NewsManager
		 * @var $news            News
		 * @var $comment_manager CommentsManager
		 */
		$id           = $request->getData( 'id' );
		$news_manager = $this->managers->getManagerOf( 'News' );
		$news         = $news_manager->getUnique( $id );
		
		if ( empty( $news ) ) // Si la news n'existe pas on redirige
		{
			$this->app->httpResponse()->redirect404();
			exit;
		}
		
		// Afficher les commentaires
		$comment_manager = $this->managers->getManagerOf();
		$listeComments   = $comment_manager->getListOf( $news->id() );
		
		$this->page->addVar( 'titre', $news->titre() );
		$this->page->addVar( 'news', $news );
		$this->page->addVar( 'listeCommentaires', $listeComments );
		$this->page->addVar( 'user', $this->app->user() );
	}
	
	/**
	 * @param HTTPRequest $request
	 * Insère un commentaire
	 */
	public function executeInsertComment( HTTPRequest $request ) {
		if ( $request->method() == 'POST' ) {
			$commentaire = new Comment( array(
				'news'    => $request->getData( 'news' ),
				'auteur'  => $request->postData( 'auteur' ),
				'contenu' => $request->postData( 'contenu' ),
			) );
		}
		else {
			$commentaire = new Comment();
		}
		
		// Construction du formulaire
		// 1) Données values
		$formulaire = new CommentFormBuilder( $commentaire );
		// 2) Construction et vérification des données
		$formulaire->build();
		
		if ( $request->method() == 'POST' AND $formulaire->form()->isValid() ) // Si le formulaire est valide, enregistrer le commentaire en DB
		{
			/**
			 * @var $comment_manager CommentsManager
			 */
			$comment_manager = $this->managers->getManagerOf( 'Comments' );
			$comment_manager->save( $commentaire );
			$this->app->user()->setFlash( 'Votre commentaire a bien été ajouté.' );
			$this->app->httpResponse()->redirect( 'news-' . $request->getData( 'news' ) . '.html' );
		}
		else {
			$this->page->addVar( 'erreurs', $commentaire->erreurs() );
		}
		$this->page->addVar( 'title', 'Ajout d\'un commentaire' );
		$this->page->addVar( 'comment', $commentaire );
		// Passer le formulaire à la vue
		$this->page->addVar( 'form', $formulaire->form()->createView() );
	}
}