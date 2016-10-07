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
	 * @var $DateAjout \DateTime
	 */
	protected $DateAjout;
	/**
	 * @var $DateModif \DateTime
	 */
	protected $DateModif;
	
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
	}
	
	/**
	 * Setter pour l'attribut DateAjout.
	 *
	 * @param \DateTime $DateAjout
	 */
	public function setDateAjout( \DateTime $DateAjout ) {
		$this->DateAjout = $DateAjout;
	}
	
	/**
	 * Setter pour l'attribut DateModif.
	 *
	 * @param \DateTime $DateModif
	 */
	public function setDateModif( \DateTime $DateModif ) {
		$this->DateModif = $DateModif;
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
	public function DateAjout() {
		return $this->DateAjout;
	}
	
	/**
	 * @return \DateTime
	 */
	public function DateModif() {
		return $this->DateModif;
	}
}