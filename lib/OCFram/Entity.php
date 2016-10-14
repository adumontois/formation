<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 10:55
 */

namespace OCFram;

/**
 * Class Entity
 *
 * @package OCFram
 *
 * Classe abstraite représentant une classe objet (news, commentaire...)
 */
abstract class Entity implements \ArrayAccess, \JsonSerializable  {
	use Hydrator;
	/**
	 * @var $id int ID de l'entité en DB. Vaut NULL si l'entité n'est pas encore insérée.
	 */
	protected $id;
	/**
	 * @var string[] $error_a Erreurs générées dans l'objet
	 */
	protected $error_a;
	
	/**
	 * Construit une entité en initialisant ses arguments.
	 *
	 * @param array $values
	 */
	public function __construct( array $values = array() ) {
		if ( !empty( $values ) ) {
			$this->hydrate( $values );
		}
	}
	
	/**
	 * Vérifie si l'entité est valide.
	 *
	 * @return bool
	 */
	abstract public function isValid();
	
	/**
	 * Indique si l'objet est déjà inséré en base. Renvoie vrai si l'objet n'est pas inséré, faux sinon.
	 *
	 * @return bool
	 */
	public function objectNew() {
		return empty( $this->id );
	}
	
	/**
	 * Implémentation de l'interface ArrayAccess.
	 * Vérifie si l'attribut indiqué par une accession en tableau existe.
	 *
	 * @param string $offset
	 *
	 * @return bool
	 */
	public function offsetExists( $offset ) {
		return isset( $this->$offset ) AND is_callable( array(
			$this,
			$offset,
		) );
	}
	
	/**
	 * Implémentation de l'interface ArrayAccess.
	 * Renvoie la valeur de l'attribut $offset en accession en tableau. Si l'attribut n'existe pas, renvoie NULL.
	 *
	 * @param string $offset
	 *
	 * @return mixed|null
	 */
	public function offsetGet( $offset ) {
		if ( $this->offsetExists( $offset ) ) {
			return $this->$offset();
		}
		
		return null;
	}
	
	/**
	 * Set la valeur de l'attribut $offset à $value en accession en tableau.
	 *
	 * @param string $offset attribut
	 * @param mixed  $value  valeur
	 */
	public function offsetSet( $offset, $value ) {
		$method = 'set' . ucfirst( $offset );
		if ( isset( $this->$offset ) AND is_callable( array(
				[
					$this,
					$method,
				],
			) )
		) {
			$this->$method( $value );
		}
	}
	
	/**
	 * Désactive la possibilité de détruire la valeur d'un attribut en accession en tableau.
	 *
	 * @param string $offset
	 *
	 * @throws \Exception
	 */
	public function offsetUnset( $offset ) {
		throw new \Exception( 'Can\'t delete value in entity' );
	}
	
	/**
	 * @return int ID ID de l'entité en DB. Vaut NULL si l'entité n'est pas encore insérée.
	 */
	public function id() {
		return $this->id;
	}
	
	/**
	 * Setter pour l'id
	 *
	 * @param $id int strictement positif
	 */
	public function setId( $id ) {
		if ( (int)$id > 0 ) {
			$this->id = (int)$id;
		}
	}
	
	// Implémentation de JsonSerialisable
	public function jsonSerialize() {
		return get_object_vars($this);
	}
	
	/**
	 * return string[]
	 */
	public function error_a() {
	    return $this->error_a;
	}
	
	/**
	 * Ajoute une erreur à l'entité
	 *
	 * @param $key string clé
	 * @param $error_message string Erreur à afficher
	 */
	public function addError_a($key, $error_message) {
		if (!is_string($key) OR empty($key) OR ctype_digit($key[0])) {
			throw new \InvalidArgumentException('Key to add must be a non empty string, and must start with a letter.');
		}
		if (!is_string($error_message)) {
			throw new \InvalidArgumentException('Error_message must be a string');
		}
		if (!isset($this->error_a[$key])) {
			$this->error_a[$key] = $error_message;
		}
	}
}