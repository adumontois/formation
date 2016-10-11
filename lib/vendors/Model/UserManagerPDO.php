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
		 * @var $stmt \PDOStatement
		 */
		$sql = 'INSERT INTO T_SIT_userc (SUC_login, SUC_password, SUC_email, SUC_DateSubscription, SUC_fk_SUY, SUC_fk_SUE_banned, SUC_fk_SUE_valid)
				VALUES (:login, :password, :email, NOW(), :fk_SUY, :fk_SUE_banned, :fk_SUE_valid)';
		
		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':login', $User->login() );
		$stmt->bindValue( ':password', $User->password() );
		$stmt->bindValue( ':email', $User->email());
		$stmt->bindValue( ':fk_SUY', User::USERY_STANDARD, \PDO::PARAM_INT);
		$stmt->bindValue( ':fk_SUE_banned', User::USERE_BANNED_NOT_BANNED, \PDO::PARAM_INT);
		$stmt->bindValue( ':fk_SUE_valid', User::USERE_VALID_VALIDATED_BY_FORM, \PDO::PARAM_INT);
		$stmt->execute();
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
		 * @var $stmt \PDOStatement
		 */
		$sql   = 'UPDATE T_SIT_userc
				SET SUC_password = :password, SUC_email = :email
				WHERE SUC_id = :id';
		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':password', $User->password() );
		$stmt->bindValue( ':email', $User->email() );
		$stmt->bindValue( ':id', $User->id(), \PDO::PARAM_INT );
		$stmt->execute();
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
		 * @var $stmt \PDOStatement
		 * @var $User User
		 */
		$sql = 'SELECT SUC_id id, SUC_login login, SUC_password password, SUC_email email, SUC_DateSubscription datesubscription, SUC_fk_SUY fk_SUY, SUC_fk_SUE_banned fk_SUE_banned, SUC_fk_SUE_valid fk_SUE_valid
				FROM T_SIT_userc
				WHERE SUC_id = :id';
		
		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':id', $userc_id, \PDO::PARAM_INT );
		$stmt->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'Entity\User' );
		$stmt->execute();
		$User = $stmt->fetch();
		if (null != $User) {
			$User->setDatesubscription(new \DateTime($User->datesubscription()));
		}
		return $User;
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
		 * @var $stmt \PDOStatement
		 */
		$sql = 'SELECT SUC_id id, SUC_login login, SUC_password password, SUC_email email, SUC_DateSubscription datesubscription, SUC_fk_SUY fk_SUY, SUC_fk_SUE_banned fk_SUE_banned, SUC_fk_SUE_valid fk_SUE_valid
				FROM T_SIT_userc
				WHERE SUC_login = :login';
		
		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':login', $userc_login );
		$stmt->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'Entity\User' );
		$stmt->execute();
		$User = $stmt->fetch();
		if (null != $User) {
			$User->setDatesubscription(new \DateTime($User->datesubscription()));
		}
		return $stmt->rowCount() == 1 ? $User : null;
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
		 * @var $stmt \PDOStatement
		 */
		$sql = 'UPDATE T_SIT_userc
				SET SUC_fk_SUE_banned = :ban
				WHERE SUC_id = :id';
		
		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':id', $userc_id, \PDO::PARAM_INT );
		$stmt->bindValue( ':ban', $userc_banned, \PDO::PARAM_INT );
		$stmt->execute();
	}
	
	/**
	 * Compte le nombre d'utilisateurs enregistrés en base (inclut les administrateurs)
	 *
	 * @return int nombre d'utilisateurs enregistrés en base
	 */
	public function countUsercUsingUsercId() {
		$sql = 'SELECT COUNT(*)
				FROM T_SIT_userc';
		
		return $this->dao->query( $sql )->fetchColumn();
	}
}