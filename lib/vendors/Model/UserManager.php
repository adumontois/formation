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

abstract class UserManager extends Manager {
	/**
	 * Sauvegarde un utilisateur en base.
	 *
	 * @param Entity $User
	 */
	final public function save( Entity $User ) {
		if ( !$User instanceof User ) {
			throw new \BadMethodCallException( 'Save method expects Entity\User argument.' );
		}
		if ( $User->isValid() ) {
			if ( $User->objectNew() ) {
				$this->insertUserc( $User );
			}
			
			else {
				$this->updatePasswordAndMailOfUsercUsingId( $User );
			}
		}
	}
	
	/**
	 * Insère un utilisateur en base.
	 * Cette méthode ne devrait pas être appelée directement ; utiliser la méthode save pour y accéder.
	 *
	 * @param Entity $User
	 */
	abstract protected function insertUserc( Entity $User );
	
	/**
	 * Met à jour le password et le mail d'un utilisateur en base.
	 * Cette méthode ne devrait pas être appelée directement ; utiliser la méthode save pour y accéder.
	 *
	 * @param Entity $User
	 */
	abstract protected function updatePasswordAndMailOfUsercUsingId( Entity $User );
	
	/**
	 * Récupère un User à partir de son ID en base.
	 *
	 * @param $userc_id
	 *
	 * @return User
	 */
	abstract public function getUsercUsingUsercId( $userc_id );
	
	/**
	 * Récupère un User à partir de son login.
	 *
	 * @param $userc_login
	 *
	 * @return User
	 */
	abstract public function getUsercUsingUsercLogin( $userc_login );
	
	/**
	 * Bannit le User caractérisé par son ID.
	 *
	 * @param $userc_id     int ID du User
	 * @param $userc_banned int Raison du bannissement
	 */
	abstract public function updateUsercBannedUsingUsercId( $userc_id, $userc_banned );
	
	/**
	 * Compte le nombre d'utilisateurs enregistrés en base (inclut les administrateurs)
	 *
	 * @return int nombre d'utilisateurs enregistrés en base
	 */
	abstract public function countUsercUsingUsercId();
}