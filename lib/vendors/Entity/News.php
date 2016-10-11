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
	 * @var $User User
	 */
	protected $User;
	/**
	 * @var $title string
	 */
	protected $title;
	/**
	 * @var $fk_SUC int
	 */
	protected $fk_SUC;
	/**
	 * @var $content string
	 */
	protected $content;
	/**
	 * @var $Dateadd \DateTime
	 */
	protected $Dateadd;
	/**
	 * @var $Dateupdate \DateTime
	 */
	protected $Dateupdate;
	
	/**
	 * Vérifie si la news est valide.
	 *
	 * @return bool
	 */
	public function isValid() {
		return $this->User->isValid() AND !empty( $this->title ) AND !empty( $this->content );
	}
	
	/**
	 * Setter pour l'attribut User.
	 *
	 * @param $User User
	 */
	public function setUser( $User ) {
		if ($User->isValid()) {
			$this->User = $User;
		}
	}
	
	/**
	 * Setter pour l'attribut title.
	 *
	 * @param $title string
	 */
	public function setTitle( $title ) {
		if ( !empty( $title ) AND is_string( $title ) ) {
			$this->title = $title;
		}
	}
	
	/**
	 * Setter pour l'attribut content.
	 *
	 * @param $content string
	 */
	public function setContent( $content ) {
		if ( !empty( $content ) AND is_string( $content ) ) {
			$this->content = $content;
		}
	}
	
	/**
	 * Setter pour l'attribut Dateadd.
	 *
	 * @param \DateTime $Dateadd
	 */
	public function setDateadd( \DateTime $Dateadd ) {
		$this->Dateadd = $Dateadd;
	}
	
	/**
	 * Formate la date d'ajout. Cette méthode modifie l'attribut Dateadd.
	 */
	public function formatDateadd() {
		$this->Dateadd = $this->Dateadd->format('d/m/Y à H\hi');
	}
	
	/**
	 * Setter pour l'attribut Dateupdate.
	 *
	 * @param \DateTime $Dateupdate
	 */
	public function setDateupdate( \DateTime $Dateupdate ) {
		$this->Dateupdate = $Dateupdate;
	}
	
	/**
	 * Formate la date de modification. Cette méthode modifie l'attribut Dateupdate.
	 */
	public function formatDateupdate() {
		$this->Dateupdate = $this->Dateupdate->format('d/m/Y à H\hi');
	}
	
	/**
	 * Formate les attributs de l'objet pour affichage à l'écran Cette méthode modifie les dates.
	 */
	public function format() {
		$this->formatDateadd();
		$this->formatDateupdate();
	}
	
	/**
	 * @return User
	 */
	public function User() {
		return $this->User;
	}
	
	public function fk_SUC() {
		return $this->fk_SUC;
	}
	
	/**
	 * @return string
	 */
	public function title() {
		return $this->title;
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
	public function Dateadd() {
		return $this->Dateadd;
	}
	
	/**
	 * @return \DateTime
	 */
	public function Dateupdate() {
		return $this->Dateupdate;
	}
}