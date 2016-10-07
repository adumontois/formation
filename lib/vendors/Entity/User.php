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
	const INVALID_LOGIN  = 1;
	const INVALID_PASSWORD   = 2;
	const INVALID_EMAIL = 3;
	
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
	 * Setter pour l'attribut login.
	 *
	 * @param $login string
	 */
	public function setLogin( $login ) {
		if ( !empty( $login ) AND is_string( $login ) ) {
			$this->login = $login;
		}
		else {
			$this->erreurs[] = self::INVALID_LOGIN;
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
		else {
			$this->erreurs[] = self::INVALID_PASSWORD;
		}
	}
	
	/**
	 * Setter pour l'attribut email.
	 *
	 * @param $email string
	 */
	public function setEmail( $email ) {
		if (filter_var($email, FILTER_VALIDATE_EMAIL) ) {
			$this->email = $email;
		}
		else {
			$this->erreurs[] = self::INVALID_EMAIL;
		}
	}
	
	/**
	 * Setter pour l'attribut DateAjout.
	 *
	 * @param \DateTime $DateAjout
	 */
	public function setDateAjout( \DateTime $DateAjout ) {
		$this->DateAjout = $DateAjout;
	}
	
	/**
	 * Setter pour l'attribut DateModif.
	 *
	 * @param \DateTime $DateModif
	 */
	public function setDateModif( \DateTime $DateModif ) {
		$this->DateModif = $DateModif;
	}
	
	/**
	 * @return string
	 */
	public function auteur() {
		return $this->auteur;
	}
	
	/**
	 * @return string
	 */
	public function titre() {
		return $this->titre;
	}
	
	/**
	 * @return string
	 */
	public function contenu() {
		return $this->contenu;
	}
	
	/**
	 * @return \DateTime
	 */
	public function DateAjout() {
		return $this->DateAjout;
	}
	
	/**
	 * @return \DateTime
	 */
	public function DateModif() {
		return $this->DateModif;
	}
}