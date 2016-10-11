<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 16:37
 */

namespace Entity;


use OCFram\Entity;

/**
 * Class Comment
 *
 * Modélise un commentaire.
 *
 * @package Entity
 */
class Comment extends Entity {
	/**
	 * @var $fk_SNC int
	 */
	protected $fk_SNC;
	/**
	 * @var $author string
	 */
	protected $author;
	/**
	 * @var $content string
	 */
	protected $content;
	/**
	 * @var $date \DateTime
	 */
	protected $date;
	
	/**
	 * Vérifie si le commentaire est valide.
	 *
	 * @return bool
	 */
	public function isValid() {
		return !empty( $this->fk_SNC ) AND !empty( $this->content ) AND is_string( $this->content );
	}
	
	/**
	 * @return int
	 */
	public function fk_SNC() {
		return $this->fk_SNC;
	}
	
	/**
	 * @return string
	 */
	public function author() {
		return $this->author;
	}
	
	/**
	 * @return string
	 */
	public function content() {
		return $this->content;
	}
	
	/**
	 * @return \DateTime
	 */
	public function date() {
		return $this->date;
	}
	
	/**
	 * Setter pour l'attribut fk_SNC.
	 *
	 * @param $fk_SNC int
	 */
	public function setFk_SNC( $fk_SNC ) {
		if ( (int)$fk_SNC > 0 ) {
			$this->fk_SNC = $fk_SNC;
		}
	}
	
	/**
	 * Setter pour l'attribut author.
	 *
	 * @param $author string
	 */
	public function setAuthor( $author ) {
		if ( is_string( $author ) AND !empty( $author ) ) {
			$this->author = $author;
		}
	}
	
	/**
	 * Setter pour l'attribut content.
	 *
	 * @param $content string
	 */
	public function setContent( $content ) {
		if ( is_string( $content ) AND !empty( $content ) ) {
			$this->content = $content;
		}
	}
	
	/**
	 * Setter pour l'attribut date.
	 *
	 * @param \DateTime $date
	 */
	public function setDate( \DateTime $date ) {
		$this->date = $date;
	}
	
	/**
	 * Formate la date pour affichage dans une vue. Cette méthode modifie la valeur de l'attribut date.
	 */
	public function formatDate() {
		$this->date = $this->date->format('d/m/Y à H\hi');
	}
}