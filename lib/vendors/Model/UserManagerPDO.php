<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 07/10/2016
 * Time: 16:34
 */

namespace Model;


use Entity\User;
use OCFram\Entity;

class UserManagerPDO extends UserManager {
	/**
	 * Insère un utilisateur en base.
	 * Cette méthode ne devrait pas être appelée directement ; utiliser la méthode save pour y accéder.
	 *
	 * @param Entity $User
	 */
	protected function insertUserc(Entity $User) {
		/**
		 * @var $User User
		 * @var $Query \PDOStatement
		 */
		$sql = 'INSERT INTO T_SIT_userc (SUC_login, SUC_password, SUC_mail, SUC_DateSubscription, SUC_fk_SUY, SUC_fk_SUE_banned, SUC_fk_SUE_valid)
		VALUES (:login, :password, :mail, NOW(), '.User::USERY_STANDARD.', '.User::USERE_BANNED_NOT_BANNED.', '.User::USERE_VALID_VALIDATED_BY_FORM.' )';
		$Query = $this->dao->prepare($sql);
		$Query->bindValue(':login', $User->login());
		$Query->bindValue(':password', $User->password());
	}
	/**
	 * Met à jour un utilisateur en base.
	 * Cette méthode ne devrait pas être appelée directement ; utiliser la méthode save pour y accéder.
	 *
	 * @param Entity $User
	 */
	protected function updateUserc(Entity $User) {
		
	}
	
	/**
	 * Récupère un User à partir de son ID en base.
	 *
	 * @param $userc_id
	 *
	 * @return Entity
	 */
	public function getUsercUsingUsercId($userc_id) {
		
	}
	
	/**
	 * Bannit le User caractérisé par son ID.
	 *
	 * @param $userc_id int ID du User
	 * @param $userc_banned int Raison du bannissement
	 */
	public function updateUsercBannedUsingUsercId($userc_id , $userc_banned) {
		
	}
	
	/**
	 * Compte le nombre d'utilisateurs enregistrés en base (inclut les administrateurs)
	 *
	 * @return int nombre d'utilisateurs enregistrés en base
	 */
	public function countUsercUsingUsercId() {
		
	}
}