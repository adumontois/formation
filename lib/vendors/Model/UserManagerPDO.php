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
	protected function insertUserc( Entity $User ) {
		/**
		 * @var $User  User
		 * @var $Query \PDOStatement
		 */
		$sql   = 'INSERT INTO T_SIT_userc (SUC_login, SUC_password, SUC_mail, SUC_DateSubscription, SUC_fk_SUY, SUC_fk_SUE_banned, SUC_fk_SUE_valid)
		VALUES (:login, :password, :mail, NOW(), ' . User::USERY_STANDARD . ', ' . User::USERE_BANNED_NOT_BANNED . ', ' . User::USERE_VALID_VALIDATED_BY_FORM . ' )';
		$Query = $this->dao->prepare( $sql );
		$Query->bindValue( ':login', $User->login() );
		$Query->bindValue( ':password', $User->password() );
	}
	
	/**
	 * Met à jour le password et le mail d'un utilisateur en base.
	 * Cette méthode ne devrait pas être appelée directement ; utiliser la méthode save pour y accéder.
	 *
	 * @param Entity $User
	 */
	protected function updatePasswordAndMailOfUsercUsingId( Entity $User ) {
		/**
		 * @var $User  User
		 * @var $Query \PDOStatement
		 */
		$sql   = 'UPDATE T_SIT_userc
				SET SUC_password = :password, SUC_mail = :mail
				WHERE SUC_id = :id';
		$Query = $this->dao->prepare( $sql );
		$Query->bindValue( ':password', $User->password() );
		$Query->bindValue( ':mail', $User->email() );
		$Query->bindValue( ':id', $User->id(), \PDO::PARAM_INT );
		$Query->execute();
	}
	
	/**
	 * Récupère un User à partir de son ID en base.
	 *
	 * @param $userc_id
	 *
	 * @return Entity
	 */
	public function getUsercUsingUsercId( $userc_id ) {
		/**
		 * @var $Query \PDOStatement
		 */
		$sql   = 'SELECT SUC_id id, SUC_login login, SUC_password password, SUC_mail email, SUC_DateSubscription Date_subscription, SUC_fk_SUY type, SUC_fk_SUE_banned banned, SUC_fk_SUE_valid valid
			FROM T_SIT_userc
			WHERE SUC_id = :id';
		$Query = $this->dao->prepare( $sql );
		$Query->bindValue( ':id', $userc_id, \PDO::PARAM_INT );
		$Query->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'Entity\User' );
		$Query->execute();
		return $Query->fetch();
	}
	
	/**
	 * Récupère un User à partir de son login.
	 * Si 2 users ont le même login, la fonction renvoie NULL.
	 * Si le user n'existe pas, la fonction renvoie NULL.
	 *
	 * @param $userc_login
	 *
	 * @return User|null
	 */
	public function getUsercUsingUsercLogin( $userc_login ) {
		/**
		 * @var $Query \PDOStatement
		 */
		$sql   = 'SELECT SUC_id id, SUC_login login, SUC_password password, SUC_mail email, SUC_DateSubscription Date_subscription, SUC_fk_SUY type, SUC_fk_SUE_banned banned, SUC_fk_SUE_valid valid
			FROM T_SIT_userc
			WHERE SUC_login = :login';
		$Query = $this->dao->prepare( $sql );
		$Query->bindValue( ':id', $userc_login);
		$Query->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'Entity\User' );
		$Query->execute();
		$User = $Query->fetch();
		return $Query->rowCount() == 1 ? $User : null;
	}
	
	/**
	 * Bannit le User caractérisé par son ID.
	 *
	 * @param $userc_id     int ID du User
	 * @param $userc_banned int Raison du bannissement
	 */
	public function updateUsercBannedUsingUsercId( $userc_id, $userc_banned ) {
		/**
		 * @var $User  User
		 * @var $Query \PDOStatement
		 */
		$sql   = 'UPDATE T_SIT_userc
				SET SUC_fk_SUE_banned = :ban
				WHERE SUC_id = :id';
		$Query = $this->dao->prepare( $sql );
		$Query->bindValue( ':id', $userc_id, \PDO::PARAM_INT );
		$Query->bindValue( ':ban', $userc_banned, \PDO::PARAM_INT );
		$Query->execute();
	}
	
	/**
	 * Compte le nombre d'utilisateurs enregistrés en base (inclut les administrateurs)
	 *
	 * @return int nombre d'utilisateurs enregistrés en base
	 */
	public function countUsercUsingUsercId() {
		$sql = 'SELECT COUNT(*)
			FROM T_SIT_userc';
		return $this->dao->exec($sql)->fetchColumn();
	}
}