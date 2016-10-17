<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 15:53
 */

namespace FormBuilder;

use OCFram\FormBuilder;
use OCFram\NotNullValidator;
use OCFram\StringField;
use OCFram\TextField;

/**
 * Class CommentFormBuilder
 *
 * Construit un formulaire pour les commentaires.
 *
 * @package FormBuilder
 */
class CommentFormBuilder extends FormBuilder {
	const AUTHOR_MAX_LENGTH = 50;
	const TEXTAREA_COLS     = 50;
	const TEXTAREA_ROWS     = 7;
	
	/**
	 * Construit le formulaire de commentaires.
	 */
	public function build() {
		$this->form->add( new TextField( array(
			'label'      => 'Contenu',
			'name'       => 'content',
			'rows'       => self::TEXTAREA_ROWS,
			'cols'       => self::TEXTAREA_COLS,
			'validators' => array(
				new NotNullValidator( 'Vous ne pouvez pas envoyer un message vide !' ),
			),
		) ) );
	}
}