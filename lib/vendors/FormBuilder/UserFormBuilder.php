<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 10/10/2016
 * Time: 11:03
 */

namespace FormBuilder;


use OCFram\EmailField;
use OCFram\EmailValidator;
use OCFram\NotNullValidator;

class UserFormBuilder extends ConnexionFormBuilder  {
	const LOGIN_MIN_SIZE    = 5;
	const LOGIN_MAX_SIZE    = 50;
	const PASSWORD_MIN_SIZE = 8;
	const PASSWORD_MAX_SIZE = 50;
	const EMAIL_MAX_SIZE    = 100;
	
	public function build() {
		parent::build();
		$this->form->add( new EmailField( array(
			'label'      => 'Email',
			'maxLength'  => self::EMAIL_MAX_SIZE,
			'validators' => array(
				new NotNullValidator( 'Email can\'t be empty !' ),
				new EmailValidator( 'Email must be a valid email !' ),
			),
		) ) );
	}
}