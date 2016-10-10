<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 10/10/2016
 * Time: 11:03
 */

namespace FormBuilder;


use OCFram\FormBuilder;
use OCFram\StringField;

class UserFormBuilder extends FormBuilder {
	public function build() {
		$this->form->add( new StringField( array( 'label' => 'Login' ) ) );
		$this->form->add( new PasswordField( array(
			'label' => 'Mot de passe',
			'name'  => 'password',
		) ) );
		$this->form->add( new PasswordField( array(
			'label' => 'Confirmez le mot de passe',
			'name'  => 'password_confirm',
		) ) );
		$this->form->add( new EmailField( array(
			'label' => 'Email',
		) ) );
		$this->form->add( new EmailField( array(
			'label' => 'Confirmez l\'email',
			'name'  => 'email_confirm',
		) ) );
	}
}