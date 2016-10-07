<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 07/10/2016
 * Time: 17:24
 */

namespace App\Traits;

/**
 * Trait Password
 *
 * Trait donnant les méthodes de gestion d'un password.
 * /!\ Il ne faudrait JAMAIS changer la clé de cryptage.
 *
 * @package App\Traits
 */
trait Password {
	/**
	 * Le password est géré crypté ; il est stocké en base crypté.
	 *
	 * @var $password string
	 */
	private $password;
	
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
		return  '$6$rounds=457312984$p@__{#5h£y|+7G*-$' === substr($this->password, 0, strlen('$6$rounds=457312984$p@__{#5h£y|+7G*-$'));
	}
	
	/**
	 * Crypte le password courant. La méthode de cryptage utilisée est SHA_512.
	 */
	public function crypt() {
		return crypt( $this->password, '$6$rounds=457312984$p@__{#5h£y|+7G*-$' );
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
}