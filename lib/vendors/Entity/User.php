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
	const USERE_VALID_VALIDATED_BY_FORM = 21;
	/**
	 * Type constants
	 */
	const USERY_STANDARD   = 1;
	const USERY_SUPERADMIN = 2;
	/** Password constant */
	const CRYPT_KEY = '$6$rounds=457312984$p@__{#5h£y|+7G*-$';
	/**
	 * @var $login string
	 */
	protected $login;
	/**
	 * Le password est géré crypté ; il est stocké en base crypté.
	 *
	 * @var $password string
	 */
	private $password;
	/**
	 * @var $email string
	 */
	protected $email;
	/**
	 * @var $DateSubscription \DateTime
	 */
	protected $DateSubscription;
	/**
	 * @var $type int Type de l'utilisateur (standard, admin, superadmin...)
	 */
	protected $type;
	/**
	 * @var $banned int Indique si l'utilisateur est banni
	 */
	protected $banned;
	/**
	 * @var $valid int Indique si l'utilisateur a été validé
	 */
	protected $valid;
	
	/**
	 * Vérifie si les coordonnées de l'utilisateur est valide.
	 * Les vérifications s'ajoutent aux vérifications effectuées dans le formulaire de création/mise à jour de Entity\User
	 *
	 * /!\ Ne concerne pas le champ valid !
	 *
	 * @return bool
	 */
	public function isValid() {
		return !empty( $this->login ) AND $this->isCrypted() AND !empty( $this->email );
	}
	
	/**
	 * Vérifie si le password donné est crypté ou non.
	 * On considère qu'un password est crypté s'il commence par la clé de cryptage
	 * (qui est suffisamment longue et complexe pour faire cette approximation,
	 * d'autant qu'il n'existe pas une telle méthode...).
	 *
	 * /!\ Il ne faudrait JAMAIS modifier la clé de cryptage !
	 *
	 * @return bool
	 */
	public function isCrypted() {
		return  self::CRYPT_KEY === substr($this->password, 0, strlen(self::CRYPT_KEY));
	}
	
	/**
	 * Crypte le password courant. La méthode de cryptage utilisée est SHA_512.
	 */
	public function crypt() {
		return crypt( $this->password, self::CRYPT_KEY );
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
	 * Stocke le password directement crypté.
	 *
	 * @param $password string password NON crypté
	 */
	public function setPassword( $password ) {
		$this->password = $password;
		// Si le password n'est pas déjà crypté, on le crypte
		if ($this->isCrypted())
			if ( !empty( $password ) AND is_string( $password ) ) {
				$this->password = $this->crypt();
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
	 * Setter pour l'attribut type.
	 *
	 * @param $SUY_id int Tinyint associé à un type de user existant.
	 */
	public function setType($SUY_id) {
		if (!is_int($SUY_id) OR $SUY_id < 0 OR $SUY_id > 255) {
			throw new \BadMethodCallException('Le type d\'un User doit être une entier entre 0 et 255');
		}
		$this->type = $SUY_id;
	}
	
	/**
	 * Setter pour l'attribut banned.
	 *
	 * @param $SUE_id int Tinyint associé à un état de bannissement existant.
	 */
	public function setBanned($SUE_id) {
		if (!is_int($SUE_id) OR $SUE_id < 1 OR $SUE_id > 19) {
			throw new \BadMethodCallException('L\'état de bannissement d\'un User doit être une entier entre 1 et 19');
		}
		$this->type = $SUE_id;
	}
	
	/**
	 * Setter pour l'attribut valid.
	 *
	 * @param $SUE_id int Tinyint associé à un état de validation existant.
	 */
	public function setValid($SUE_id) {
		if (!is_int($SUE_id) OR $SUE_id < 21 OR $SUE_id > 39) {
			throw new \BadMethodCallException('L\'état de validation d\'un User doit être une entier entre 21 et 39');
		}
		$this->type = $SUE_id;
	}
	
	/**
	 * @return string
	 */
	public function login() {
		return $this->login;
	}
	
	/**
	 * Renvoie le password CRYPTE (pour des raisons de sécurité évidentes).
	 *
	 * @return string
	 */
	public function password() {
		if (!$this->isCrypted()) {
			return $this->crypt();
		}
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
	
	/**
	 * @return int
	 */
	public function type() {
		return $this->type;
	}
	
	/**
	 * @return int
	 */
	public function	banned() {
		return $this->banned;
	}
	
	/**
	 * Accesseur pour l'attribut valid.
	 *
	 * /!\ N'effectue pas la vérification de validité d'un champ lors de l'acces
	 *
	 * @return int
	 */
	public function valid() {
		return $this->valid;
	}
}