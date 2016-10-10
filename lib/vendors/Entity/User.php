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
	 * @var $Date_subscription \DateTime
	 */
	protected $Date_subscription;
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
	 * Permet de crypter un password à partir d'une clé spécifique passée en paramètre
	 *
	 * @param $password string
	 * @param $crypt_key string Clé de cryptage au format SHA_512
	 */
	static public function cryptWithKey($password, $crypt_key) {
		return crypt($password, $crypt_key);
	}
	
	
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
	
	/**
	 * Vérifie si le password est crypté. La méthode de cryptage utilisée est SHA_512.
	 *
	 * @return bool
	 */
	public function isCrypted() {
		return preg_match( '%^\$6\$rounds=([0-9]){1,9}\$[.]{16}\$[.]+$', $this->password);
	}
	
	/**
	 * Crypte le password courant. La méthode de cryptage utilisée est SHA_512.
	 * Le password n'est pas crypté : utiliser setPassword pour ce faire.
	 *
	 * @return string Le password crypté.
	 */
	public function crypt() {
		// Générer une clé de cryptage aléatoire
		if (!$this->isCrypted()) {
			$crypt_key = '$6$rounds=' . rand( 1, 999999999 ) . '$' . random_bytes( 16 ) . '$';
			return crypt( $this->password, $crypt_key );
		}
		
		// Retourner le champ si le password est déjà crypté.
		return $this->password;
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
		if ( $this->isCrypted() ) {
			if ( !empty( $password ) AND is_string( $password ) ) {
				$this->password = $this->crypt();
			}
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
	 * Setter pour l'attribut Date_subscription.
	 *
	 * @param \DateTime $Date_subscription
	 */
	public function setDate_subscription( \DateTime $Date_subscription ) {
		$this->Date_subscription = $Date_subscription;
	}
	
	/**
	 * Setter pour l'attribut type.
	 *
	 * @param $SUY_id int Tinyint associé à un type de user existant.
	 */
	public function setType( $SUY_id ) {
		if ( !is_int( $SUY_id ) OR $SUY_id < 0 OR $SUY_id > 255 ) {
			throw new \BadMethodCallException( 'Le type d\'un User doit être une entier entre 0 et 255' );
		}
		$this->type = $SUY_id;
	}
	
	/**
	 * Setter pour l'attribut banned.
	 *
	 * @param $SUE_id int Tinyint associé à un état de bannissement existant.
	 */
	public function setBanned( $SUE_id ) {
		if ( !is_int( $SUE_id ) OR $SUE_id < 1 OR $SUE_id > 19 ) {
			throw new \BadMethodCallException( 'L\'état de bannissement d\'un User doit être une entier entre 1 et 19' );
		}
		$this->type = $SUE_id;
	}
	
	/**
	 * Setter pour l'attribut valid.
	 *
	 * @param $SUE_id int Tinyint associé à un état de validation existant.
	 */
	public function setValid( $SUE_id ) {
		if ( !is_int( $SUE_id ) OR $SUE_id < 21 OR $SUE_id > 39 ) {
			throw new \BadMethodCallException( 'L\'état de validation d\'un User doit être une entier entre 21 et 39' );
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
		if ( !$this->isCrypted() ) {
			return $this->crypt();
		}
		return $this->password;
	}
	
	/**
	 * Renvoie la clé de cryptage utilisée pour un password, ou null si le password n'est pas crypté.
	 *
	 * @return string|null
	 */
	public function cryptKey() {
		$matches_a = preg_match( '%(^\$6\$rounds=([0-9]){1,9}\$[.]{16}\$)[.]+$', $this->password);
		return isset($matches_a[1]) ? $matches_a[1] : null;
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
	public function Date_subscription() {
		return $this->Date_subscription;
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
	public function banned() {
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