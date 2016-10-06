<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 18:07
 */

namespace App\Backend;


use OCFram\Application;

class BackendApplication extends Application {
	public function __construct() {
		parent::__construct();
		$this->name = 'Backend';
	}
	
	/**
	 * Lance l'application Backend
	 */
	public function run() {
		if ( $this->user->isAuthenticated() ) // Si l'utilisateur est authentifié, on récupère le contrôleur souhaité
		{
			$controller = $this->getController();
		}
		else // Sinon on récupère le contrôleur d'authentification
		{
			$controller = new Modules\Connexion\ConnexionController( $this, 'Connexion', 'buildIndex' );
		}
		$controller->execute();
		$this->httpResponse()->setPage( $controller->page() );
		$this->httpResponse()->send();
	}
}