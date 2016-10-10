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
	 * @var $auteur int
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
	 * @param $auteur int
	 */
	public function setAuteur( $auteur ) {
		if (is_int($auteur)) {
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
	 * Formate la date d'ajout. Cette méthode modifie l'attribut DateAjout.
	 */
	public function formatDateAjout() {
		$this->DateAjout = $this->DateAjout->format('d/m/Y à H\hi');
	}
	
	/**
	 * Formate la date de modification. Cette méthode modifie l'attribut DateModif.
	 */
	public function formatDateModif() {
		$this->DateModif = $this->DateModif->format('d/m/Y à H\hi');
	}
	
	/**
	 * Formate les attributs de l'objet pour affichage à l'écran Cette méthode modifie les dates.
	 */
	public function format() {
		$this->formatDateAjout();
		$this->formatDateModif();
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
	 * @return int
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