<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 17/10/2016
 * Time: 12:25
 */

namespace FormBuilder;


use OCFram\NotNullValidator;
use OCFram\StringField;

/**
 * Class CommentFormBuilderWithAuthor
 *
 * Formulaire de commentaires avec auteur.
 *
 * @package FormBuilder
 */
class CommentFormBuilderWithAuthor extends CommentFormBuilder {
	/**
	 * Construit un formulaire de commentaires avec auteur.
	 */
	public function build() {
		$this->form->add( new StringField( array(
			'label'      => 'Auteur',
			'name'       => 'author',
			'maxLength'  => self::AUTHOR_MAX_LENGTH,
			'validators' => array(
				new NotNullValidator( 'Le nom de l\'auteur doit être précisé.' ),
			),
		) ) );
	    parent::build();
	}
}