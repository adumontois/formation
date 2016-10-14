<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 14:48
 */

namespace App\Frontend\Modules\News;

use App\Traits\AppController;
use Entity\Comment;
use Entity\News;
use Entity\User;
use FormBuilder\CommentFormBuilder;
use Model\CommentsManager;
use Model\CommentsManagerPDO;
use Model\NewsManager;
use Model\UserManager;
use Model\UserManagerPDO;
use OCFram\Application;
use \OCFram\BackController;
use OCFram\FormHandler;
use \OCFram\HTTPRequest;
use OCFram\HTTPResponse;
use OCFram\Router;

class NewsController extends BackController {
	use AppController;
	
	/**
	 * Affiche les $nombre_news dernières news, $nombre_news est une constante déclarée dans le fichier app.xml.
	 */
	public function executeBuildIndex() {
		$this->run();
		var_dump( new \DateTime() );
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
		$Liste_news_a = $News_manager->getNewscSortByIdDesc( 0, $nombre_news );
		
		foreach ( $Liste_news_a as $News ) {
			// Prendre le nombre de caractères nécessaires
			$News->setContent( substr( $News->content(), 0, $longueur_news ) );
			if ( strlen( $News->content() ) == $longueur_news ) {
				$News->setContent( substr( $News->content(), 0, strrpos( $News->content(), ' ' ) ) . '...' );
			}
			$News->setAction_a( array( 'build' => Router::getUrlFromModuleAndAction( 'Frontend', 'News', 'buildNews', array( 'id' => (int)$News->id() ) ) ) );
		}
		$this->page->addVar( 'title', 'Liste des ' . $nombre_news . ' dernières news' );
		$this->page->addVar( 'News_list_a', $Liste_news_a );
	}
	
	/**
	 * @param HTTPRequest $Request
	 * Affiche une news et les commentaires associés
	 */
	public function executeBuildNews( HTTPRequest $Request ) {
		$this->run();
		/**
		 * @var $News_manager    NewsManager
		 * @var $News            News
		 * @var $User_manager    UserManager
		 * @var $Comment_manager CommentsManager
		 */
		$News_manager = $this->managers->getManagerOf();
		$News         = $News_manager->getNewscUsingNewscId( $Request->getData( 'id' ) );
		$News->format();
		$News->setAction_a( [
			'insert_comment_json'   => Router::getUrlFromModuleAndAction( 'Frontend', 'News', 'putInsertCommentFromAjax', array( 'id' => $News->id() ) ),
			'refresh_comments_json' => Router::getUrlFromModuleAndAction( 'Frontend', 'News', 'buildRefreshCommentsFromAjax', array( 'id' => $News->id() ) ),
		] );
		// Si la news n'existe pas on redirige vers une erreur 404
		if ( null == $News ) {
			$this->app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \RuntimeException( 'La news demandée n\'existe pas !' ) );
		}
		
		// Afficher les commentaires
		$Comment_manager  = $this->managers->getManagerOf( 'Comments' );
		$Liste_comments_a = $Comment_manager->getCommentcUsingNewscIdSortByIdDesc( $News->id() );
		foreach ( $Liste_comments_a as $Comment ) {
			$Comment->formatDate();
			if ( $this->app->user()->authenticationLevel() == User::USERY_SUPERADMIN ) {
				$Comment->setAction_a( [
					'link'  => Router::getUrlFromModuleAndAction( 'Backend', 'News', 'putUpdateComment', array( 'id' => (int)$Comment->id() ) ),
					'label' => 'Modifier',
				] );
				$Comment->setAction_a( [
					'link'  => Router::getUrlFromModuleAndAction( 'Backend', 'News', 'clearComment', array( 'id' => (int)$Comment->id() ) ),
					'label' => 'Supprimer',
				] );
			}
		}
		
		
		// Construire le formulaire
		$Comment = new Comment();
		// Préremplir le champ auteur si l'utilisateur est connecté
		if ( $this->app->user()->isAuthenticated() ) {
			$User_manager = $this->managers->getManagerOf( 'User' );
			$User         = $User_manager->getUsercUsingUsercId( $this->app->user()->userId() );
			if ( null != $User ) {
				$Comment->setAuthor( $User->login() );
			}
		}
		
		$Form_builder = new CommentFormBuilder( $Comment );
		$Form_builder->build();
		$Form = $Form_builder->form();
		
		
		$this->page->addVar( 'title', $News->title() );
		$this->page->addVar( 'News', $News );
		$this->page->addVar( 'Comment_list_a', $Liste_comments_a );
		$this->page->addVar( 'User', $this->app->user() );
		$this->page->addVar( 'form', $Form->createView() );
		$this->page->addVar( 'form_action', Router::getUrlFromModuleAndAction( 'Frontend', 'News', 'putInsertComment', array( 'id' => (int)$News->id() ) ) );
		
		// Ajouter la date et heure
		$dateupdate = new \DateTime();
		$dateupdate = $dateupdate->format('Y-m-d H:i:s.u');
		$this->page->addVar( 'dateupdate', $dateupdate );
	}
	
	/**
	 * @param HTTPRequest $Request
	 * Insère un commentaire
	 */
	public function executePutInsertComment( HTTPRequest $Request ) {
		$this->run();
		/**
		 * @var NewsManager    $News_manager
		 * @var UserManagerPDO $User_manager
		 * @var User           $User
		 */
		
		if ( $Request->method() == HTTPRequest::POST_METHOD ) {
			$News_manager = $this->managers->getManagerOf();
			if ( !$News_manager->existsNewscUsingNewscId( $Request->getData( 'id' ) ) ) {
				$this->app->httpResponse()
						  ->redirectError( HTTPResponse::NOT_FOUND, new \Exception( 'Impossible d\'insérer votre commentaire : la news associée à votre commentaire n\'existe plus !' ) );
			}
			$Comment = new Comment( array(
				'fk_SNC'  => $Request->getData( 'id' ),
				'author'  => $Request->postData( 'author' ),
				'content' => $Request->postData( 'content' ),
			) );
		}
		else {
			$Comment = new Comment();
			// Préremplir le champ auteur si l'utilisateur est connecté
			if ( $this->app->user()->isAuthenticated() ) {
				$User_manager = $this->managers->getManagerOf( 'User' );
				$User         = $User_manager->getUsercUsingUsercId( $this->app->user()->userId() );
				if ( null != $User ) {
					$Comment->setAuthor( $User->login() );
				}
			}
		}
		
		// Construction du formulaire
		// 1) Données values
		$Form_builder = new CommentFormBuilder( $Comment );
		// 2) Construction et vérification des données
		$Form_builder->build();
		$Form = $Form_builder->form();
		
		// Sauvegarde avec le handler
		$Form_handler = new FormHandler( $Form, $this->managers->getManagerOf( 'Comments' ), $Request );
		if ( $Form_handler->process() ) {
			$this->app->user()->setFlash( 'Votre commentaire a bien été ajouté.' );
			$this->app->httpResponse()
					  ->redirect( Router::getUrlFromModuleAndAction( $this->app->name(), $this->module, 'buildNews', array( 'id' => (int)$Request->getData( 'id' ) ) ) );
		}
		$this->page->addVar( 'title', 'Ajout d\'un commentaire' );
		// Passer le formulaire à la vue
		$this->page->addVar( 'form', $Form_builder->form()->createView() );
		// Rajouter le lien d'action du formulaire
		$this->page->addVar( 'form_action', Router::getUrlFromModuleAndAction( 'Frontend', 'News', 'putInsertComment', array( 'id' => (int)$Request->getData( 'id' ) ) ) );
	}
	
	/**
	 * Methode pour gerer l'insertion d'un commentaire depuis une requête Ajax
	 *
	 * @param HTTPRequest $Request
	 *
	 * @return string|false Retourne un commentaire JSON s'il a été inséré, false sinon.
	 */
	public function executePutInsertCommentFromAjax( HTTPRequest $Request ) {
		/**
		 * @var NewsManager     $News_manager
		 * @var UserManager     $User_manager
		 * @var User            $User
		 * @var CommentsManager $Comments_manager
		 */
		
		// Il est important de ne faire que les vérifs et l'insertion en DB - tout le reste doit être géré en JS.
		// Est-ce qu'on peut rediriger en cas d'erreur ??? A voir aussi
		
		$News_manager = $this->managers->getManagerOf();
		if ( !$News_manager->existsNewscUsingNewscId( $Request->getData( 'id' ) ) ) {
			$this->app->httpResponse()
					  ->redirectError( HTTPResponse::NOT_FOUND, new \Exception( 'Impossible d\'insérer votre commentaire : la news associée à votre commentaire n\'existe plus !' ) );
		}
		$Comment = new Comment( array(
			'fk_SNC'  => $Request->getData( 'id' ),
			'author'  => $Request->postData( 'author' ),
			'content' => $Request->postData( 'content' ),
		) );
		
		// Construction du formulaire
		// 1) Données values
		$Form_builder = new CommentFormBuilder( $Comment );
		// 2) Construction et vérification des données
		$Form_builder->build();
		$Form = $Form_builder->form();
		
		// Sauvegarde avec le handler
		$Form_handler = new FormHandler( $Form, $this->managers->getManagerOf( 'Comments' ), $Request );
		
		if ( $Form_handler->process() ) {
			
			// On va récupérer l'heure qui a été insérée en base
			$Comments_manager = $this->managers->getManagerOf( 'Comments' );
			$Comment          = $Comments_manager->getCommentcUsingCommentcId( $Comment->id() );
			$Comment->formatDate();
			
			// On ajoute les droits d'administrateur si besoin
			if ( $this->app->user()->authenticationLevel() == User::USERY_SUPERADMIN ) {
				$Comment->setAction_a( [
					'link'  => Router::getUrlFromModuleAndAction( 'Backend', 'News', 'putUpdateComment', array( 'id' => (int)$Comment->id() ) ),
					'label' => 'Modifier',
				] );
				$Comment->setAction_a( [
					'link'  => Router::getUrlFromModuleAndAction( 'Backend', 'News', 'clearComment', array( 'id' => (int)$Comment->id() ) ),
					'label' => 'Supprimer',
				] );
			}
		}
		else {
			// Sinon on envoie les erreurs
			foreach ( $Form->Field_a() as $Field ) {
				$error = $Field->errorMessage();
				if ( !empty( $error ) ) {
					$Comment->addError_a( $Field->name(), $error );
				}
			}
		}
		$this->page->addVar( 'Comment', $Comment );
	}
	
	/**
	 * Rafraîchit les commentaires d'une news depuis la dernière date donnée
	 *
	 * /!\ Heure !
	 *
	 * @param HTTPRequest $Request
	 */
	public function executebuildRefreshCommentsFromAjax( HTTPRequest $Request ) {
		$this->run();
		/**
		 * @var CommentsManager $Comments_manager
		 * @var NewsManager     $News_manager
		 */
		$Comments_manager = $this->managers->getManagerOf( 'Comments' );
		if ( $Request->postExists( 'dateupdate' ) && $Request->getExists( 'id' ) ) {
			// On vérifie l'existence de la news
			$News_manager = $this->managers->getManagerOf();
			if ( !$News_manager->existsNewscUsingNewscId( $Request->getData( 'id' ) ) ) {
				$this->app->httpResponse()
						  ->redirectError( HTTPResponse::NOT_FOUND, new \Exception( 'Impossible d\'afficher les nouveaux commentaires : la news concernée n\'existe plus !' ) );
			}
			$Comment_a = $Comments_manager->getCommentcUsingNewscIdFilterOverDateupdateSortByIdDesc( $Request->getData( 'id' ), $Request->postData( 'dateupdate' ) );
			$this->page->addVar( 'Comment_a', $Comment_a );
			$dateupdate = new \DateTime();
			$dateupdate->format( 'Y-m-d H:i:s.u' );
			$this->page->addVar( 'dateupdate', $dateupdate );
		}
		else {
			throw new \RuntimeException( 'Can\'t determine last update date !' );
		}
	}
}