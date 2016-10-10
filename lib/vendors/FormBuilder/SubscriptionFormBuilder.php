<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 10/10/2016
 * Time: 11:03
 */

namespace FormBuilder;


use Model\UserManager;
use OCFram\EmailField;
use OCFram\EmailValidator;
use OCFram\Entity;
use OCFram\ExistsValidator;
use OCFram\FormBuilder;
use OCFram\MinLengthValidator;
use OCFram\NotExistsValidator;
use OCFram\NotNullValidator;
use OCFram\PasswordField;
use OCFram\SameValueValidator;
use OCFram\StringField;

/**
 * Class SubscriptionFormBuilder
 *
 * Crée un formulaire d'inscription au site.
 *
 * @package FormBuilder
 */
class SubscriptionFormBuilder extends FormBuilder {
	const LOGIN_MIN_SIZE    = 5;
	const LOGIN_MAX_SIZE    = 50;
	const PASSWORD_MIN_SIZE = 8;
	const PASSWORD_MAX_SIZE = 50;
	const EMAIL_MAX_SIZE    = 100;
	/**
	 * @var UserManager $User_manager Manager de User pour faire des requêtes
	 */
	protected $User_manager;
	
	/**
	 * SubscriptionFormBuilder constructor.
	 *
	 * @param Entity      $entity
	 * @param UserManager $User_manager
	 */
	public function __construct( Entity $entity, UserManager $User_manager ) {
		parent::__construct( $entity );
		$this->User_manager = $User_manager;
	}
	
	public function build() {
		$this->form->add( new StringField( array(
			'label'      => 'Login',
			'maxLength'  => self::LOGIN_MAX_SIZE,
			'validators' => array(
				new NotNullValidator( 'Login can\'t be empty !' ),
				new MinLengthValidator( 'Login must have at least ' . self::LOGIN_MIN_SIZE . ' characters', self::LOGIN_MIN_SIZE ),
				new NotExistsValidator( 'Login already exists !', $this->User_manager, 'getUsercUsingUsercLogin' ),
			),
		) ) );
		$this->form->add( $Password_field = new PasswordField( array(
			'label'      => 'Mot de passe',
			'name'       => 'password',
			'maxLength'  => self::PASSWORD_MAX_SIZE,
			'validators' => array(
				new NotNullValidator( 'Password can\'t be empty !' ),
				new MinLengthValidator( 'Password must have at least ' . self::PASSWORD_MIN_SIZE . ' characters', self::PASSWORD_MIN_SIZE ),
			),
		) ) );
		$this->form->add( $A = new PasswordField( array(
			'label'      => 'Confirmez le mot de passe',
			'name'       => 'password_confirm',
			'maxLength'  => self::PASSWORD_MAX_SIZE,
			'validators' => array(
				new SameValueValidator( 'The confirmation password must be identical to the password !', $Password_field ),
			),
		) ) );
		$this->form->add( $Email_field = new EmailField( array(
			'label'      => 'Email',
			'maxLength'  => self::EMAIL_MAX_SIZE,
			'validators' => array(
				new NotNullValidator( 'Email can\'t be empty !' ),
				new EmailValidator( 'Email must be a valid email !' ),
			),
		) ) );
		$this->form->add( new EmailField( array(
			'label'      => 'Confirmez l\'email',
			'name'       => 'email_confirm',
			'maxLength'  => self::EMAIL_MAX_SIZE,
			'validators' => array(
				new SameValueValidator( 'The confirmation email must be identical to the email !', $Email_field ),
			),
		) ) );
	}
}