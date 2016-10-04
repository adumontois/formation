<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 12:53
 */

namespace App\Frontend;

use OCFram\Application;

class FrontendApplication extends Application {
	function __construct() {
		parent::__construct();
		$this->name = 'Frontend';
	}
	
	/**
	 *  Lance l'application Frontend
	 */
	function run() {
		$controller = $this->getController();
		$controller->execute();
		$this->httpResponse->setPage( $controller->page() );
		$this->httpResponse->send();
	}
}