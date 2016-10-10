<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 10/10/2016
 * Time: 13:18
 */

namespace FormBuilder;

use OCFram\FormBuilder;
use OCFram\MinLengthValidator;
use OCFram\NotNullValidator;
use OCFram\PasswordField;
use OCFram\StringField;

class ConnexionFormBuilder extends FormBuilder{
	const LOGIN_MIN_SIZE    = 5;
	const LOGIN_MAX_SIZE    = 50;
	const PASSWORD_MIN_SIZE = 8;
	const PASSWORD_MAX_SIZE = 50;
	
	public function build() {
		$this->form->add( new StringField( array(
			'label'      => 'Login',
			'maxLength'  => self::LOGIN_MAX_SIZE,
			'validators' => array(
				new NotNullValidator( 'Login can\'t be empty !' ),
				new MinLengthValidator( 'Login must have at least ' . self::LOGIN_MIN_SIZE . ' characters', self::LOGIN_MIN_SIZE ),
			),
		) ) );
		$this->form->add( new PasswordField( array(
			'label'      => 'Mot de passe',
			'name'       => 'password',
			'maxLength'  => self::PASSWORD_MAX_SIZE,
			'validators' => array(
				new NotNullValidator( 'Password can\'t be empty !' ),
				new MinLengthValidator( 'Password must have at least ' . self::PASSWORD_MIN_SIZE . ' characters', self::PASSWORD_MIN_SIZE ),
			),
		) ) );
	}
}

