<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 10/10/2016
 * Time: 14:54
 */

namespace OCFram;


class SameValueValidator extends Validator {
	/**
	 * @var Field $First_value Le champ à comparer
	 */
	protected $First_field;
	
	public function __construct( $errorMessage, Field $First_field ) {
		parent::__construct( $errorMessage );
		$this->First_field = $First_field;
	}
	
	public function isValid($value) {
		return $value === $this->First_field->value();
	}
	
	
}