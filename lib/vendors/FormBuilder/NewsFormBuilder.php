<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 15:59
 */

namespace FormBuilder;
use OCFram\FormBuilder;
use OCFram\MaxLengthValidator;
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
class NewsFormBuilder extends FormBuilder
{
	const AUTHOR_MAX_LENGTH = 50;
	const TEXTAREA_COLS = 50;
	const TEXTAREA_ROWS = 7;
	const TITLE_MAX_LENGTH = 255;
	
	/**
	 * Construit le formulaire de news.
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

        $this -> form -> add(new StringField(array('label' => 'Titre',
                                                    'name' => 'titre',
                                                    'maxLength' => self::TITLE_MAX_LENGTH,
                                                    'validators' => array(
                                                                        new MaxLengthValidator('Specified title is too long (max = '.self::TITLE_MAX_LENGTH.' characters)', self::TITLE_MAX_LENGTH),
                                                                        new NotNullValidator('Title can\'t be undefined'
                                                                        )))));
    }
}