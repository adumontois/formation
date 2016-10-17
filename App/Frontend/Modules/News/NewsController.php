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
use FormBuilder\CommentFormBuilderWithAuthor;
use Model\CommentsManager;
use Model\NewsManager;
use Model\UserManager;
use Model\UserManagerPDO;
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
		
		// Attention, la variable $Comment CHANGE DE SIGNIFICATION !
		$Comment = new Comment();
		
		// Construction du formulaire : dépend de si l'utilisateur est connu ou pas
		// On rajoute les données dont la méthode d'obtention varie
		if ( !$this->app->user()->hasAttribute( 'user_name' ) ) {
			$Form_builder = new CommentFormBuilderWithAuthor( $Comment );
		}
		else {
			$Comment->setAuthor( $this->app->user()->getAttribute( 'user_name' ) );
			$Form_builder = new CommentFormBuilder( $Comment );
		}
		
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
		$dateupdate->setTimezone( new \DateTimeZone( 'Europe/Paris' ) );
		$dateupdate->format( 'Y-m-d H:i:s.u' );
		$this->page->addVar( 'dateupdate', $dateupdate->format( 'Y-m-d H:i:s.u' ) );
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
		$User_manager = $this->managers->getManagerOf( 'User' );
		$News_manager = $this->managers->getManagerOf();
		
		if ( $Request->method() == HTTPRequest::POST_METHOD ) {
			// La news doit exister
			if ( !$News_manager->existsNewscUsingNewscId( $Request->getData( 'id' ) ) ) {
				$this->app->httpResponse()
						  ->redirectError( HTTPResponse::NOT_FOUND, new \Exception( 'Impossible d\'insérer votre commentaire : la news associée à votre commentaire n\'existe plus !' ) );
			}
			
			// Créer le commentaire avec les données TOUJOURS passées
			$Comment = new Comment( array(
				'fk_SNC'  => $Request->getData( 'id' ),
				'content' => $Request->postData( 'content' ),
			) );
		}
		
		else {
			$Comment = new Comment();
		}
		
		// Construction du formulaire : dépend de si l'utilisateur est connu ou pas
		// On rajoute les données dont la méthode d'obtention varie
		if ( !$this->app->user()->hasAttribute( 'user_name' ) ) {
			$Comment->setAuthor( $Request->postData( 'author' ) );
			$Form_builder = new CommentFormBuilderWithAuthor( $Comment );
		}
		else {
			$Comment->setAuthor( $this->app->user()->getAttribute( 'user_name' ) );
			$Form_builder = new CommentFormBuilder( $Comment );
		}
		
		// 2) Construction et vérification des données
		$Form_builder->build();
		$Form = $Form_builder->form();
		
		// On vérifie que le nom d'auteur n'est pas déjà pris par un utilisateur enregistré si l'utilisateur n'est pas connecté
		if ( $Request->method() == HTTPRequest::POST_METHOD && !$this->app->user()->isAuthenticated() && $User_manager->existsUsercUsingUsercLogin( $Comment->author() ) ) {
			$Form->getFieldFromName( 'author' )
				 ->setErrorMessage( 'Le nom d\'utilisateur ' . $Comment->author() . ' est déjà utilisé par un utilisateur enregistré. Choisissez un autre nom.' );
		}
		
		// Sauvegarde avec le handler
		$Form_handler = new FormHandler( $Form, $this->managers->getManagerOf( 'Comments' ), $Request );
		if ( $Form_handler->process() ) {
			// Setter le login de l'utilisateur pour la session s'il n'avait pas été entré.
			if ( !$this->app->user()->hasAttribute( 'user_name' ) ) {
				$this->app->user()->setAttribute( 'user_name', $Comment->author() );
			}
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
	 */
	public function executePutInsertCommentFromAjax( HTTPRequest $Request ) {
		$this->run();
		/**
		 * @var NewsManager     $News_manager
		 * @var UserManager     $User_manager
		 * @var User            $User
		 * @var CommentsManager $Comments_manager
		 */
		
		// Il est important de ne faire que les vérifs et l'insertion en DB - tout le reste doit être géré en JS.
		// gérer la redirection et le master_error
		$News_manager = $this->managers->getManagerOf();
		$User_manager = $this->managers->getManagerOf( 'User' );
		if ( !$News_manager->existsNewscUsingNewscId( $Request->getData( 'id' ) ) ) {
			$this->app->httpResponse()
					  ->redirectError( HTTPResponse::NOT_FOUND, new \Exception( 'Impossible d\'insérer votre commentaire : la news associée à votre commentaire n\'existe plus !' ) );
		}
		$Comment = new Comment( array(
			'fk_SNC'  => $Request->getData( 'id' ),
			'content' => $Request->postData( 'content' ),
		) );
		
		// Construction du formulaire : dépend de si l'utilisateur est connu ou pas
		// On rajoute les données dont la méthode d'obtention varie
		if ( !$this->app->user()->hasAttribute( 'user_name' ) ) {
			$Comment->setAuthor( $Request->postData( 'author' ) );
			$Form_builder = new CommentFormBuilderWithAuthor( $Comment );
		}
		else {
			$Comment->setAuthor( $this->app->user()->getAttribute( 'user_name' ) );
			$Form_builder = new CommentFormBuilder( $Comment );
		}
		
		// 2) Construction et vérification des données
		$Form_builder->build();
		$Form = $Form_builder->form();
		
		// On vérifie que le nom d'auteur n'est pas déjà pris par un utilisateur enregistré si l'utilisateur n'est pas connecté
		if ( !$this->app->user()->isAuthenticated() && $Request->method() == HTTPRequest::POST_METHOD && $User_manager->existsUsercUsingUsercLogin( $Comment->author() ) ) {
			$Form->getFieldFromName( 'author' )
				 ->setErrorMessage( 'Le nom d\'utilisateur ' . $Comment->author() . ' est déjà utilisé par un utilisateur enregistré. Choisissez un autre nom.' );
		}
		
		// Sauvegarde avec le handler
		$Form_handler = new FormHandler( $Form, $this->managers->getManagerOf( 'Comments' ), $Request );
		
		if ( $Form_handler->process() ) {
			// Setter le login de l'utilisateur pour la session s'il n'avait pas été entré.
			if ( !$this->app->user()->hasAttribute( 'user_name' ) ) {
				$this->app->user()->setAttribute( 'user_name', $Comment->author() );
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
		$this->page->addVar( 'error_a', $Comment->error_a() );
	}
	
	/**
	 * Rafraîchit les commentaires d'une news depuis la dernière date donnée
	 *
	 * @param HTTPRequest $Request
	 */
	public function executebuildRefreshCommentsFromAjax( HTTPRequest $Request ) {
		/**
		 * @var CommentsManager $Comments_manager
		 * @var NewsManager     $News_manager
		 * @var Comment[]       $New_comment_a
		 * @var Comment[]       $Update_comment_a
		 */
		$Comments_manager = $this->managers->getManagerOf( 'Comments' );
		if ( $Request->postExists( 'dateupdate' ) && $Request->getExists( 'id' ) ) {
			// On vérifie l'existence de la news
			$News_manager = $this->managers->getManagerOf();
			if ( !$News_manager->existsNewscUsingNewscId( $Request->getData( 'id' ) ) ) {
				$this->app->httpResponse()
						  ->redirectError( HTTPResponse::NOT_FOUND, new \Exception( 'Impossible d\'afficher les nouveaux commentaires : la news concernée n\'existe plus !' ) );
			}
			
			// Sélection des nouveaux commentaires
			$New_comment_a = $Comments_manager->getCommentcUsingNewscIdFilterOverDatecreationSortByIdDesc( $Request->getData( 'id' ), $Request->postData( 'dateupdate' ) );
			foreach ( $New_comment_a as $Comment ) {
				$Comment->formatDate();
			}
			if ( $this->app->user()->authenticationLevel() == User::USERY_SUPERADMIN ) {
				foreach ( $New_comment_a as $Comment ) {
					// On ajoute les droits d'administrateur si besoin
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
			$this->page->addVar( 'New_comment_a', $New_comment_a );
			
			// Sélection des commentaires édités
			// Pas besoin de regénérer les droits, il n'y a pas de raison qu'ils changent
			$Update_comment_a = $Comments_manager->getCommentcUsingNewscIdFilterOverEditedAfterDateupdateAndCreatedBeforeDateupdateSortByIdDesc( $Request->getData( 'id' ), $Request->postData( 'dateupdate' ) );
			
			foreach ( $Update_comment_a as $Comment ) {
				$Comment->formatDate();
			}
			$this->page->addVar( 'Update_comment_a', $Update_comment_a );
			
			// Sélection des ids supprimés
			if ($Request->postExists('displayed_comments_ids_a')) {
				$delete_ids_a = $Comments_manager->filterCommentcUsingUnexistantCommentcId(explode(',', $Request->postData('displayed_comments_ids_a')));
			}
			else {
				$delete_ids_a = [];
			}
			$this->page->addVar('delete_ids_a', $delete_ids_a);
			
			// Générer la date du refresh
			$this->page->addVar( 'dateupdate', (new \DateTime())->format( 'Y-m-d H:i:s.u' ));
		}
		else {
			throw new \RuntimeException( 'Can\'t determine last update date !' );
		}
	}
}