<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 11/10/2016
 * Time: 10:11
 */

namespace App\Traits;

use Entity\User;
use OCFram\BackController;
use OCFram\Router;

/**
 * trait AppController
 *
 * Trait qui génère le menu et le contenu de la page en fonction de l'authentification du membre.
 *
 * @package App\Traits
 */
trait AppController {
	/**
	 * Génère le menu de la page courante. Doit être appelée à la fin de chaque contrôleur.
	 */
	public function run() {
		/**
		 * @var $this BackController
		 */
		// Générer les labels et liens de la vue
		//<li><a href="/">Accueil</a></li>
		
		$menu_a = [
			[
				'label' => 'Accueil',
				'link'  => Router::getUrlFromModuleAndAction('Frontend', 'News', 'buildIndex'),
			],
		];
		if ( $this->app()->user()->isAuthenticated() ) {
			$menu_a[] = [
				'label' => ucfirst( User::getTextualStatus( $this->app()->user()->authenticationLevel() ) ) . ' (connecté)',
				'link'  => Router::getUrlFromModuleAndAction('Backend', 'News', 'buildIndex'),
			];
			$menu_a[] = [
				'label' => 'Déconnexion',
				'link'  => Router::getUrlFromModuleAndAction('Frontend', 'Connection', 'clearConnection'),
			];
			$menu_a[] = [
				'label' => 'Ajouter une news',
				'link'  => Router::getUrlFromModuleAndAction('Backend', 'News', 'putInsertNews'),
			];
		}
		else {
			$menu_a[] = [
				'label' => 'Inscription',
				'link'  => Router::getUrlFromModuleAndAction('Frontend', 'Connection', 'putUser'),
			];
			$menu_a[] = [
				'label' => 'Connexion',
				'link'  => Router::getUrlFromModuleAndAction('Frontend', 'Connection', 'getConnection'),
			];
		}
		$this->page()->addVar( 'menu_a', $menu_a );
		
		// Générer le flash
		if ( $this->app()->user()->hasFlash() ) {
			$flash = $this->app()->user()->getFlash();
			$this->page()->addVar( 'flash', $flash );
		}
		
		// Générer les liens
		$layout_link_a                          = [];
		$layout_link_a[ 'Frontend-buildIndex' ] = Router::getUrlFromModuleAndAction( 'Frontend', 'News', 'buildIndex' );
		
		$this->page()->addVar( 'layout_link_a', $layout_link_a );
	}
}