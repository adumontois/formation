<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 18/10/2016
 * Time: 17:28
 */

namespace App\Frontend\Modules\Member;


use App\Traits\AppController;
use Entity\User;
use Model\CommentsManager;
use Model\NewsManager;
use Model\UserManager;
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
		
		$User_manager    = $this->managers->getManagerOf( 'User' );
		$News_manager    = $this->managers->getManagerOf( 'News' );
		$Comment_manager = $this->managers->getManagerOf( 'Comments' );
		
		// Vérifier si l'User demandé existe : si oui, le récupérer et le formater
		if ( !$User_manager->existsUsercUsingUsercId( $Request->getData( 'id' ) ) ) {
			$this->app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \InvalidArgumentException( 'Le membre demandé n\'existe pas !' ) );
		}
		$User = $User_manager->getUsercUsingUsercId( $Request->getData( 'id' ) );
		$User->formatDate();
		$this->page->addVar( 'User', $User );
		
		// Récupérer toutes les news postées par l'utilisateur
		$News_owned_a = $News_manager->getNewscUsingUsercIdSortByIdDesc( $User->id() );
		foreach ( $News_owned_a as $News ) {
			$News->format();
			$News->setUser($User);
			if ($this->app->user()->authenticationLevel() === User::USERY_SUPERADMIN OR $News->User()->id() == $this->app->user()->userId()) {
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
		$this->page->addVar( 'News_owned_a', $News_owned_a );
		
		// Récupérer tous les commentaires postés par l'utilisateur
		$Comment_owned_a = $Comment_manager->getCommentcUsingUsercLoginSortByFk_SNCDesc( $User->login() );
		foreach ( $Comment_owned_a as $Comment ) {
			$Comment->formatDate();
			if ($this->app->user()->authenticationLevel() === User::USERY_SUPERADMIN) {
				$Comment->setAction_a( [
					'action_link'      => Router::getUrlFromModuleAndAction( 'Backend', 'News', 'putUpdateComment', array( 'id' => $Comment->id() ) ),
					'image_source'     => '/images/update.png',
					'alternative_text' => 'Modifier',
				] );
				$Comment->setAction_a( [
					'action_link'      => Router::getUrlFromModuleAndAction( 'Backend', 'News', 'clearComment', array( 'id' => $Comment->id() ) ),
					'image_source'     => '/images/delete.png',
					'alternative_text' => 'Supprimer',
				] );
			}
		}
		$this->page->addVar( 'Comment_owned_a', $Comment_owned_a );
		
		// Récupérer toutes les News où l'utilisateur a commenté
		$News_others_a = $News_manager->getNewscAndUserUsingUsercIdFilterNotAuthorButCommenterSortByIdDesc($User->id());
		
		foreach($News_others_a as $News) {
			$News->format();
			$News->User()[ 'link' ] = Router::getUrlFromModuleAndAction( 'Frontend', 'Member', 'buildMember', array( 'id' => (int)$News->User()->id() ) );
			if ($this->app->user()->authenticationLevel() === User::USERY_SUPERADMIN) {
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
		$this->page->addVar('News_others_a', $News_others_a);
	}
}