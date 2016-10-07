<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 07/10/2016
 * Time: 15:21
 */

namespace Entity;


use OCFram\Entity;

class User extends Entity {
	/**
	 * State constants
	 */
	const USERE_BANNED_NOT_BANNED       = 1;
	const USERE_BANNED_BANNED_FOR_FLOOD = 2;
	/**
	 * Type constants
	 */
	const USERY_STANDARD   = 1;
	const USERY_SUPERADMIN = 2;
	/**
	 * @var $login string
	 */
	protected $login;
	/**
	 * Le password est géré en non-crypté ; il est stocké en base crypté.
	 *
	 * @var $password string
	 */
	protected $password;
	/**
	 * @var $email string
	 */
	protected $email;
	/**
	 * @var $DateSubscription \DateTime
	 */
	protected $DateSubscription;
	
	/**
	 * Vérifie si l'utilisateur est valide.
	 * Les vérifications s'ajoutent aux vérifications effectuées dans le formulaire de création/mise à jour de Entity\User
	 *
	 * @return bool
	 */
	public function isValid() {
		return !empty( $this->login ) AND !empty( $this->password ) AND !empty( $this->email );
	}
	
	/**
	 * Crypte le password courant. La méthode de cryptage utilisée est SHA_512.
	 */
	public function crypt() {
		$this->setPassword( crypt( $this->password, '$6$rounds=457312984$p@__{#5h£y|+7G*-$' ) );
	}
	
	/**
	 * Setter pour l'attribut login.
	 *
	 * @param $login string
	 */
	public function setLogin( $login ) {
		if ( !empty( $login ) AND is_string( $login ) ) {
			$this->login = $login;
		}
	}
	
	/**
	 * Setter pour l'attribut password.
	 *
	 * @param $password string
	 */
	public function setPassword( $password ) {
		if ( !empty( $password ) AND is_string( $password ) ) {
			$this->password = $password;
		}
	}
	
	/**
	 * Setter pour l'attribut email.
	 *
	 * @param $email string
	 */
	public function setEmail( $email ) {
		if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			$this->email = $email;
		}
	}
	
	/**
	 * Setter pour l'attribut DateSubscription.
	 *
	 * @param \DateTime $DateSubscription
	 */
	public function setDateSubscription( \DateTime $DateSubscription ) {
		$this->DateSubscription = $DateSubscription;
	}
	
	/**
	 * @return string
	 */
	public function login() {
		return $this->login;
	}
	
	/**
	 * @return string
	 */
	public function password() {
		return $this->password;
	}
	
	/**
	 * @return string
	 */
	public function email() {
		return $this->email;
	}
	
	/**
	 * @return \DateTime
	 */
	public function DateSubscription() {
		return $this->DateSubscription;
	}
}