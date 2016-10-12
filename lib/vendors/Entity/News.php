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
	 * @var $dateadd \DateTime
	 */
	protected $dateadd;
	/**
	 * @var $dateupdate \DateTime
	 */
	protected $dateupdate;
	/**
	 * Liste des liens donnant les actions possibles sur l'entité
	 *
	 * @var $action_a array[]
	 */
	protected $action_a;
	
	public function __construct( array $values ) {
		parent::__construct( $values );
		if (!isset($this->action_a)) {
			$this->action_a = [];
		}
	}
	
	/**
	 * Vérifie si la news est valide.
	 *
	 * @return bool
	 */
	public function isValid() {
		return !empty( $this->title ) AND !empty( $this->content ) AND !empty($this->fk_SUC) AND is_int($this->fk_SUC);
	}
	
	/**
	 * Setter pour l'attribut User.
	 *
	 * @param $User User
	 */
	public function setUser( User $User ) {
		if ($User->isValid()) {
			$this->User = $User;
		}
	}
	
	/**
	 * Setter pour l'attribut fk_SUC
	 *
	 * @param $fk_SUC int
	 */
	public function setFk_SUC($fk_SUC) {
		$this->fk_SUC = $fk_SUC;
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
	 * Setter pour l'attribut dateadd.
	 *
	 * @param \DateTime $dateadd
	 */
	public function setDateadd( \DateTime $dateadd ) {
		$this->dateadd = $dateadd;
	}
	
	/**
	 * Formate la date d'ajout. Cette méthode modifie l'attribut dateadd.
	 */
	public function formatDateadd() {
		$this->dateadd = $this->dateadd->format('d/m/Y à H\hi');
	}
	
	/**
	 * Setter pour l'attribut dateupdate.
	 *
	 * @param \DateTime $dateupdate
	 */
	public function setDateupdate( \DateTime $dateupdate ) {
		$this->dateupdate = $dateupdate;
	}
	
	/**
	 * Formate la date de modification. Cette méthode modifie l'attribut dateupdate.
	 */
	public function formatDateupdate() {
		$this->dateupdate = $this->dateupdate->format('d/m/Y à H\hi');
	}
	
	/**
	 * Formate les attributs de l'objet pour affichage à l'écran Cette méthode modifie les dates.
	 */
	public function format() {
		$this->formatDateadd();
		$this->formatDateupdate();
	}
	
	/**
	 * Ajoute une UNIQUE action à afficher à la news.
	 *
	 * @param array $action
	 */
	public function setAction_a(array $action = array()) {
		if (!in_array($action, $this->action_a)) {
			$this->action_a[] = $action;
		}
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
	public function dateadd() {
		return $this->dateadd;
	}
	
	/**
	 * @return \DateTime
	 */
	public function dateupdate() {
		return $this->dateupdate;
	}
	
	/**
	 * @return array[]|array array si vide.
	 */
	public function action_a() {
		return $this->action_a;
	}
}