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
	const CRYPTED_PASSWORD_MATCH = '%^(\$6\$rounds=([0-9]){1,9}\$(.){1,16}\$)(.){1,}$%';
	const CRYPT_KEY_MATCH        = '%^(\$6\$rounds=([0-9]){1,9}\$(.){1,16}\$)%';
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
	 * @var $crypt_key string Clé de cryptage actuelle pour les mots de passe
	 */
	
	// Variables de stockage pour la confirmation lors de la création d'un User
	protected $password_confirm;
	protected $email_confirm;
	/**
	 * @var $crypt_key string Contient la clé de chiffrage actuelle
	 */
	static private $crypt_key;
	
	/**
	 * Permet de crypter un password à partir de la clé actuelle, ou de la clé passée en paramètre.
	 *
	 * @param $password  string
	 * @param $crypt_key string La clé à assigner. Si mise à null, utilise la clé courante.
	 *
	 * @return string
	 */
	static public function cryptWithKey( $password, $crypt_key = null ) {
		self::setCrypt_key( $crypt_key );
		
		return crypt( $password, self::$crypt_key );
	}
	
	/**
	 * Modifie la clé de cryptage à la clé passée en paramètre.
	 * Si la clé n'est pas donnée, elle est générée aléatoirement.
	 * Si la clé ne respecte pas le format de clé SHA_512, une exception est levée.
	 *
	 * @param $crypt_key string La clé à assigner
	 */
	static protected function setCrypt_key( $crypt_key = null ) {
		if ( null !== $crypt_key ) {
			if ( !preg_match( self::CRYPT_KEY_MATCH, $crypt_key ) ) {
				throw new \InvalidArgumentException( 'La clé de chiffrement SHA_512 est invalide.' );
			}
			else {
				self::$crypt_key = $crypt_key;
			}
		}
		else {
			if ( !isset( self::$crypt_key ) ) {
				self::$crypt_key = '$6$rounds=' . rand( 1, 99999 ) . '$' . 'abf_89{594' . '$';
			}
		}
	}
	
	public function __construct( array $values = array() ) {
		parent::__construct( $values );
		if ( isset( $values[ 'password_confirm' ] ) ) {
			$this->password_confirm = $values[ 'password_confirm' ];
		}
		if ( isset( $values[ 'email_confirm' ] ) ) {
			$this->email_confirm = $values[ 'email_confirm' ];
		}
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
	 * Vérifie si le password est crypté. La méthode de cryptage utilisée est SHA_512.
	 *
	 * @return bool
	 */
	public function isCrypted() {
		return preg_match( self::CRYPTED_PASSWORD_MATCH, $this->password );
	}
	
	/**
	 * Crypte le password courant. La méthode de cryptage utilisée est SHA_512.
	 * Le password n'est pas crypté : utiliser setPassword pour ce faire.
	 *
	 * @return string Le password crypté.
	 */
	public function crypt() {
		// Générer une clé de cryptage aléatoire
		// openssl_random_pseudo_bytes nécessite l'activation de la bibliothèque openssl dans le fichier de config
		// Attention à la fonction openssl (caractères spéciaux)
		// Ne pas augmenter la valeur max de rand
		if ( !$this->isCrypted() ) {
			return self::cryptWithKey( $this->password );
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
	 *
	 * @param $password string password NON crypté
	 */
	public function setPassword( $password ) {
		$this->password = $password;
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
	 * @return string
	 */
	public function password() {
		return $this->password;
	}
	
	/**
	 * @return string
	 */
	public function password_confirm() {
		return $this->password_confirm;
	}
	
	/**
	 * Renvoie la clé de cryptage utilisée pour un password, ou null si le password n'est pas crypté.
	 *
	 * @return string|null
	 */
	public function cryptKey() {
		preg_match( self::CRYPT_KEY_MATCH, $this->password, $matches_a );
		
		return isset( $matches_a[ 1 ] ) ? $matches_a[ 1 ] : null;
	}
	
	/**
	 * @return string
	 */
	public function email() {
		return $this->email;
	}
	
	/**
	 * @return string
	 */
	public function email_confirm() {
		return $this->email_confirm;
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