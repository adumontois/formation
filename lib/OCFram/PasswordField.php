<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 10/10/2016
 * Time: 11:10
 */

namespace OCFram;

/**
 * Class PasswordField
 *
 * Définit un champ password.
 *
 * @package OCFram
 */
class PasswordField extends StringField {
	/**
	 * Construit le champ de texte.
	 *
	 * Le champ est effacé à chaque fois que le formulaire est relancé.
	 *
	 * @return string Code HTML associé au champ de texte.
	 */
	public function buildWidget() {
		$code = '';
		if ( !empty( $this->errorMessage ) ) {
			$code = $this->errorMessage . '<br />';
		}
		$code .= '<label>' . $this->label . '</label>
            <input type = "password" name = "' . $this->name() . '" value = "" ';
		if ( !empty( $this->maxLength ) ) {
			$code .= 'maxlength = "' . $this->maxLength . '" ';
		}
		
		return $code . '/>';
	}
}