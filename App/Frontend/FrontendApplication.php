<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 12:53
 */

namespace App\Frontend;

use OCFram\Application;

/**
 * Class FrontendApplication
 *
 * Représente l'application de Frontend
 *
 * @package App\Frontend
 */
class FrontendApplication extends Application {
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