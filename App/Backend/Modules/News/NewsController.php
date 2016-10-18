<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 09:56
 */

namespace App\Backend\Modules\News;

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
		$this->run();
		/**
		 * @var NewsManager $News_manager
		 * @var News[]      $News_list_a
		 * @var string[]    $modify_a Liens permettant de modifier les news
		 */
		$News_manager = $this->managers->getManagerOf();
		$this->page->addVar( 'title', 'Liste des news' );
		try {
			$News_list_a = $News_manager->getNewscSortByIdDesc();
			foreach ( $News_list_a as $News ) {
				$News->format();
				// On génère le lien si l'utilisateur a les droits de modification et de suppression
				if ( $this->app->user()->authenticationLevel() === User::USERY_SUPERADMIN OR $this->app->user()->userId() == $News->User()->id() ) {
					$News->setAction_a( [
						'action_link'      => Router::getUrlFromModuleAndAction( 'Backend', 'News', 'putUpdateNews', array( 'id' => $News->id() ) ),
						'image_source'     => '/images/update.png',
						'alternative_text' => 'Modifier',
					] );
					$News->setAction_a( [
						'action_link'      => Router::getUrlFromModuleAndAction( 'Backend', 'News', 'clearNews', array( 'id' => $News->id() ) ),
						'image_source'     => '/images/delete.png',
						'alternative_text' => 'Supprimer',
					] );
				}
			}
			$this->page->addVar( 'News_list_a', $News_list_a );
			$this->page->addVar( 'news_count', $News_manager->countNewsc() );
		}
		catch ( \PDOException $Db_error ) {
			$this->app->httpResponse()->redirectError( HTTPResponse::SERVICE_TEMPORARY_UNAVAILABLE, $Db_error );
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
		$this->run();
		/**
		 * @var $News_manager NewsManager
		 */
		$News_manager = $this->managers->getManagerOf();
		if ( $Request->method() == HTTPRequest::POST_METHOD ) {
			$News = new News( array(
				'fk_SUC'  => $this->app->user()->userId(),
				'title'   => $Request->postData( 'title' ),
				'content' => $Request->postData( 'content' ),
			) );
			// S'il s'agit d'un update, il faut connaître l'id de la news qui est donné dans l'url
			if ( $Request->getExists( 'id' ) ) {
				// On vérifie si la news correspondante existe
				if ( !$News_manager->existsNewscUsingNewscId( $Request->getData( 'id' ) ) ) {
					$this->app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \Exception( 'La news à modifier n\'existe plus !' ) );
				}
				$News->setId( $Request->getData( 'id' ) );
			}
		}
		else {
			if ( $Request->getExists( 'id' ) ) {
				// Afficher le commentaire en update
				$News = $News_manager->getNewscUsingNewscId( $Request->getData( 'id' ) );
				// Seul un superadmin peut modifier les news des autres
				if ( $this->app->user()->userId() != $News->fk_SUC() AND $this->app->user()->authenticationLevel() !== User::USERY_SUPERADMIN ) {
					$this->app->httpResponse()->redirectError( HTTPResponse::ACCESS_DENIED, new \Exception( 'Vous ne pouvez pas modifier la news de quelqu\'un d\'autre !' ) );
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
				$this->app->user()->setFlash( 'La news a été correctement ajoutée.' );
			}
			else {
				$this->app->user()->setFlash( 'La news a été correctement modifiée.' );
			};
			$this->app->httpResponse()->redirect( Router::getUrlFromModuleAndAction( $this->app->name(), $this->module, 'buildIndex' ) );
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
		$this->run();
		/**
		 * @var $News_manager     NewsManager
		 * @var $Comments_manager CommentsManager
		 */
		$News_manager = $this->managers->getManagerOf();
		
		$News = $News_manager->getNewscUsingNewscId( $Request->getData( 'id' ) );
		if ( null == $News ) {
			$this->app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \RuntimeException( 'La news à supprimer n\'existe pas !' ) );
		}
		if ( $News->fk_SUC() !== $this->app->user()->userId() AND $this->app->user()->authenticationLevel() === User::USERY_SUPERADMIN ) {
			$this->app->httpResponse()->redirectError( HTTPResponse::ACCESS_DENIED, new \Exception( 'Vous ne pouvez pas supprimer la news de quelqu\'un d\'autre !' ) );
		}
		
		// Suppression des commentaires associés à la news
		$Comments_manager = $this->managers->getManagerOf( 'Comments' );
		$Comments_manager->deleteCommentcUsingNewscId( $Request->getData( 'id' ) );
		// Suppression de la news
		$News_manager->deleteNewscUsingNewscId( $Request->getData( 'id' ) );
		
		$this->page->addVar( 'title', 'Suppression d\'une news' );
		$this->app->user()->setFlash( 'La news a été correctement supprimée.' );
		$this->app->httpResponse()->redirect( Router::getUrlFromModuleAndAction( $this->app->name(), $this->module, 'buildIndex' ) );
	}
	
	/**
	 * Met à jour un commentaire.
	 *
	 * Si le commentaire n'existe pas, redirige vers une erreur 404.
	 *
	 * @param HTTPRequest $Request
	 */
	public function executePutUpdateComment( HTTPRequest $Request ) {
		$this->run();
		/**
		 * @var $Comments_manager CommentsManager
		 * @var $News_manager     NewsManager
		 */
		if ( $this->app->user()->authenticationLevel() != User::USERY_SUPERADMIN ) {
			$this->app->httpResponse()
					  ->redirectError( HTTPResponse::ACCESS_DENIED, new \Exception( 'Vous devez être ' . User::getTextualStatus( User::USERY_SUPERADMIN ) . ' pour éditer les commentaires.' ) );
		}
		$Comments_manager = $this->managers->getManagerOf( 'Comments' );
		$News_manager     = $this->managers->getManagerOf();
		if ( $Request->method() == HTTPRequest::POST_METHOD ) {
			if ( !ctype_digit( $Request->postData( 'news' ) ) ) {
				$this->app->httpResponse()->redirectError( HTTPResponse::BAD_REQUEST, new \Exception( 'Le champ de news caché a été modifié par l\'utilisateur. Bien essayé !' ) );
			}
			if ( !$Comments_manager->existsCommentcUsingCommentcId( $Request->getData( 'id' ) ) ) {
				$this->app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \Exception( 'Le commentaire en cours d\'édition n\'existe plus !' ) );
			}
			if ( !$News_manager->existsNewscUsingNewscId( $Request->postData( 'news' ) ) ) {
				$this->app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \Exception( 'La news associée au commentaire en cours d\'édition n\'existe pas !' ) );
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
			$this->app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \RuntimeException( 'Le commentaire à éditer n\'existe pas !' ) );
		}
		
		// Construire le formulaire
		$Form_builder = new CommentFormBuilder( $Comment );
		$Form_builder->build();
		$Form = $Form_builder->form();
		
		// Sauvegarder avec le FormHandler
		$Form_handler = new FormHandler( $Form, $Comments_manager, $Request );
		if ( $Form_handler->process() ) {
			$this->app->user()->setFlash( 'Le commentaire a été correctement modifié.' );
			// Redirection vers l'accueil d'administration
			$this->app->httpResponse()->redirect( Router::getUrlFromModuleAndAction( $this->app->name(), $this->module, 'buildIndex' ) );
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
		$this->run();
		/**
		 * @var $Comments_manager CommentsManager
		 */
		if ( $this->app->user()->authenticationLevel() != User::USERY_SUPERADMIN ) {
			$this->app->httpResponse()
					  ->redirectError( HTTPResponse::ACCESS_DENIED, new \Exception( 'Vous devez être ' . User::getTextualStatus( User::USERY_SUPERADMIN ) . ' pour supprimer les commentaires.' ) );
		}
		$Comments_manager = $this->managers->getManagerOf( 'Comments' );
		if ( !$Comments_manager->existsCommentcUsingCommentcId( $Request->getData( 'id' ) ) ) {
			$this->app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \RuntimeException( 'Le commentaire à supprimer n\'existe pas !' ) );
		}
		$Comments_manager->deleteCommentcUsingCommentcId( $Request->getData( 'id' ) );
		$this->app->user()->setFlash( 'Le commentaire a été correctement supprimé.' );
		$this->page->addVar( 'title', 'Suppression d\'un commentaire' );
		$this->app->httpResponse()->redirect( Router::getUrlFromModuleAndAction( $this->app->name(), $this->module, 'buildIndex' ) );
	}
	
	/**
	 * Supprime un commentaire en Ajax.
	 *
	 * @param HTTPRequest $Request
	 */
	public function executeClearCommentFromAjax( HTTPRequest $Request ) {
		$this->run();
		/**
		 * @var $Comments_manager CommentsManager
		 */
		$Comments_manager = $this->managers->getManagerOf( 'Comments' );
		if ( $this->app->user()->authenticationLevel() != User::USERY_SUPERADMIN ) {
			$this->page->addVar( 'master_code', 1 );
			$this->page->addVar( 'master_error', 'Vous devez être ' . User::getTextualStatus( User::USERY_SUPERADMIN ) . ' pour supprimer les commentaires.' );
		}
		else {
			if ( !$Comments_manager->existsCommentcUsingCommentcId( $Request->getData( 'id' ) ) ) {
				$this->page->addVar( 'master_code', 2 );
				$this->page->addVar( 'master_error', 'Le commentaire à supprimer n\'existe pas !' );
			}
			else {
				$Comments_manager->deleteCommentcUsingCommentcId( $Request->getData( 'id' ) );
			}
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
		$this->run();
		/**
		 * @var $Comments_manager CommentsManager
		 * @var $News_manager     NewsManager
		 */
		$Comments_manager = $this->managers->getManagerOf( 'Comments' );
		$News_manager     = $this->managers->getManagerOf();
		if ( $this->app->user()->authenticationLevel() != User::USERY_SUPERADMIN ) {
			$this->page->addVar( 'master_code', 1 );
			$this->page->addVar( 'master_error', 'Vous devez être ' . User::getTextualStatus( User::USERY_SUPERADMIN ) . ' pour supprimer les commentaires.' );
		}
		else {
			if ( $Request->postData('content') !== null ) {
				if ( !$Comments_manager->existsCommentcUsingCommentcId( $Request->getData( 'id' ) ) ) {
					$this->page->addVar( 'master_code', 2 );
					$this->page->addVar( 'master_error', 'Le commentaire en cours d\'édition n\'existe plus !' );
				}
			}
			
			// Récupérer le commentaire en DB
			$Comment = $Comments_manager->getCommentcUsingCommentcId( $Request->getData( 'id' ) );
			if ( $Request->postData('content') !== null) {
				$Comment->setContent($Request->postData('content'));
			}
			
			
			if (null === $Comment) {
				$this->page->addVar( 'master_code', 3 );
				$this->page->addVar( 'master_error', 'Le commentaire à éditer n\'existe pas !' );
			}
			else {
				// Construire le formulaire
				$Form_builder = new CommentFormBuilder( $Comment );
				$Form_builder->build();
				$Form = $Form_builder->form();
				
				if ($Request->postData('content') !== null) {
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
						$Comment = $Comments_manager->getCommentcUsingCommentcId($Request->getData('id'));
						/*$Comment->setAction_a( [
							'link'  => Router::getUrlFromModuleAndAction( 'Backend', 'News', 'putUpdateComment', array( 'id' => (int)$Comment->id() ) ),
							'label' => 'Modifier',
							'js_function' => 'update_comment_on_click'
						] );
						$Comment->setAction_a( [
							'link'  => Router::getUrlFromModuleAndAction( 'Backend', 'News', 'clearCommentFromAjax', array( 'id' => (int)$Comment->id() ) ),
							'label' => 'Supprimer',
							'js_function' => 'delete_comment_on_click'
						] );*/
					}
				}
				else {
					$this->page->addVar( 'form', $Form->createView() );
				}
				// Ne pas envoyer le form si OK
				$this->page->addVar( 'Comment', $Comment );
			}
		}
	}
}