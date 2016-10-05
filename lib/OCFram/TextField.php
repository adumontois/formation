<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 14:40
 */

namespace OCFram;

/**
 * Class TextField
 *
 * Classe représentant une zone de texte (textarea)
 *
 * @package OCFram
 */
class TextField extends Field {
	/**
	 * @var $cols int Entier strictement positif, nombre de colonnes de la textarea.
	 */
	protected $cols;
	/**
	 * @var $rows int Entier strictement positif, nombre de lignes de la textarea.
	 */
	protected $rows;
	
	/**
	 * Construit la textarea.
	 *
	 * @return string Code HTML issu de la construction de la textarea.
	 */
	public function buildWidget() {
		$code = '';
		if ( !empty( $this->errorMessage ) ) {
			$code = $this->errorMessage . '<br />';
		}
		$code .= '<label>' . $this->label . '</label>
            <textarea name = "' . $this->name() . '" ';
		if ( !empty( $this->cols ) ) {
			$code .= 'cols = "' . $this->cols . '" ';
		}
		if ( !empty( $this->rows ) ) {
			$code .= 'rows = "' . $this->rows . '" ';
		}
		
		return $code . '>' . htmlspecialchars( $this->value() ) . '</textarea>';
	}
	
	/**
	 * Setter pour l'attribut cols.
	 *
	 * @param $cols int Entier strictement positif, nombre de colonnes de la textarea.
	 */
	public function setCols( $cols ) {
		if ( is_int( $cols ) AND $cols >= 0 ) {
			$this->cols = $cols;
		}
	}
	
	/**
	 * Setter pour l'attribut cols
	 *
	 * @param $rows int Entier strictement positif, nombre de lignes de la textarea.
	 */
	public function setRows( $rows ) {
		if ( is_int( $rows ) AND $rows >= 0 ) {
			$this->rows = $rows;
		}
	}
}