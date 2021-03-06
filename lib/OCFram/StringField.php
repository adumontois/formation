<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 14:33
 */

namespace OCFram;

/**
 * Class StringField
 * Classe représentant un champ de texte (input type="text").
 *
 * @package OCFram
 */
class StringField extends Field {
	/**
	 * @var $maxLength int Entier strictement positif, longueur maximale du champ
	 */
	protected $maxLength;
	
	/**
	 * StringField constructor.
	 * Ajoute automatiquement un validateur MaxLengthValidator.
	 *
	 * @param array $options
	 */
	public function __construct( array $options ) {
		parent::__construct( $options );
		if ( isset( $this->maxLength ) ) {
			$this->setValidators( array( new MaxLengthValidator( 'Specified ' . strtolower( $this->label ) . ' is too long (max = ' . $this->maxLength . ' characters', $this->maxLength ) ) );
		}
	}
	
	/**
	 * Construit le champ de texte.
	 *
	 * @return string Code HTML associé au champ de texte.
	 */
	public function buildWidget() {
		$code = '';
		/*if ( !empty( $this->errorMessage ) ) {
			$code = $this->errorMessage . '<br />';
		}*/
		$code .= '<label for="'.$this->name().'">' . $this->label . '</label>
            <input type = "text" name = "' . $this->name() . '" value = "' . htmlspecialchars( $this->value() ) . '" ';
		if ( !empty( $this->maxLength ) ) {
			$code .= 'maxlength = "' . $this->maxLength . '" ';
		}
		
		return $code . '/>';
	}
	
	/**
	 * Setter pour l'attribut maxLength.
	 *
	 * @param $maxLength int Entier strictement positif, longueur maximale du champ
	 */
	public function setMaxLength( $maxLength ) {
		if ( is_int( $maxLength ) AND $maxLength > 0 ) {
			$this->maxLength = $maxLength;
		}
		else {
			throw new \InvalidArgumentException( 'MaxLength must be a positive integer' );
		}
	}
}