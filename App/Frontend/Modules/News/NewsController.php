<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 14:48
 */

namespace App\Frontend\Modules\News;

use App\Frontend\Modules\Member\MemberController;
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
		$nombre_news   = self::$app->config()->get( 'nombre_news' );
		$longueur_news = self::$app->config()->get( 'longueur_news' );
		
		// Récupérer le manager des news
		
		$News_manager = $this->managers->getManagerOf();
		
		// Récupérer la liste des news à afficher
		$Liste_news_a = $News_manager->getNewscAndUserSortByIdDesc( 0, $nombre_news );
		
		foreach ( $Liste_news_a as $News ) {
			// Prendre le nombre de caractères nécessaires
			$News->setContent( substr( $News->content(), 0, $longueur_news ) );
			if ( strlen( $News->content() ) == $longueur_news ) {
				$News->setContent( substr( $News->content(), 0, strrpos( $News->content(), ' ' ) ) . '...' );
			}
			$News->build_link       = self::getLinkToBuildNews( $News );
			$News->User()[ 'link' ] = MemberController::getLinkToBuildMember( $News->User() );
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
		// Si la news n'existe pas on redirige vers une erreur 404
		if ( null == $News ) {
			self::$app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \RuntimeException( 'La news demandée n\'existe pas !' ) );
		}
		$News->User()->link = MemberController::getLinkToBuildMember( $News->User() );
		$News->format();
		$News->link_insert_comment_json   = self::getLinkToPutInsertCommentFromAjax( $News );
		$News->link_refresh_comments_json = self::getLinkToBuildRefreshCommentsFromAjax( $News );
		
		// Afficher les commentaires
		$Comment_manager  = $this->managers->getManagerOf( 'Comments' );
		$Liste_comments_a = $Comment_manager->getCommentcAndUsercUsingNewscIdSortByNewscIdDesc( $News->id() );
		foreach ( $Liste_comments_a as $Comment ) {
			$Comment->formatDate();
			if ( self::$app->user()->authenticationLevel() == User::USERY_SUPERADMIN ) {
				$Comment->setAdminLinks('json');
			}
			if ( isset( $Comment->User ) ) {
				$Comment->User->link = MemberController::getLinkToBuildMember( $Comment->User );
			}
		}
		
		// Attention, la variable $Comment CHANGE DE SIGNIFICATION !
		$Comment = new Comment();
		
		// Construction du formulaire : dépend de si l'utilisateur est connu ou pas
		// On rajoute les données dont la méthode d'obtention varie
		if ( !self::$app->user()->hasAttribute( 'user_name' ) ) {
			$Form_builder = new CommentFormBuilderWithAuthor( $Comment );
		}
		else {
			$Comment->setAuthor( self::$app->user()->getAttribute( 'user_name' ) );
			$Form_builder = new CommentFormBuilder( $Comment );
		}
		
		$Form_builder->build();
		$Form = $Form_builder->form();
		
		$this->page->addVar( 'title', $News->title() );
		$this->page->addVar( 'News', $News );
		$this->page->addVar( 'Comment_list_a', $Liste_comments_a );
		$this->page->addVar( 'User', self::$app->user() );
		$this->page->addVar( 'form', $Form->createView() );
		$this->page->addVar( 'form_action', self::getLinkToPutInsertComment($News ));
		
		// Ajouter la date et heure
		$this->page->addVar( 'dateupdate', (new \DateTime())->format( 'Y-m-d H:i:s.u' ) );
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
				self::$app->httpResponse()
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
		if ( !self::$app->user()->hasAttribute( 'user_name' ) ) {
			$Comment->setAuthor( $Request->postData( 'author' ) );
			$Form_builder = new CommentFormBuilderWithAuthor( $Comment );
		}
		else {
			$Comment->setAuthor( self::$app->user()->getAttribute( 'user_name' ) );
			$Form_builder = new CommentFormBuilder( $Comment );
		}
		
		// 2) Construction et vérification des données
		$Form_builder->build();
		$Form = $Form_builder->form();
		
		// On vérifie que le nom d'auteur n'est pas déjà pris par un utilisateur enregistré si l'utilisateur n'est pas connecté
		if ( $Request->method() == HTTPRequest::POST_METHOD && !self::$app->user()->isAuthenticated() && $User_manager->existsUsercUsingUsercLogin( $Comment->author() ) ) {
			$Form->getFieldFromName( 'author' )
				 ->setErrorMessage( 'Le nom d\'utilisateur ' . $Comment->author() . ' est déjà utilisé par un utilisateur enregistré. Choisissez un autre nom.' );
		}
		
		// Sauvegarde avec le handler
		$Form_handler = new FormHandler( $Form, $this->managers->getManagerOf( 'Comments' ), $Request );
		if ( $Form_handler->process() ) {
			// Setter le login de l'utilisateur pour la session s'il n'avait pas été entré.
			if ( !self::$app->user()->hasAttribute( 'user_name' ) ) {
				self::$app->user()->setAttribute( 'user_name', $Comment->author() );
			}
			self::$app->user()->setFlash( 'Votre commentaire a bien été ajouté.' );
			self::$app->httpResponse()->redirect( self::getLinkToBuildNews( null, (int)$Request->getData( 'id' ) ) );
		}
		$this->page->addVar( 'title', 'Ajout d\'un commentaire' );
		// Passer le formulaire à la vue
		$this->page->addVar( 'form', $Form_builder->form()->createView() );
		// Rajouter le lien d'action du formulaire
		$this->page->addVar( 'form_action', self::getLinkToPutInsertComment(null, (int)$Request->getData( 'id' ) ) );
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
		$News_manager = $this->managers->getManagerOf();
		$User_manager = $this->managers->getManagerOf( 'User' );
		if ( !$News_manager->existsNewscUsingNewscId( $Request->getData( 'id' ) ) ) {
			$this->forbiddenAccess( 'Impossible d\'insérer votre commentaire : la news associée à votre commentaire n\'existe plus !', 1 );
		}
		$Comment = new Comment( array(
			'fk_SNC'  => $Request->getData( 'id' ),
			'content' => $Request->postData( 'content' ),
		) );
		
		// Construction du formulaire : dépend de si l'utilisateur est connu ou pas
		// On rajoute les données dont la méthode d'obtention varie
		if ( !self::$app->user()->hasAttribute( 'user_name' ) ) {
			$Comment->setAuthor( $Request->postData( 'author' ) );
			$Form_builder = new CommentFormBuilderWithAuthor( $Comment );
		}
		else {
			$Comment->setAuthor( self::$app->user()->getAttribute( 'user_name' ) );
			$Form_builder = new CommentFormBuilder( $Comment );
		}
		
		// 2) Construction et vérification des données
		$Form_builder->build();
		$Form = $Form_builder->form();
		
		// On vérifie que le nom d'auteur n'est pas déjà pris par un utilisateur enregistré si l'utilisateur n'est pas connecté
		if ( !self::$app->user()->hasAttribute( 'user_name' )
			 && $Request->method() == HTTPRequest::POST_METHOD
			 && $User_manager->existsUsercUsingUsercLogin( $Comment->author() )
		) {
			$Form->getFieldFromName( 'author' )
				 ->setErrorMessage( 'Le nom d\'utilisateur ' . $Comment->author() . ' est déjà utilisé par un utilisateur enregistré. Choisissez un autre nom.' );
		}
		
		// Sauvegarde avec le handler
		$Form_handler = new FormHandler( $Form, $this->managers->getManagerOf( 'Comments' ), $Request );
		
		if ( $Form_handler->process() ) {
			// Setter le login de l'utilisateur pour la session s'il n'avait pas été entré.
			if ( !self::$app->user()->hasAttribute( 'user_name' ) ) {
				self::$app->user()->setAttribute( 'user_name', $Comment->author() );
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
	public function executeBuildRefreshCommentsFromAjax( HTTPRequest $Request ) {
		$this->run();
		/**
		 * @var CommentsManager $Comments_manager
		 * @var NewsManager     $News_manager
		 * @var Comment[]       $New_comment_a
		 * @var Comment[]       $Update_comment_a
		 */
		$Comments_manager = $this->managers->getManagerOf( 'Comments' );
		// On vérifie l'existence de la news
		$News_manager = $this->managers->getManagerOf();
		if ( !$News_manager->existsNewscUsingNewscId( $Request->getData( 'id' ) ) ) {
			$this->forbiddenAccess( 'Impossible de rafraîchir les commentaires : la news n\'existe plus !', 1 );
		}
		
		// Sélection des nouveaux commentaires
		$New_comment_a = $Comments_manager->getCommentcUsingNewscIdFilterOverDatecreationSortByIdDesc( $Request->getData( 'id' ), $Request->postData( 'dateupdate' ) );
		self::formatCommentsForJson($New_comment_a);
		$this->page->addVar( 'New_comment_a', $New_comment_a );
		
		// Sélection des commentaires édités
		$Update_comment_a = $Comments_manager->getCommentcAndUsercUsingNewscIdFilterOverEditedAfterDateupdateAndCreatedBeforeDateupdateSortByIdDesc( $Request->getData( 'id' ), $Request->postData( 'dateupdate' ) );
		self::formatCommentsForJson($Update_comment_a);
		$this->page->addVar( 'Update_comment_a', $Update_comment_a );
		
		// Sélection des ids supprimés
		if ( $Request->postExists( 'displayed_comments_ids_a' ) ) {
			$delete_ids_a = $Comments_manager->filterCommentcUsingUnexistantCommentcId( explode( ',', $Request->postData( 'displayed_comments_ids_a' ) ) );
		}
		else {
			$delete_ids_a = [];
		}
		$this->page->addVar( 'delete_ids_a', $delete_ids_a );
		
		// Générer la date du refresh
		$this->page->addVar( 'dateupdate', ( new \DateTime() )->format( 'Y-m-d H:i:s.u' ) );
	}
	
	/**
	 * Renvoie le lien de la page d'accueil Frontend
	 *
	 * @return string
	 */
	static public function getLinkToBuildIndex() {
		return Router::getUrlFromModuleAndAction( 'Frontend', 'News', 'buildIndex' );
	}
	
	/**
	 * Renvoie le lien de la page d'affichage d'une News
	 * Si la News n'est pas connue, on regarde l'id de News passé en paramètre.
	 *
	 * @param News $News
	 * @param int $News_id
	 *
	 * @return string
	 */
	static public function getLinkToBuildNews( News $News = null, $News_id = null) {
		if (null === $News) {
			$id = $News_id;
		}
		else {
			$id = $News->id();
		}
		if ( empty( $id ) ) {
			throw new \RuntimeException( 'Can\'t create News link : News id is unknown !' );
		}
		return Router::getUrlFromModuleAndAction( 'Frontend', 'News', 'buildNews', array( 'id' => (int)$id ) );
	}
	
	/**
	 * Renvoie le lien de la page d'affichage du formulaire HTML d'insertion de commentaire à une News.
	 * Si la News n'est pas connue, on regarde l'id de News passé en paramètre.
	 *
	 * @param News $News
	 * @param int $News_id
	 *
	 * @return string
	 */
	static public function getLinkToPutInsertComment( News $News = null, $News_id = null) {
		if (null === $News) {
			$id = $News_id;
		}
		else {
			$id = $News->id();
		}
		if ( empty( $id ) ) {
			throw new \RuntimeException( 'Can\'t create News link : News id is unknown !' );
		}
		return Router::getUrlFromModuleAndAction( 'Frontend', 'News', 'putInsertComment', array( 'id' => (int)$id ) );
	}
	
	/**
	 * Génère le lien d'insertion d'un commentaire en Ajax
	 *
	 * @param News $News
	 *
	 * @return string
	 */
	static public function getLinkToPutInsertCommentFromAjax( News $News ) {
		$id = $News->id();
		if ( empty( $id ) ) {
			throw new \RuntimeException( 'Can\'t create News link : News id is unknown !' );
		}
		
		return Router::getUrlFromModuleAndAction( 'Frontend', 'News', 'putInsertCommentFromAjax', array( 'id' => (int)$id ) );
	}
	
	/**
	 * Génère le lien de rafraîchissement des commentaires en Ajax
	 *
	 * @param News $News
	 *
	 * @return string
	 */
	static public function getLinkToBuildRefreshCommentsFromAjax( News $News ) {
		$id = $News->id();
		if ( empty( $id ) ) {
			throw new \RuntimeException( 'Can\'t create News link : News id is unknown !' );
		}
		
		return Router::getUrlFromModuleAndAction( 'Frontend', 'News', 'buildRefreshCommentsFromAjax', array( 'id' => (int)$id) );
	}
	
	/**
	 * Format comments to be displayed in Json on buildNews.
	 *
	 * @param Comment[] $Comment_a Comment list to format
	 *
	 * @return Comment[] edited (reference return)
	 */
	static public function &formatCommentsForJson(array &$Comment_a ) {
		foreach ( $Comment_a as $Comment ) {
			$Comment->formatDate();
			if ( isset( $Comment->User ) ) {
				$Comment->User->build_link = MemberController::getLinkToBuildMember($Comment->User());
			}
			if ( self::$app->user()->authenticationLevel() == User::USERY_SUPERADMIN ) {
				$Comment->setAdminLinks('json');
			}
		}
		return $Comment_a;
	}
}