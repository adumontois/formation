<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 18/10/2016
 * Time: 17:28
 */

namespace App\Frontend\Modules\Member;


use App\Traits\AppController;
use Entity\Comment;
use Entity\User;
use Model\CommentsManager;
use Model\NewsManager;
use Model\UserManager;
use OCFram\ApplicationComponent;
use OCFram\BackController;
use OCFram\HTTPRequest;
use OCFram\HTTPResponse;
use OCFram\Router;

/**
 * Class MemberController
 *
 * Classe qui gère l'affichage des membres
 *
 * @package App\Frontend\Modules\Member
 */
class MemberController extends BackController {
	use AppController;
	
	/**
	 * Affiche toutes les infos et les participations du membre sur le site.
	 *
	 * @param HTTPRequest $Request
	 */
	public function executeBuildMember( HTTPRequest $Request ) {
		$this->run();
		/**
		 * @var UserManager     $User_manager
		 * @var CommentsManager $Comment_manager
		 * @var NewsManager     $News_manager
		 */
		
		$User_manager = $this->managers->getManagerOf( 'User' );
		$News_manager = $this->managers->getManagerOf( 'News' );
		
		// Vérifier si l'User demandé existe : si oui, le récupérer et le formater
		if ( !$User_manager->existsUsercUsingUsercId( $Request->getData( 'id' ) ) ) {
			self::$app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \InvalidArgumentException( 'Le membre demandé n\'existe pas !' ) );
		}
		$User = $User_manager->getUsercUsingUsercId( $Request->getData( 'id' ) );
		$User->formatDate();
		$this->page->addVar( 'User', $User );
		
		$News_a = $News_manager->getNewscCommentcAndUserUsingUsercIdFilterOwnNewsOwnCommentsAndNewsUserCommentedSortByNewscIdAndCommentcId( $Request->getData( 'id' ) );
		foreach ( $News_a as $News ) {
			$News->format();
			if ( self::$app->user()->authenticationLevel() === User::USERY_SUPERADMIN OR $News->User()->id() == self::$app->user()->userId() ) {
				$News->setAdminLinks();
			}
			// Ajout du lien s'il modifie la page (on ne génère pas les liens qui mèneraient à la page en cours d'affichage)
			if ( $News->User()->id() != $User->id() ) {
				$News->User()->link = MemberController::getLinkToBuildMember( $News->User() );
			}
			
			foreach ( $News[ 'Comment_a' ] as $Comment ) {
				/**
				 * @var $Comment Comment
				 */
				$Comment->formatDate();
				if ( self::$app->user()->authenticationLevel() === User::USERY_SUPERADMIN ) {
					$Comment->setAdminLinks();
				}
			}
		}
		$this->page->addVar( 'News_a', $News_a );
	}
	
	/**
	 * Renvoie le lien de la page membre du User passé en paramètre
	 * Si le paramètre n'est pas renseigné, on récupère l'id User de la session en cours.
	 *
	 * @param User|null $Member
	 *
	 * @return string
	 */
	static public function getLinkToBuildMember( User $Member = null ) {
		if ( null === $Member ) {
			$id = ApplicationComponent::app()->user()->userId();
		}
		else {
			$id = $Member->id();
		}
		if ( empty( $id ) ) {
			throw new \RuntimeException( 'Impossible de créer le lien de la fiche membre : L\'id du membre n\'est pas renseigné !' );
		}
		
		return Router::getUrlFromModuleAndAction( 'Frontend', 'Member', 'buildMember', array( 'id' => (int)$id ) );
	}
}