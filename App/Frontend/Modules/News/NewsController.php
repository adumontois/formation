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
use OCFram\Application;
use \OCFram\BackController;
use OCFram\FormHandler;
use \OCFram\HTTPRequest;
use OCFram\HTTPResponse;

class NewsController extends BackController {
	/**
	 * NewsController constructor.
	 * Construit un backcontroller en spécifiant la DB news.
	 *
	 * @param Application $App
	 * @param string      $module
	 * @param string      $action
	 */
	public function __construct( Application $App, $module, $action ) {
		parent::__construct( $App, $module, $action, 'news' );
	}
	
	/**
	 * Affiche les $nombre_news dernières news, $nombre_news est une constante déclarée dans le fichier app.xml.
	 */
	public function executeBuildIndex() {
		/**
		 * @var $News_manager NewsManager
		 * @var $Liste_news_a News[]
		 */
		// Récupérer la config
		$nombre_news   = $this->app()->config()->get( 'nombre_news' );
		$longueur_news = $this->app()->config()->get( 'longueur_news' );
		
		// Récupérer le manager des news
		
		$News_manager = $this->managers->getManagerOf();
		
		// Récupérer la liste des news à afficher
		$Liste_news_a = $News_manager->getNewscAndUsercLoginSortByIdDesc( 0, $nombre_news );
		
		//
		foreach ( $Liste_news_a as $News ) {
			// Prendre le nombre de caractères nécessaires
			$News->setContenu( substr( $News->contenu(), 0, $longueur_news ) );
			if ( strlen( $News->contenu() ) == $longueur_news ) {
				$News->setContenu( substr( $News->contenu(), 0, strrpos( $News->contenu(), ' ' ) ) . '...' );
			}
		}
		$this->page->addVar( 'title', 'Liste des ' . $nombre_news . ' dernières news' );
		$this->page->addVar( 'News_list_a', $Liste_news_a );
	}
	
	/**
	 * @param HTTPRequest $Request
	 * Affiche une news et les commentaires associés
	 */
	public function executeBuildNews( HTTPRequest $Request ) {
		/**
		 * @var $News_manager    NewsManager
		 * @var $News            News
		 * @var $Comment_manager CommentsManager
		 */
		$News_manager = $this->managers->getManagerOf();
		$News         = $News_manager->getNewscUsingNewscId( $Request->getData( 'id' ) );
		
		// Si la news n'existe pas on redirige vers une erreur 404
		if ( empty( $News ) ) {
			$this->app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \RuntimeException( 'La news demandée n\'existe pas !' ) );
		}
		
		// Afficher les commentaires
		$Comment_manager  = $this->managers->getManagerOf( 'Comments' );
		$Liste_comments_a = $Comment_manager->getCommentcUsingNewscIdSortByIdDesc( $News->id() );
		
		$this->page->addVar( 'title', $News->titre() );
		$this->page->addVar( 'News', $News );
		$this->page->addVar( 'Comment_list_a', $Liste_comments_a );
		$this->page->addVar( 'User', $this->app->user() );
	}
	
	/**
	 * @param HTTPRequest $Request
	 * Insère un commentaire
	 */
	public function executePutInsertComment( HTTPRequest $Request ) {
		if ( $Request->method() == HTTPRequest::POST_METHOD ) {
			$Commentaire = new Comment( array(
				'news'    => $Request->getData( 'id' ),
				'auteur'  => $Request->postData( 'auteur' ),
				'contenu' => $Request->postData( 'contenu' ),
			) );
		}
		else {
			$Commentaire = new Comment();
		}
		// Construction du formulaire
		// 1) Données values
		$Form_builder = new CommentFormBuilder( $Commentaire );
		// 2) Construction et vérification des données
		$Form_builder->build();
		$Form = $Form_builder->form();
		
		// Sauvegarde avec le handler
		$Form_handler = new FormHandler( $Form, $this->managers->getManagerOf( 'Comments' ), $Request );
		if ( $Form_handler->process() ) {
			$this->app->user()->setFlash( 'Votre commentaire a bien été ajouté.' );
			$this->app->httpResponse()->redirect( 'news-' . $Request->getData( 'id' ) . '.html' );
		}
		$this->page->addVar( 'title', 'Ajout d\'un commentaire' );
		// Passer le formulaire à la vue
		$this->page->addVar( 'form', $Form_builder->form()->createView() );
	}
}