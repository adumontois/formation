<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 15:21
 */

namespace OCFram;

/**
 * Class NotNullValidator
 * Validateur qui vérifie la contrainte de non nullité d'un champ
 *
 * @package OCFram
 */
class NotNullValidator extends Validator {
	/**
	 * Vérifie si $value n'est pas une chaîne vide.
	 *
	 * @param $value string
	 *
	 * @return bool
	 */
	public function isValid( $value ) {
		return $value != '';
	}
}