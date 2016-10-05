<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 12:42
 */

namespace OCFram;


/**
 * Class Field
 *
 * Représente un champ d'un formulaire HTML (classe abstraite)
 *
 * @package OCFram
 */
abstract class Field {
	use Hydrator;
	protected $errorMessage;
	protected $label;
	protected $name;
	protected $value;
	protected $validators;
	
	/**
	 * Construit un champ à partir des arguments en array associatif.
	 *
	 * @param array $options
	 */
	public function __construct( array $options = array() ) {
		$this->setValidators( array() );
		$this->hydrate( $options );
		// Par défaut, setter le nom du field au label en minuscules
		if ( !isset( $this->name ) ) {
			$this->setName( strtolower( $this->label ) );
		}
	}
	
	/**
	 * Construit le champ et renvoie la string associée à cette construction.
	 *
	 * @return string
	 */
	abstract public function buildWidget();
	
	/**
	 * Vérifie si le champ est valide. Un champ est valide si son contenu vérifie toutes les conditions imposées par les validateurs.
	 *
	 * @return bool
	 */
	public function isValid() {
		foreach ( $this->validators as $validator ) {
			/**
			 * @var $validator \OCFram\Validator
			 */
			if ( !$validator->isValid( $this->value ) ) {
				$this->errorMessage = $validator->errorMessage();
				
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * @return string
	 */
	public function label() {
		return $this->label;
	}
	
	/**
	 * @return string
	 */
	public function name() {
		return $this->name;
	}
	
	/**
	 * Accesseur à la valeur du champ précisé, stocké sous forme de string.
	 *
	 * @return string
	 */
	public function value() {
		return $this->value;
	}
	
	/**
	 * Accesseur au tableau des validateurs
	 *
	 * @return \OCFram\Validator[]
	 */
	public function validators() {
		return $this->validators;
	}
	
	/**
	 * @param $label string
	 */
	public function setLabel( $label ) {
		if ( is_string( $label ) ) {
			$this->label = $label;
		}
	}
	
	/**
	 * @param $name string
	 */
	public function setName( $name ) {
		if ( is_string( $name ) ) {
			$this->name = $name;
		}
	}
	
	/**
	 * @param $value string
	 */
	public function setValue( $value ) {
		if ( is_string( $value ) ) {
			$this->value = $value;
		}
	}
	
	/**
	 * Ajoute les validateurs de $validators aux validateurs déjà présents dans l'objet Field, s'ils n'y sont pas déjà.
	 *
	 * @param array $validators
	 */
	public function setValidators( array $validators ) {
		// Ajouter les validateurs suivants
		foreach ( $validators as $validator ) {
			if ( $validator instanceof Validator AND !in_array( $validator, $this->validators ) ) {
				$this->validators[] = $validators;
			}
		}
	}
}