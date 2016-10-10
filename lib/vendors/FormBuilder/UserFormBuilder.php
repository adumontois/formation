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
use OCFram\FormBuilder;
use OCFram\MinLengthValidator;
use OCFram\NotNullValidator;
use OCFram\PasswordField;
use OCFram\StringField;

class UserFormBuilder extends FormBuilder {
	const LOGIN_MIN_SIZE    = 5;
	const LOGIN_MAX_SIZE    = 50;
	const PASSWORD_MIN_SIZE = 8;
	const PASSWORD_MAX_SIZE = 50;
	const EMAIL_MAX_SIZE    = 100;
	
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
		/*$this->form->add( new PasswordField( array(
			'label' => 'Confirmez le mot de passe',
			'name'  => 'password',
		) ) );*/
		$this->form->add( new EmailField( array(
			'label'      => 'Email',
			'maxLength'  => self::EMAIL_MAX_SIZE,
			'validators' => array(
				new NotNullValidator( 'Email can\'t be empty !' ),
				new EmailValidator( 'Email must be a valid email !' ),
			),
		) ) );
		/*$this->form->add( new EmailField( array(
			'label' => 'Confirmez l\'email',
			'name'  => 'email',
		) ) );*/
	}
}