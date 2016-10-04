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


class NewsFormBuilder extends FormBuilder
{
    public function build()
    {
        $this -> form -> add(new StringField(array('label' => 'Auteur',
                                                    'name' => 'auteur',
                                                    'maxLength' => 50,
                                                    'validators' => array(
                                                                        new MaxLengthValidator('Specified author is too long (max = 50 characters)', 50),
                                                                        new NotNullValidator('Author can\'t be unknown'
                                                                        )))));

        $this -> form -> add(new TextField(array('label' => 'Contenu',
                                                    'name' => 'contenu',
                                                    'rows' => 7,
                                                    'cols' => 50,
                                                    'validators' => array(new NotNullValidator('Content can\'t be empty'
                                                                            )))));

        $this -> form -> add(new StringField(array('label' => 'Titre',
                                                    'name' => 'titre',
                                                    'maxLength' => 255,
                                                    'validators' => array(
                                                                        new MaxLengthValidator('Specified title is too long (max = 255 characters)', 255),
                                                                        new NotNullValidator('Title can\'t be undefined'
                                                                        )))));
    }
}