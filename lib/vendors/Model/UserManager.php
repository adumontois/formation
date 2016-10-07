<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 07/10/2016
 * Time: 15:17
 */

namespace Model;


use Entity\User;
use OCFram\Entity;
use OCFram\Manager;

class UserManager extends Manager {
	
	public function save(Entity $User)
	{
		if (!$User instanceof User)
		{
			throw new \BadMethodCallException('Save method expects Entity\User argument.');
		}
		if ( $User->isValid() ) {
			if ( $User->objectNew() ) {
				$this->insertUserc( $User );
			}
			
			else {
				$this->updateUserc( $User );
			}
		}
	}
	
	
}