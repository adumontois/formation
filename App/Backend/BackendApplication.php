<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 18:07
 */

namespace App\Backend;


use Entity\User;
use OCFram\Application;
use OCFram\HTTPResponse;

class BackendApplication extends Application {
	public function __construct() {
		parent::__construct();
		$this->name = 'Backend';
	}
	
	/**
	 * Lance l'application Backend
	 */
	public function run() {
		if ($this->user->isAuthenticated()) // Si l'utilisateur est authentifié, on récupère le contrôleur souhaité
		{
			$Controller = $this->getController();
			$Controller->execute();
			$this->httpResponse()->setPage( $Controller->page() );
			$this->httpResponse()->send();
		}
		else // Sinon on renvoie une erreur access denied
		{
			$this->httpResponse->redirectError(HTTPResponse::ACCESS_DENIED, new \Exception('Vous n\'avez pas les droits pour accéder à cette page.'));
		}
	}
}