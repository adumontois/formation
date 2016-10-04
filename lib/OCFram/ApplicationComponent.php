<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 30/09/2016
 * Time: 16:19
 */

namespace OCFram;


abstract class ApplicationComponent {
	protected $app;
	
	/**
	 * @param Application $app
	 */
	public function __construct( Application $app ) {
		$this->app = $app;
	}
	
	/**
	 * @return Application
	 */
	public function app() {
		return $this->app;
	}
}