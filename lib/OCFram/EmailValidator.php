<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 10/10/2016
 * Time: 11:21
 */

namespace OCFram;

/**
 * Class EmailValidator
 *
 * Validateur qui vérifie si un champ est un e-mail.
 *
 * @package OCFram
 */
class EmailValidator extends Validator {
	/**
	 * Vérifie si le champ est un e-mail.
	 *
	 * @param string $value
	 *
	 * @return bool
	 */
	public function isValid( $value ) {
		return filter_var( $value, FILTER_VALIDATE_EMAIL ) !== false;
	}
}