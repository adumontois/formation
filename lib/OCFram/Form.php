<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 12:39
 */

namespace OCFram;

/**
 * Class Form
 *
 * Représente le contenu d'un formulaire. Le formulaire est associé à une \OCFram\Entity et contient un tableau de \OCFram\Fields.
 *
 * @package OCFram
 */
class Form {
	/**
	 * @var $entity Entity
	 */
	protected $entity;
	/**
	 * @var $fields Field[]
	 */
	protected $fields;
	
	/**
	 * Form constructor.
	 *
	 * @param Entity $entity
	 */
	public function __construct( Entity $entity ) {
		$this->setEntity( $entity );
		$this->fields = array();
	}
	
	/**
	 * Ajoute un champ au formulaire passé en paramètre, en assignant la valeur du champ associé de l'netité du formulaire ($this -> entity -> $field_name()).
	 * Retourne le nouveau formulaire.
	 *
	 * @param Field $field
	 *
	 * @return Form
	 */
	public function add( Field $field ) {
		$field_name = $field->name(); // Récupérer le nom du champ
		$field->setValue( $this->entity->$field_name());
		// La value du field est initialisée à la valeur détenue par l'entité
		$this->fields[] = $field;
		
		return $this;
	}
	
	/**
	 * Génère le code associé au formulaire objet.
	 *
	 * @return string
	 */
	public function createView() {
		$view = '';
		// Construire tous les fields
		foreach ( $this->fields as $field ) {
			$view .= $field->buildWidget();
			$view .= '<br />';
		}
		
		return $view;
	}
	
	/**
	 * Vérifie si le formulaire est valide ou pas.
	 * Un formulaire est valide si tous ses champs sont valides.
	 *
	 * @return bool
	 */
	public function isValid() {
		foreach ( $this->fields as $field ) {
			if ( !$field->isValid() ) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * @return Entity
	 */
	public function entity() {
		return $this->entity;
	}
	
	/**
	 * Setter de l'attribut $entity.
	 *
	 * @param Entity $entity
	 */
	public function setEntity( Entity $entity ) {
		$this->entity = $entity;
	}
}