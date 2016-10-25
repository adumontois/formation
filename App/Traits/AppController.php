<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 11/10/2016
 * Time: 10:11
 */

namespace App\Traits;

use App\Frontend\Modules\Connection\ConnectionController;
use App\Frontend\Modules\Device\DeviceController;
use App\Frontend\Modules\Member\MemberController;
use App\Frontend\Modules\News\NewsController;
use Entity\User;
use Helpers\LinkHelper;
use OCFram\ApplicationComponent;
use OCFram\BackController;
use OCFram\HTTPResponse;

/**
 * trait AppController
 *
 * Trait qui génère le menu et le contenu de la page en fonction de l'authentification du membre.
 *
 * @package App\Traits
 */
trait AppController {
	use LinkHelper;
	/**
	 * @var int[] $access_authorized_to_a Liste des états utilisateur pour lesquels l'accès à l'action est autorisée.
	 */
	protected $access_authorized_to_a;
	
	/**
	 * Génère le menu de la page courante. Doit être appelée au début de chaque contrôleur.
	 */
	public function run() {
		/**
		 * @var $this BackController
		 */
		
		switch ( $this->page()->format() ) {
			case 'html':
				$this->runHTML();
				break;
			case 'json':
				$this->runJSON();
				break;
			default:
				throw new \Exception( 'Format ' . $this->page()->format() . ' has no run method defined.' );
		}
		
		// Mettre fin au programme si l'utilisateur n'est pas autorisé
		if ( !$this->isAuthorized() ) {
			$this->forbiddenAccess();
		}
	}
	
	/**
	 * Génère le menu d'une page HTML.
	 */
	private function runHTML() {
		/**
		 * @var $this BackController
		 */
		$menu_a = [];
		LinkHelper::addLink( LinkHelper::addLink( $menu_a, NewsController::getLinkToBuildIndex(), 'Accueil' ), DeviceController::getLinkToBuildDevice(), 'Votre appareil' );
		if ( ApplicationComponent::app()->user()->isAuthenticated() ) {
			LinkHelper::addLink( $menu_a, MemberController::getLinkToBuildMember(), ApplicationComponent::app()->user()
																										->getAttribute( 'user_name' ) . ' (' . ucfirst( User::getTextualStatus( ApplicationComponent::app()
																																																	->user()
																																																	->authenticationLevel() ) ) . ', connecté)' );
			
			if ( ApplicationComponent::app()->user()->authenticationLevel() == User::USERY_SUPERADMIN ) {
				LinkHelper::addLink( $menu_a, \App\Backend\Modules\News\NewsController::getLinkToBuildIndex(), 'Admin' );
			}
			LinkHelper::addLink( LinkHelper::addLink( $menu_a, ConnectionController::getLinkToClearConnection(), 'Déconnexion' ), \App\Backend\Modules\News\NewsController::getLinkToPutInsertNews(), 'Ajouter news' );
		}
		else {
			LinkHelper::addLink( LinkHelper::addLink( $menu_a, ConnectionController::getLinkToPutUser(), 'Inscription' ), ConnectionController::getLinkToGetConnection(), 'Connexion' );
		}
		$this->page()->addVar( 'menu_a', $menu_a );
		
		// Générer le flash
		if ( ApplicationComponent::app()->user()->hasFlash() ) {
			$flash = ApplicationComponent::app()->user()->getFlash();
			$this->page()->addVar( 'flash', $flash );
		}
		
		// Générer les liens sur la page hors du menu
		$h1_link_a                          = [];
		LinkHelper::addLink($h1_link_a, NewsController::getLinkToBuildIndex(), 'Mon super site');
		$this->page()->addVar( 'h1_link_a', $h1_link_a );
	}
	
	/**
	 * Génère le menu d'une page JSON
	 */
	private function runJSON() {
	}
	
	/**
	 * Indique si l'utilisateur est autorisé à accéder à la page du contrôleur, en fonction du contenu de access_authorized_to_a.
	 * Si access_authorized_to_a n'est pas setté, tout le monde peut accéder à la page.
	 * Si access_authorized_to_a est un tableau vide, seuls les utilisateurs authentifiés peuvent y accéder.
	 * Sinon, seuls les utilisateurs authentifiés dont le status est présent dans le tableau peuvent accéder à la page.
	 *
	 * @return bool
	 */
	public function isAuthorized() {
		/**
		 * @var $this BackController
		 */
		if ( !isset( $this->access_authorized_to_a ) ) {
			return true;
		}
		if ( $this->access_authorized_to_a === [] ) {
			return ApplicationComponent::app()->user()->isAuthenticated();
		}
		
		return array_search( ApplicationComponent::app()->user()->authenticationLevel(), $this->access_authorized_to_a ) !== false;
	}
	
	/**
	 * @return int[]
	 */
	public function access_forbidden_to_a() {
		return $this->access_authorized_to_a;
	}
	
	/**
	 * Setter pour l'attribut access_authorized_to_a.
	 *
	 * @param int []|[]|null $access_authorized_to_a Autorise l'accès à tous les statuts de membre passés en paramètre.
	 */
	public function setAccess_authorized_to_a( $access_authorized_to_a ) {
		$this->access_authorized_to_a = $access_authorized_to_a;
	}
	
	/**
	 * Interdit l'accès au contrôleur si l'utilisateur n'a pas les autorisations nécessaires. Met fin au programme.
	 *
	 * @param string $master_error Erreur à afficher à l'utilisateur
	 * @param int    $master_code  Code d'erreur à retourner
	 *
	 * @throws \Exception
	 */
	public function forbiddenAccess( $master_error = 'Vous n\'avez pas les droits suffisants pour effectuer cette action.', $master_code = 255 ) {
		/**
		 * @var $this BackController
		 */
		switch ( $this->page()->format() ) {
			case 'html':
				ApplicationComponent::app()->httpResponse()
									->redirectError( HTTPResponse::ACCESS_DENIED, new \RuntimeException( 'Vous n\'avez pas les droits suffisants pour consulter cette page.' ) );
				break;
			case 'json':
				// On doit empêcher le traitement ultérieur en sortant de l'action du contrôleur
				$this->page()->addVar( 'master_code', $master_code );
				$this->page()->addVar( 'master_error', $master_error );
				$this->runJSON();
				// Envoyer directement le résultat
				ApplicationComponent::app()->httpResponse()->send();
				break;
			default:
				throw new \Exception( 'Format ' . $this->page()->format() . ' has no run method defined.' );
		}
	}
}