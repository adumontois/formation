<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 15:53
 */

namespace FormBuilder;

use OCFram\FormBuilder;
use OCFram\MaxLengthValidator;
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
class CommentFormBuilder extends FormBuilder
{
	const AUTHOR_MAX_LENGTH = 50;
	const TEXTAREA_COLS = 50;
	const TEXTAREA_ROWS = 7;
	
	/**
	 * Construit le formulaire de commentaires.
	 */
    public function build()
    {
        $this -> form -> add(new StringField(array('label' => 'Auteur',
                                                    'name' => 'auteur',
                                                    'maxLength' => self::AUTHOR_MAX_LENGTH,
                                                    'validators' => array(
                                                                        new MaxLengthValidator('Specified author is too long (max = '.self::AUTHOR_MAX_LENGTH.' characters)', self::AUTHOR_MAX_LENGTH),
                                                                        new NotNullValidator('Author can\'t be unknown'
                                                                        )))));
        $this -> form -> add(new TextField(array('label' => 'Contenu',
                                                    'name' => 'contenu',
                                                    'rows' => self::TEXTAREA_ROWS,
                                                    'cols' => self::TEXTAREA_COLS,
                                                    'validators' => array(new NotNullValidator('Content can\'t be empty'
                                                                        )))));
    }
}