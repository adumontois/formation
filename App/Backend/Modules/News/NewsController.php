<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 09:56
 */

namespace App\Backend\Modules\News;

use App\Frontend\Modules\Member\MemberController;
use App\Traits\AppController;
use Entity\Comment;
use Entity\News;
use Entity\User;
use FormBuilder\CommentFormBuilder;
use FormBuilder\NewsFormBuilder;
use Model\CommentsManager;
use Model\NewsManager;
use OCFram\BackController;
use OCFram\FormHandler;
use OCFram\HTTPRequest;
use OCFram\HTTPResponse;
use OCFram\Router;

/**
 * Class NewsController
 *
 * Contrôleur du module News.
 *
 * @package App\Backend\Modules\News
 */
class NewsController extends BackController {
	/**
	 * Utiliser la génération du menu.
	 */
	use AppController;
	
	/**
	 * Récupère toutes les news disponibles en DB.
	 */
	public function executeBuildIndex() {
		$this->access_authorized_to_a = [ User::USERY_SUPERADMIN ];
		$this->run();
		/**
		 * @var NewsManager $News_manager
		 * @var News[]      $News_list_a
		 * @var string[]    $modify_a Liens permettant de modifier les news
		 */
		$News_manager = $this->managers->getManagerOf();
		$this->page->addVar( 'title', 'Liste des news' );
		try {
			$News_list_a = $News_manager->getNewscAndUserSortByIdDesc();
			foreach ( $News_list_a as $News ) {
				$News->format();
				$News->User()[ 'link' ] = MemberController::getLinkToBuildMember($News->User());
				$News->setAdminLinks();
			}
			$this->page->addVar( 'News_list_a', $News_list_a );
			$this->page->addVar( 'news_count', $News_manager->countNewsc() );
		}
		catch ( \PDOException $Db_error ) {
			self::$app->httpResponse()->redirectError( HTTPResponse::SERVICE_TEMPORARY_UNAVAILABLE, $Db_error );
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
		$this->access_authorized_to_a = [];
		$this->run();
		/**
		 * @var $News_manager NewsManager
		 */
		$News_manager = $this->managers->getManagerOf();
		if ( $Request->method() == HTTPRequest::POST_METHOD ) {
			$News = new News( array(
				'fk_SUC'  => self::$app->user()->userId(),
				'title'   => $Request->postData( 'title' ),
				'content' => $Request->postData( 'content' ),
			) );
			
			// S'il s'agit d'un update, il faut connaître l'id de la news qui est donné dans l'url
			if ( $Request->getExists( 'id' ) ) {
				// On vérifie si la news correspondante existe
				if ( !$News_manager->existsNewscUsingNewscId( $Request->getData( 'id' ) ) ) {
					self::$app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \Exception( 'La news à modifier n\'existe plus !' ) );
				}
				$News->setId( $Request->getData( 'id' ) );
			}
		}
		else {
			if ( $Request->getExists( 'id' ) ) {
				// Afficher le commentaire en update
				if ( !$News_manager->existsNewscUsingNewscId( $Request->getData( 'id' ) ) ) {
					self::$app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \Exception( 'La news à modifier n\'existe plus !' ) );
				}
				$News = $News_manager->getNewscUsingNewscId( $Request->getData( 'id' ) );
				// Seul un superadmin peut modifier les news des autres
				if (self::$app->user()->userId() != $News->fk_SUC() AND self::$app->user()->authenticationLevel() !== User::USERY_SUPERADMIN ) {
					self::$app->httpResponse()->redirectError( HTTPResponse::ACCESS_DENIED, new \Exception( 'Vous ne pouvez pas modifier la news de quelqu\'un d\'autre !' ) );
				}
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
		$Form_handler = new FormHandler( $Form, $News_manager, $Request );
		if ( $Form_handler->process() ) {
			if ( $News->objectNew() ) {
				self::$app->user()->setFlash( 'La news a été correctement ajoutée.' );
			}
			else {
				self::$app->user()->setFlash( 'La news a été correctement modifiée.' );
			};
			self::$app->httpResponse()->redirect( \App\Frontend\Modules\News\NewsController::getLinkToBuildIndex() );
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
		$this->access_authorized_to_a = [];
		$this->run();
		/**
		 * @var $News_manager     NewsManager
		 * @var $Comments_manager CommentsManager
		 */
		$News_manager = $this->managers->getManagerOf();
		
		$News = $News_manager->getNewscUsingNewscId( $Request->getData( 'id' ) );
		if ( null == $News ) {
			self::$app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \RuntimeException( 'La news à supprimer n\'existe pas !' ) );
		}
		if ( $News->fk_SUC() !== self::$app->user()->userId() AND self::$app->user()->authenticationLevel() !== User::USERY_SUPERADMIN ) {
			self::$app->httpResponse()->redirectError( HTTPResponse::ACCESS_DENIED, new \Exception( 'Vous ne pouvez pas supprimer la news de quelqu\'un d\'autre !' ) );
		}
		
		// Suppression des commentaires associés à la news
		$Comments_manager = $this->managers->getManagerOf( 'Comments' );
		$Comments_manager->deleteCommentcUsingNewscId( $Request->getData( 'id' ) );
		// Suppression de la news
		$News_manager->deleteNewscUsingNewscId( $Request->getData( 'id' ) );
		
		$this->page->addVar( 'title', 'Suppression d\'une news' );
		self::$app->user()->setFlash( 'La news a été correctement supprimée.' );
		self::$app->httpResponse()->redirect( \App\Frontend\Modules\News\NewsController::getLinkToBuildIndex() );
	}
	
	/**
	 * Met à jour un commentaire.
	 *
	 * Si le commentaire n'existe pas, redirige vers une erreur 404.
	 *
	 * @param HTTPRequest $Request
	 */
	public function executePutUpdateComment( HTTPRequest $Request ) {
		$this->access_authorized_to_a = [ User::USERY_SUPERADMIN ];
		$this->run();
		/**
		 * @var $Comments_manager CommentsManager
		 * @var $News_manager     NewsManager
		 */
		$Comments_manager = $this->managers->getManagerOf( 'Comments' );
		$News_manager     = $this->managers->getManagerOf();
		if ( $Request->method() == HTTPRequest::POST_METHOD ) {
			if ( !ctype_digit( $Request->postData( 'news' ) ) ) {
				self::$app->httpResponse()->redirectError( HTTPResponse::BAD_REQUEST, new \Exception( 'Le champ de news caché a été modifié par l\'utilisateur. Bien essayé !' ) );
			}
			if ( !$Comments_manager->existsCommentcUsingCommentcId( $Request->getData( 'id' ) ) ) {
				self::$app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \Exception( 'Le commentaire en cours d\'édition n\'existe plus !' ) );
			}
			if ( !$News_manager->existsNewscUsingNewscId( $Request->postData( 'news' ) ) ) {
				self::$app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \Exception( 'La news associée au commentaire en cours d\'édition n\'existe pas !' ) );
			}
			$Comment = new Comment( array(
				'id'      => $Request->getData( 'id' ),
				'fk_SNC'  => $Request->postData( 'news' ),
				'author'  => $Request->postData( 'author' ),
				'content' => $Request->postData( 'content' ),
			) );
		}
		else {
			// Récupérer le commentaire en DB
			$Comment = $Comments_manager->getCommentcUsingCommentcId( $Request->getData( 'id' ) );
		}
		
		// News qui n'existe pas : on redirige vers une erreur 404
		if ( null === $Comment ) {
			self::$app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \RuntimeException( 'Le commentaire à éditer n\'existe pas !' ) );
		}
		
		// Construire le formulaire
		$Form_builder = new CommentFormBuilder( $Comment );
		$Form_builder->build();
		$Form = $Form_builder->form();
		
		// Sauvegarder avec le FormHandler
		$Form_handler = new FormHandler( $Form, $Comments_manager, $Request );
		if ( $Form_handler->process() ) {
			self::$app->user()->setFlash( 'Le commentaire a été correctement modifié.' );
			// Redirection vers l'accueil d'administration
			self::$app->httpResponse()->redirect( NewsController::getLinkToBuildIndex() );
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
		$this->access_authorized_to_a = [ User::USERY_SUPERADMIN ];
		$this->run();
		/**
		 * @var $Comments_manager CommentsManager
		 */
		$Comments_manager = $this->managers->getManagerOf( 'Comments' );
		if ( !$Comments_manager->existsCommentcUsingCommentcId( $Request->getData( 'id' ) ) ) {
			self::$app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \RuntimeException( 'Le commentaire à supprimer n\'existe pas !' ) );
		}
		$Comments_manager->deleteCommentcUsingCommentcId( $Request->getData( 'id' ) );
		self::$app->user()->setFlash( 'Le commentaire a été correctement supprimé.' );
		$this->page->addVar( 'title', 'Suppression d\'un commentaire' );
		self::$app->httpResponse()->redirect( self::getLinkToBuildIndex() );
	}
	
	/**
	 * Supprime un commentaire en Ajax.
	 *
	 * @param HTTPRequest $Request
	 */
	public function executeClearCommentFromAjax( HTTPRequest $Request ) {
		$this->access_authorized_to_a = [ User::USERY_SUPERADMIN ];
		$this->run();
		/**
		 * @var $Comments_manager CommentsManager
		 */
		$Comments_manager = $this->managers->getManagerOf( 'Comments' );
		
		if ( !$Comments_manager->existsCommentcUsingCommentcId( $Request->getData( 'id' ) ) ) {
			$this->forbiddenAccess( 'Le commentaire à supprimer n\'existe pas !', 2 );
		}
		else {
			$Comments_manager->deleteCommentcUsingCommentcId( $Request->getData( 'id' ) );
		}
	}
	
	/**
	 * Met à jour un commentaire.
	 *
	 * Si le commentaire n'existe pas, redirige vers une erreur 404.
	 *
	 * @param HTTPRequest $Request
	 */
	public function executePutUpdateCommentFromAjax( HTTPRequest $Request ) {
		$this->access_authorized_to_a = [ User::USERY_SUPERADMIN ];
		$this->run();
		/**
		 * @var $Comments_manager CommentsManager
		 * @var $News_manager     NewsManager
		 */
		$Comments_manager = $this->managers->getManagerOf( 'Comments' );
		if ( $Request->postData( 'content' ) !== null AND !$Comments_manager->existsCommentcUsingCommentcId( $Request->getData( 'id' ) ) ) {
			$this->forbiddenAccess( 'Le commentaire en cours d\'édition n\'existe plus !', 1 );
		}
		
		// Récupérer le commentaire en DB
		$Comment = $Comments_manager->getCommentcUsingCommentcId( $Request->getData( 'id' ) );
		if ( $Request->postData( 'content' ) !== null ) {
			$Comment->setContent( $Request->postData( 'content' ) );
		}
		
		if ( null === $Comment ) {
			$this->forbiddenAccess( 'Le commentaire à éditer n\'existe pas !', 2 );
		}
		// Pas besoin de vérifier l'existence de la news, elle est garantie en base par la clé étrangère
		
		else {
			// Construire le formulaire
			$Form_builder = new CommentFormBuilder( $Comment );
			$Form_builder->build();
			$Form = $Form_builder->form();
			
			if ( $Request->postData( 'content' ) !== null ) {
				// Sauvegarder avec le FormHandler
				$Form_handler = new FormHandler( $Form, $Comments_manager, $Request );
				if ( !$Form_handler->process() ) {
					// On envoie les erreurs si besoin
					foreach ( $Form->Field_a() as $Field ) {
						$error = $Field->errorMessage();
						if ( !empty( $error ) ) {
							$Comment->addError_a( $Field->name(), $error );
						}
					}
					// Envoyer les erreurs
					$this->page->addVar( 'form', $Form->createView() );
				}
				// Récupérer le nouvel état du commentaire (date d'update par exemple)
				else {
					$Comment = $Comments_manager->getCommentcUsingCommentcId( $Request->getData( 'id' ) );
				}
			}
			else {
				$this->page->addVar( 'form', $Form->createView() );
			}
			// Ne pas envoyer le form si OK
			$this->page->addVar( 'Comment', $Comment );
		}
	}
	
	/**
	 * Renvoie le lien de la page de mise à jour d'une News
	 *
	 * @param News $News
	 *
	 * @return string
	 */
	static public function getLinkToPutUpdateNews(News $News) {
		$id = $News->id();
		if (empty($id)) {
			throw new \RuntimeException('Impossible de créer le lien de la News : L\'id de la News n\'est pas renseigné !');
		}
		return Router::getUrlFromModuleAndAction( 'Backend', 'News', 'putUpdateNews', array('id' => (int)$News->id()) );
	}
	
	/**
	 * Renvoie le lien de la page de suppression d'une News
	 *
	 * @param News $News
	 *
	 * @return string
	 */
	static public function getLinkToClearNews(News $News) {
		$id = $News->id();
		if (empty($id)) {
			throw new \RuntimeException('Impossible de créer le lien de la News : L\'id de la News n\'est pas renseigné !');
		}
		return Router::getUrlFromModuleAndAction( 'Backend', 'News', 'clearNews', array('id' => (int)$News->id()) );
	}
	
	/**
	 * Renvoie le lien de la page d'accueil Backend
	 *
	 * @return string
	 */
	static public function getLinkToBuildIndex() {
		return Router::getUrlFromModuleAndAction( 'Backend', 'News', 'buildIndex' );
	}
	
	/**
	 * Renvoie le lien de la page de mise à jour d'un Comment
	 *
	 * @param Comment $Comment
	 *
	 * @return string
	 */
	static public function getLinkToPutUpdateComment(Comment $Comment) {
		$id = $Comment->id();
		if (empty($id)) {
			throw new \RuntimeException('Impossible de créer le lien du Comment : L\'id du Comment n\'est pas renseigné !');
		}
		return Router::getUrlFromModuleAndAction( 'Backend', 'News', 'putUpdateComment', array('id' => (int)$Comment->id()) );
	}
	
	/**
	 * Renvoie le lien de la page de suppression d'un Comment
	 *
	 * @param Comment $Comment
	 *
	 * @return string
	 */
	static public function getLinkToClearComment(Comment $Comment) {
		$id = $Comment->id();
		if (empty($id)) {
			throw new \RuntimeException('Impossible de créer le lien du Comment : L\'id du Comment n\'est pas renseigné !');
		}
		return Router::getUrlFromModuleAndAction( 'Backend', 'News', 'clearComment', array('id' => (int)$Comment->id()) );
	}
	
	/**
	 * Génère le lien de création d'une News
	 *
	 * @return string
	 */
	 static public function getLinkToPutInsertNews() {
	    return Router::getUrlFromModuleAndAction('Backend', 'News', 'putInsertNews');
	 }
	
	/**
	 * Génère le lien de mise à jour d'un commentaire en Ajax
	 *
	 * @param Comment $Comment
	 *
	 * @return string
	 */
	static public function getLinkToPutUpdateCommentFromAjax( Comment $Comment ) {
		$id = $Comment->id();
		if ( empty( $id ) ) {
			throw new \RuntimeException( 'Can\'t create Comment link : Comment id is unknown !' );
		}
		
		return Router::getUrlFromModuleAndAction( 'Backend', 'News', 'putUpdateCommentFromAjax', array( 'id' => (int)$id ) );
	}
	
	/**
	 * Génère le lien de suppression d'un commentaire en Ajax
	 *
	 * @param Comment $Comment
	 *
	 * @return string
	 */
	static public function getLinkToClearCommentFromAjax( Comment $Comment ) {
		$id = $Comment->id();
		if ( empty( $id ) ) {
			throw new \RuntimeException( 'Can\'t create Comment link : Comment id is unknown !' );
		}
		
		return Router::getUrlFromModuleAndAction( 'Backend', 'News', 'clearCommentFromAjax', array( 'id' => (int)$id ) );
	}
}