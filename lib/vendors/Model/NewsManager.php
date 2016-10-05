<?php

/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 14:50
 */

namespace Model;

use Entity\News;
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
	 * Récupère une liste de $count news, commençant par la start-ème news.
	 *
	 * @param $start int Offset dans la liste
	 * @param $count int Nombre maximal de news à retourner
	 *
	 * @return News[] Renvoie un tableau de news
	 */
	abstract public function getList( $start = 0, $count = self::MAX_LIST_SIZE );
	
	/**
	 * Récupère la news d'id donné.
	 *
	 * @param $id int
	 *
	 * @return null|News
	 */
	abstract public function getUnique( $id );
	
	/**
	 * Compte le nombre de news en DB.
	 *
	 * @return int
	 */
	abstract public function count();
	
	/**
	 * Insère ou met à jour la news en DB selon qu'elle existe déjà ou non en base.
	 *
	 * @param News $news
	 */
	public function save( News $news ) {
		if ( !$news->isValid() ) {
			throw new \RuntimeException( 'Couldn\'t save the news : invalid news given' );
		}
		else {
			if ( $news->object_new() ) {
				$this->add( $news );
			}
			else {
				$this->modify( $news );
			}
		}
	}
	
	/**
	 * Insère la news passée en paramètre en DB.
	 *
	 * @param News $news
	 */
	abstract public function add( News $news );
	
	/**
	 * Met à jour la news passée en paramètre en DB.
	 *
	 * @param News $news
	 */
	abstract public function modify( News $news );
	
	/**
	 * Supprime la news d'id donné de la DB.
	 *
	 * @param $id int
	 */
	abstract public function delete( $id );
}