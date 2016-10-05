<?php

/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 14:50
 */

namespace Entity;

use \OCFram\Entity;

/**
 * Class News
 *
 * Modélise une news.
 *
 * @package Entity
 */
class News extends Entity {
	/**
	 * @var $auteur string
	 */
	protected $auteur;
	/**
	 * @var $titre string
	 */
	protected $titre;
	/**
	 * @var $contenu string
	 */
	protected $contenu;
	/**
	 * @var $dateAjout \DateTime
	 */
	protected $dateAjout;
	/**
	 * @var $dateModif \DateTime
	 */
	protected $dateModif;
	const INVALID_AUTHOR  = 1;
	const INVALID_TITLE   = 2;
	const INVALID_CONTENT = 3;
	
	/**
	 * Vérifie si la news est valide.
	 *
	 * @return bool
	 */
	public function isValid() {
		return !empty( $this->auteur ) AND !empty( $this->titre ) AND !empty( $this->contenu );
	}
	
	/**
	 * Setter pour l'attribut auteur.
	 *
	 * @param $auteur string
	 */
	public function setAuteur( $auteur ) {
		if ( !empty( $auteur ) AND is_string( $auteur ) ) {
			$this->auteur = $auteur;
		}
		else {
			$this->erreurs[] = self::INVALID_AUTHOR;
		}
	}
	
	/**
	 * Setter pour l'attribut titre.
	 *
	 * @param $titre string
	 */
	public function setTitre( $titre ) {
		if ( !empty( $titre ) AND is_string( $titre ) ) {
			$this->titre = $titre;
		}
		else {
			$this->erreurs[] = self::INVALID_TITLE;
		}
	}
	
	/**
	 * Setter pour l'attribut contenu.
	 *
	 * @param $contenu string
	 */
	public function setContenu( $contenu ) {
		if ( !empty( $contenu ) AND is_string( $contenu ) ) {
			$this->contenu = $contenu;
		}
		else {
			$this->erreurs[] = self::INVALID_CONTENT;
		}
	}
	
	/**
	 * Setter pour l'attribut dateAjout.
	 *
	 * @param \DateTime $date
	 */
	public function setDateAjout( \DateTime $date ) {
		$this->dateAjout = $date;
	}
	
	/**
	 * Setter pour l'attribut dateModif.
	 *
	 * @param \DateTime $date
	 */
	public function setDateModif( \DateTime $date ) {
		$this->dateModif = $date;
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
	public function titre() {
		return $this->titre;
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
	public function dateAjout() {
		return $this->dateAjout;
	}
	
	/**
	 * @return \DateTime
	 */
	public function dateModif() {
		return $this->dateModif;
	}
}