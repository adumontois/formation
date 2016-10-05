<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 15:49
 */

namespace OCFram;


abstract class FormBuilder {
	/**
	 * @var $form Form
	 */
	protected $form;
	
	/**
	 * Construit un formulaire vide à partir d'une entité objet.
	 *
	 * @param Entity $entity
	 */
	public function __construct( Entity $entity ) {
		$this->setForm( new Form( $entity ) );
	}
	
	/**
	 * Remplit le formulaire avec tous les objets Field qu'il contient.
	 *
	 * @return void
	 */
	abstract public function build();
	
	/**
	 * @return Form
	 */
	public function form() {
		return $this->form;
	}
	
	/**
	 * Setter pour l'attribut form.
	 *
	 * @param Form $form
	 */
	public function setForm( Form $form ) {
		$this->form = $form;
	}
}