<?php

/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 14:50
 */

namespace Model;

use Entity\News;
use OCFram\Entity;
use OCFram\Manager;

/**
 * Class NewsManager
 *
 * Classe abstraite permettant de gérer les news.
 *
 * @package Model
 */
abstract class NewsManager extends Manager {
	const MAX_LIST_SIZE = 1000000;
	
	/**
	 * Récupère une liste de $count news, commençant par la $start-ème news.
	 *
	 * @param $start int Offset dans la liste
	 * @param $count int Nombre maximal de news à retourner
	 *
	 * @return News[]
	 */
	abstract public function getNewscSortByIdDesc( $start = 0, $count = self::MAX_LIST_SIZE );
	
	/**
	 * Récupère la news d'id donné.
	 *
	 * @param $newsc_id int
	 *
	 * @return null|News
	 */
	abstract public function getNewscUsingNewscId( $newsc_id );
	
	/**
	 * Compte le nombre de news en DB.
	 *
	 * @return int
	 */
	abstract public function countNewscUsingNewscId();
	
	/**
	 * Supprime la news d'id donné de la DB.
	 * Renvoie true si la news existait, false sinon.
	 *
	 * @param $newsc_id int
	 *
	 * @return bool
	 */
	abstract public function deleteNewscUsingNewscId( $newsc_id );
	
	/**
	 * Insère ou met à jour la news en DB selon qu'elle existe déjà ou non en base.
	 *
	 * @param Entity $News
	 *
	 */
	final public function save( Entity $News ) {
		if (!$News instanceof News) {
			throw new \BadMethodCallException('Save method expects News argument.');
		}
		if ( $News->isValid() ) {
			if ( $News->objectNew() ) {
				$this->insertNewsc( $News );
			}
			
			else {
				$this->updateNewsc( $News );
			}
		}
	}
	
	/**
	 * Insère la news en DB.
	 * Cette méthode ne doit pas être appelée directement ; utiliser la méthode publique save.
	 *
	 * @param News $News
	 */
	abstract protected function insertNewsc( News $News );
	
	/**
	 * Modifie la news en DB.
	 * Cette méthode ne doit pas être appelée directement ; utiliser la méthode publique save.
	 *
	 * @param News $News
	 */
	abstract protected function updateNewsc( News $News );
}