<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 16:34
 */

namespace Model;

use Entity\Comment;
use OCFram\Manager;

/**
 * Class CommentsManager
 *
 * Classe abstraite permettant de gérer les commentaires.
 *
 * @package Model
 */
abstract class CommentsManager extends Manager {
	/**
	 * Insère ou met à jour en DB un commentaire, selon s'il existe déjà ou non.
	 *
	 * @param Comment $comment
	 */
	public function save( Comment $comment ) {
		if ( $comment->isValid() ) {
			if ( $comment->object_new() ) {
				$this->add( $comment );
			}
			else {
				$this->modify( $comment );
			}
		}
		else {
			throw new \RuntimeException( 'Invalid comment, it couldn\'t be registered' );
		}
	}
	
	/**
	 * Ajoute un nouveau commentaire en DB.
	 *
	 * @param Comment $comment
	 */
	abstract protected function add( Comment $comment );
	
	/**
	 * Met à jour un commentaire existant en DB.
	 *
	 * @param Comment $comment
	 */
	abstract protected function modify( Comment $comment );
	
	/**
	 * Récupère tous les commentaires associés à la news d'id passé en paramètre
	 *
	 * @param $id int ID de la news
	 *
	 * @return Comment[]
	 */
	abstract public function getListOf( $id );
	
	/**
	 * Récupère le commentaire d'id donné.
	 *
	 * @param $id int ID du commentaire
	 *
	 * @return Comment
	 */
	abstract public function get( $id );
	
	/**
	 * Supprime le commentaire d'id fourni en paramètre.
	 *
	 * @param $id int ID du commentaire
	 */
	abstract public function delete( $id );
	
	/**
	 * Supprime tous les commentaires liés à une news d'id donné.
	 *
	 * @param $id int ID de la news
	 */
	abstract public function deleteFromNews( $id );
}