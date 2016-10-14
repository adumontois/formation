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
	/**
	 * @var $errorMessage string Message d'erreur à afficher lors du renvoi du formulaire
	 */
	protected $errorMessage;
	/**
	 * @var $label string Label à afficher pour le champ
	 */
	protected $label;
	/**
	 * @var $name string Nom du champ
	 */
	protected $name;
	/**
	 * @var $value string Valeur du champ
	 */
	protected $value;
	/**
	 * @var $validators Validator[] Liste des validateurs permettant de valider le champ
	 */
	protected $validators;
	
	/**
	 * Construit un champ à partir des arguments en array associatif.
	 * Le nom du champ est optionnel, il est fixé par défaut au label en minuscules.
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
		if (!$this->validators) {
			return true;
		}
		foreach ( $this->validators as $validator ) {
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
	 * @return string
	 */
	 public function errorMessage() {
	    return $this->errorMessage;
	 }
	
	/**
	 * Setter pour le paramètre label
	 *
	 * @param $label string
	 */
	public function setLabel( $label ) {
		if ( is_string( $label ) ) {
			$this->label = $label;
		}
	}
	
	/**
	 * Setter pour le paramètre name
	 *
	 * @param $name string
	 */
	public function setName( $name ) {
		if ( is_string( $name ) ) {
			$this->name = $name;
		}
	}
	
	/**
	 * Setter pour le paramètre value
	 *
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
			if ( $validator instanceof Validator AND ( null === $this->validators OR !in_array( $validator, $this->validators ) ) ) {
				$this->validators[] = $validator;
			}
		}
	}
	
	/**
	 * Efface le contenu du tableau de validateurs
	 */
	public function eraseValidators() {
		$this->validators = array();
	}
}