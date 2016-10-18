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
	const USERY_STANDARD         = 1;
	const USERY_SUPERADMIN       = 2;
	const CRYPTED_PASSWORD_MATCH = '%^(\$6\$rounds=([0-9]){1,9}\$(.){1,16}\$)(.){1,}$%';
	const CRYPT_KEY_MATCH        = '%^(\$6\$rounds=([0-9]){1,9}\$(.){1,16}\$)%';
	/**
	 * @var $login string
	 */
	protected $login;
	/**
	 * Le password est stocké en base crypté.
	 *
	 * @var $password string
	 */
	private $password;
	/**
	 * @var $email string
	 */
	protected $email;
	/**
	 * @var $datesubscription \DateTime
	 */
	protected $datesubscription;
	/**
	 * @var $type int Type de l'utilisateur (standard, admin, superadmin...)
	 */
	protected $fk_SUY;
	/**
	 * @var $banned int Indique si l'utilisateur est banni
	 */
	protected $fk_SUE_banned;
	/**
	 * @var $valid int Indique si l'utilisateur a été validé
	 */
	protected $fk_SUE_valid;
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
	
	/**
	 * Récupère le texte descriptif d'un type de User.
	 *
	 * @param $fk_SUY int Type du User en chiffre ($this->Fk_SUY)
	 *
	 * @return string
	 */
	static public function getTextualStatus( $fk_SUY ) {
		switch ( (int) $fk_SUY ) {
			case 1 :
				return 'simple écrivain';
			case 2 :
				return 'superadmin';
			default :
				throw new \InvalidArgumentException( 'Le type de User ' . $fk_SUY . ' n\'existe pas !' );
		}
	}
	
	/**
	 * Retourne un état de validité formaté à partir de l'état passé en paramètre.
	 *
	 * @param int $fk_SUE_valid
	 *
	 * @return string Etat de validité formaté
	 */
	static public function getTextualValid( $fk_SUE_valid ) {
		switch ( (int)$fk_SUE_valid ) {
			case ( User::USERE_VALID_VALIDATED_BY_FORM ):
				return "validé par le formulaire";
			default:
				throw new \InvalidArgumentException( 'L\'état ' . $fk_SUE_valid . 'n\'a pas de signification pour l\'attribut de validité.' );
		}
	}
	
	/**
	 * Retourne un état de bannissement formaté à partir de l'état passé en paramètre.
	 *
	 * @param int $fk_SUE_banned
	 *
	 * @return string Etat de bannissement formaté
	 */
	static public function getTextualBanned( $fk_SUE_banned ) {
		switch ( (int)$fk_SUE_banned ) {
			case ( User::USERE_BANNED_NOT_BANNED):
				return "en activité";
			case (User::USERE_BANNED_BANNED_FOR_FLOOD):
				return "banni pour flood";
			default:
				throw new \InvalidArgumentException( 'L\'état ' . $fk_SUE_banned . 'n\'a pas de signification pour l\'attribut de bannissement.' );
		}
	}
	
	/**
	 * Construit le user en l'hydratant. Si les valeurs password_confirm et email_confirm sont passées, elles sont settées manuellement.
	 *
	 * @param array $values Tableau paramètre-valeur du User à instancier.
	 */
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
	 * Conversion en string d'un User
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->login() . ' (' . $this->email() . ')';
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
		return !empty( $this->login ) AND !empty( $this->email );
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
	 * Setter pour l'attribut datesubscription.
	 *
	 * @param \DateTime $datesubscription
	 */
	public function setDatesubscription( \DateTime $datesubscription ) {
		$this->datesubscription = $datesubscription;
	}
	
	/**
	 * Setter pour l'attribut fk_SUY.
	 *
	 * @param $fk_SUY int Tinyint associé à un type de user existant.
	 */
	public function setFk_SUY( $fk_SUY ) {
		if ( !is_int( $fk_SUY ) OR $fk_SUY < 0 OR $fk_SUY > 255 ) {
			throw new \InvalidArgumentException( 'Le type d\'un User doit être une entier entre 0 et 255' );
		}
		$this->fk_SUY = $fk_SUY;
	}
	
	/**
	 * Setter pour l'attribut $fk_SUE_banned.
	 *
	 * @param $fk_SUE_banned int Tinyint associé à un état de bannissement existant.
	 */
	public function setFk_SUE_banned( $fk_SUE_banned ) {
		if ( !is_int( $fk_SUE_banned ) OR $fk_SUE_banned < 1 OR $fk_SUE_banned > 19 ) {
			throw new \BadMethodCallException( 'L\'état de bannissement d\'un User doit être une entier entre 1 et 19' );
		}
		$this->fk_SUE_banned = $fk_SUE_banned;
	}
	
	/**
	 * Setter pour l'attribut fk_SUE_valid.
	 *
	 * @param $fk_SUE_valid int Tinyint associé à un état de validation existant.
	 */
	public function setFk_SUE_valid( $fk_SUE_valid ) {
		if ( !is_int( $fk_SUE_valid ) OR $fk_SUE_valid < 21 OR $fk_SUE_valid > 39 ) {
			throw new \InvalidArgumentException( 'L\'état de validation d\'un User doit être une entier entre 21 et 39' );
		}
		$this->fk_SUE_valid = $fk_SUE_valid;
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
	public function datesubscription() {
		return $this->datesubscription;
	}
	
	/**
	 * @return int
	 */
	public function fk_SUY() {
		return $this->fk_SUY;
	}
	
	/**
	 * @return int
	 */
	public function fk_SUE_banned() {
		return $this->fk_SUE_banned;
	}
	
	/**
	 * Accesseur pour l'attribut valid.
	 *
	 * /!\ N'effectue pas la vérification de validité d'un champ lors de l'acces
	 *
	 * @return int
	 */
	public function fk_SUE_valid() {
		return $this->fk_SUE_valid;
	}
	
	/**
	 * Formate les dates pour affichage dans une vue. Cette méthode modifie la valeur de l'attribut datesubscription
	 */
	public function formatDate() {
		$this->datesubscription = $this->datesubscription->format('d/m/Y');
	}
}