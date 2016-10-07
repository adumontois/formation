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
	 * @var $news int
	 */
	protected $news;
	/**
	 * @var $auteur string
	 */
	protected $auteur;
	/**
	 * @var $contenu string
	 */
	protected $contenu;
	/**
	 * @var $Date \DateTime
	 */
	protected $Date;
	
	/**
	 * Vérifie si le commentaire est valide.
	 *
	 * @return bool
	 */
	public function isValid() {
		return !empty( $this->news ) AND !empty( $this->auteur ) AND is_string( $this->auteur ) AND !empty( $this->contenu ) AND is_string( $this->contenu );
	}
	
	/**
	 * @return int
	 */
	public function news() {
		return $this->news;
	}
	
	/**
	 * @return string
	 */
	public function auteur() {
		return $this->auteur;
	}
	
	/**
	 * @return string
	 */
	public function contenu() {
		return $this->contenu;
	}
	
	/**
	 * @return \DateTime
	 */
	public function Date() {
		return $this->Date;
	}
	
	/**
	 * Setter pour l'attribut news.
	 *
	 * @param $news int
	 */
	public function setNews( $news ) {
		if ( (int)$news > 0 ) {
			$this->news = $news;
		}
	}
	
	/**
	 * Setter pour l'attribut auteur.
	 *
	 * @param $auteur string
	 */
	public function setAuteur( $auteur ) {
		if ( is_string( $auteur ) AND !empty( $auteur ) ) {
			$this->auteur = $auteur;
		}
	}
	
	/**
	 * Setter pour l'attribut contenu.
	 *
	 * @param $contenu string
	 */
	public function setContenu( $contenu ) {
		if ( is_string( $contenu ) AND !empty( $contenu ) ) {
			$this->contenu = $contenu;
		}
	}
	
	/**
	 * Setter pour l'attribut Date.
	 *
	 * @param \DateTime $Date
	 */
	public function setDate( \DateTime $Date ) {
		$this->Date = $Date;
	}
}