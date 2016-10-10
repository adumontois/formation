<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 10/10/2016
 * Time: 11:10
 */

namespace OCFram;

/**
 * Class EmailField
 *
 * Définit un champ email
 *
 * @package OCFram
 */
class EmailField extends StringField {
	/**
	 * EmailField constructor.
	 * Ajoute automatiquement un validateur EmailValidator.
	 *
	 * @param array $options
	 */
	public function __construct( array $options ) {
		parent::__construct( $options );
		$this->setValidators( array( new EmailValidator( 'Specified ' . strtolower( $this->label ) . ' is not an email') ) );
	}
	
	/**
	 * Construit le champ de texte.
	 *
	 * @return string Code HTML associé au champ de texte.
	 */
	public function buildWidget() {
		return str_replace('type = "text"', 'type = "email"', parent::buildWidget());
	}
}