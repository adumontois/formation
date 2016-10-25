<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 17/10/2016
 * Time: 18:57
 */

namespace App\Frontend\Modules\Device;


use App\Traits\AppController;
use Detection\MobileDetect;
use OCFram\BackController;
use OCFram\Router;

/**
 * Class DeviceController
 *
 * Manipule le device de l'utilisateur.
 *
 * @package App\Frontend\Modules\Device
 */
class DeviceController extends BackController {
	use AppController;
	/**
	 * Crée une variable indiquant le device sur lequel l'utilisateur navigue.
	 */
	public function executebuildDevice() {
		$this->run();
		$detector = new MobileDetect();
		if ($detector->isMobile()) {
			$this->page->addVar('device', 'un mobile');
		}
		else if ($detector->isTablet()) {
			$this->page->addVar('device', 'une tablette');
		}
		else {
			$this->page->addVar('device', 'un ordinateur');
		}
	}
	
	/**
	 * Génère le lien de l'affichage du device de l'utilisateur.
	 *
	 * @return string
	 */
	 static public function getLinkToBuildDevice() {
	    return Router::getUrlFromModuleAndAction('Frontend', 'Device', 'buildDevice', array());
	 }
}