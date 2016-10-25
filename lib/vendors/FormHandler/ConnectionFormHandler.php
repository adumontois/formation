<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 11/10/2016
 * Time: 10:39
 */

namespace FormHandler;


use App\Frontend\Modules\Connection\ConnectionController;
use App\Frontend\Modules\News\NewsController;
use Entity\User;
use Model\UserManager;
use OCFram\FormHandler;
use OCFram\HTTPRequest;

/**
 * Class ConnectionFormHandler
 *
 * Handler qui vérifie si les informations de connexions sont correctes, et qui lance la connexion.
 *
 * @package FormHandler
 */
class ConnectionFormHandler extends FormHandler {
	/**
	 * Lance la connexion au site si les arguments de connexion sont valides. Un flash d'erreur est renvoyé si les arguments de connexion sont invalides.
	 * Ne fait rien si aucune donnée n'est passée.
	 *
	 * @return bool
	 */
	public function process() {
		/**
		 * @var UserManager $Manager
		 * @var User $User
		 */
		if ( $this->request->method() == HTTPRequest::POST_METHOD AND $this->form->isValid() ) {
			$Manager = $this->manager;
			$User = $this->form->entity();
			$User_stored  = $Manager->getUsercUsingUsercLogin( $User->login() );
			// On vérifie si le password est celui qu'on a trouvé en DB.
			if ($User_stored != NULL AND User::cryptWithKey( $User->password(), $User_stored->cryptKey() ) === $User_stored->password()) {
				$this->request->app()->user()->setAuthenticationLevel((int) $User_stored->fk_SUY());
				$this->request->app()->user()->setUserId($User_stored->id());
				$this->request->app()->user()->setAttribute('user_name', $User->login());
				$this->request->app()->user()->setFlash( 'Vous êtes connecté(e) en tant que ' . htmlspecialchars(User::getTextualStatus((int)$User_stored->fk_SUY())) . '.');
				$this->request->app()->httpResponse()->redirect( NewsController::getLinkToBuildIndex());
			}
			else {
				$this->request->app()->user()->setFlash( htmlspecialchars(ConnectionController::REFUSED_CONNECTION ));
			}
		}
		return false;
	}
}