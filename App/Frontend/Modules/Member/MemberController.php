<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 18/10/2016
 * Time: 17:28
 */

namespace App\Frontend\Modules\Member;


use App\Backend\Modules\News\NewsController;
use App\Traits\AppController;
use Entity\Comment;
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
		
		// Vérifier si l'User demandé existe : si oui, le récupérer et le formater
		if ( !$User_manager->existsUsercUsingUsercId( $Request->getData( 'id' ) ) ) {
			self::$app->httpResponse()->redirectError( HTTPResponse::NOT_FOUND, new \InvalidArgumentException( 'Le membre demandé n\'existe pas !' ) );
		}
		$User = $User_manager->getUsercUsingUsercId( $Request->getData( 'id' ) );
		$User->formatDate();
		$this->page->addVar( 'User', $User );
		
		$News_a = $News_manager->getNewscCommentcAndUserUsingUsercIdFilterOwnNewsOwnCommentsAndNewsUserCommentedSortByNewscIdAndCommentcId($Request->getData('id'));
		foreach ($News_a as $News) {
			$News->format();
			if (self::$app->user()->authenticationLevel() === User::USERY_SUPERADMIN OR $News->User()->id() == self::$app->user()->userId()) {
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
			// Ajout du lien s'il modifie la page (on ne génère pas les liens qui mèneraient à la page en cours d'affichage)
			if ($News->User()->id() != $User->id()) {
				$News->User()->link =
					Router::getUrlFromModuleAndAction(self::$app->name(), $this->module, $this->action, array('id' => (int)$News->User()->id()));
			}
			
			foreach ($News['Comment_a'] as $Comment) {
				/**
				 * @var $Comment Comment
				 */
				$Comment->formatDate();
				if (self::$app->user()->authenticationLevel() === User::USERY_SUPERADMIN) {
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
		}
		$this->page->addVar('News_a', $News_a);
	}
	
	
	/**
	 * Renvoie le lien de la page membre du User passé en paramètre
	 *
	 * @param User $Member
	 *
	 * @return string
	 */
	static public function getLinkToBuildMember(User $Member) {
		$id = $Member->id();
		if (empty($id)) {
			throw new \RuntimeException('Impossible de créer le lien de la fiche membre : L\'id du membre n\'est pas renseigné !');
		}
		return Router::getUrlFromModuleAndAction( 'Frontend' , 'Member' , 'buildMember', array('id' => (int)$Member->id()) );
	}
}