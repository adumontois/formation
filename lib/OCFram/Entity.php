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
abstract class Entity implements \ArrayAccess {
	use Hydrator;
	protected $erreurs;
	protected $id;
	
	/**
	 * Construit une entité en initialisant ses arguments.
	 *
	 * @param array $values
	 */
	public function __construct( array $values = array() ) {
		if ( !empty( $values ) ) {
			$this->hydrate( $values );
		}
		$this->erreurs = array();
	}
	
	/**
	 * Vérifie si l'entité est valide.
	 *
	 * @return bool
	 */
	abstract public function isValid();
		
	/**
	 * Indique si l'objet est déjà inséré en base. Renvoie vrai si l'objet n'est pas inséré, faux sinon.
	 * @return bool
	 */
	public function object_new() {
		return empty( $this->id );
	}
	
	/**
	 * Implémentation de l'interface ArrayAccess.
	 * Vérifie si l'attribut indiqué par une accession en tableau existe.
	 * @param mixed $offset
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
	 * @param mixed $offset
	 *
	 * @return null
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
	 * @param mixed $offset attribut
	 * @param mixed $value valeur
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
	 * @param mixed $offset
	 *
	 * @throws \Exception
	 */
	public function offsetUnset( $offset ) {
		throw new \Exception( 'Can\'t delete value in entity' );
	}
	
	/**
	 * @return integer DB id de l'objet.
	 */
	public function id() {
		return $this->id;
	}
	
	/**
	 * @return array Tableau associatif indiquant à chaque attribut l'erreur qui est survenue
	 */
	public function erreurs() {
		return $this->erreurs;
	}
	
	/**
	 * Setter pour l'id
	 * @param $id int strictement positif
	 */
	public function setId( $id ) {
		if ( is_int( $id ) AND $id > 0 ) {
			$this->id = $id;
		}
	}
}