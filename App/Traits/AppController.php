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
		
		$menu_a = [['label' => 'Accueil',
						  'link' => '/']];
		if ( $this->app()->user()->isAuthenticated() ) {
			$menu_a[] = ['label' => ucfirst( User::getTextualStatus( $this->app()->user()->authenticationLevel() ) ). ' (connecté)',
					   'link' => '/admin/'];
			$menu_a[] = ['label' => 'Déconnexion',
						'link' => '/logout.html'];
			$menu_a[] = ['label' => 'Ajouter une news',
						 'link' => '/admin/news-insert.html'];
		}
		else {
			$menu_a[] = ['label' => 'Inscription',
						 'link' => '/create-account.html'];
			$menu_a[] = ['label' => 'Connexion',
						'link' => '/connect.html'];
		}
		$this->page()->addVar( 'menu_a', $menu_a );
		
		// Générer le flash
		if ( $this->app()->user()->hasFlash() ) {
			$flash = $this->app()->user()->getFlash();
			$this->page()->addVar( 'flash', $flash );
		}
	}
}