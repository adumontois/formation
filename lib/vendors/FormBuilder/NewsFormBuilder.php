<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 15:59
 */

namespace FormBuilder;

use OCFram\Entity;
use OCFram\FormBuilder;
use OCFram\NotNullValidator;
use OCFram\StringField;
use OCFram\TextField;

/**
 * Class NewsFormBuilder
 *
 * Implémente un constructeur de formulaire pour les news.
 *
 * @package FormBuilder
 */
class NewsFormBuilder extends FormBuilder {
	const AUTHOR_MAX_LENGTH = 50;
	const TEXTAREA_COLS     = 50;
	const TEXTAREA_ROWS     = 7;
	const TITLE_MAX_LENGTH  = 255;
	
	/**
	 * NewsFormBuilder constructor.
	 *
	 * @param Entity $entity
	 */
	public function __construct( Entity $entity ) {
		parent::__construct( $entity );
	}
	
	/**
	 * Construit le formulaire de news.
	 */
	public function build() {
		$this->form->add( new StringField( array(
			'label'      => 'Auteur',
			'maxLength'  => self::AUTHOR_MAX_LENGTH,
			'validators' => array(
				new NotNullValidator( 'Author can\'t be unknown' ),
			),
		) ) );
		
		$this->form->add( new TextField( array(
			'label'      => 'Contenu',
			'rows'       => self::TEXTAREA_ROWS,
			'cols'       => self::TEXTAREA_COLS,
			'validators' => array(
				new NotNullValidator( 'Content can\'t be empty' ),
			),
		) ) );
		
		$this->form->add( new StringField( array(
			'label'      => 'Titre',
			'maxLength'  => self::TITLE_MAX_LENGTH,
			'validators' => array(
				new NotNullValidator( 'Title can\'t be undefined' ),
			),
		) ) );
	}
}