<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 07/10/2016
 * Time: 15:21
 */

namespace Entity;


use App\Traits\Password;
use OCFram\Entity;

class User extends Entity {
	use Password;
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