<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 16:34
 */

namespace Model;

use Entity\Comment;
use OCFram\Entity;
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
	 * Récupère tous les commentaires associés à la news d'id passé en paramètre
	 *
	 * @param $newsc_id int ID de la news
	 *
	 * @return Comment[]
	 */
	abstract public function getCommentcUsingNewscIdSortByIdDesc( $newsc_id );
	
	/**
	 * Récupère le commentaire d'id donné.
	 *
	 * @param $commentc_id int ID du commentaire
	 *
	 * @return Comment|null
	 */
	abstract public function getCommentcUsingCommentcId( $commentc_id );
	
	/**
	 * Supprime le commentaire d'id fourni en paramètre.
	 * Renvoie true si le commentaire existait, false sinon.
	 *
	 * @param $commentc_id int ID du commentaire
	 *
	 * @return bool
	 */
	abstract public function deleteCommentcUsingCommentcId( $commentc_id );
	
	/**
	 * Supprime tous les commentaires liés à une news d'id donné.
	 *
	 * @param $newsc_id int ID de la news
	 */
	abstract public function deleteCommentcUsingNewscId( $newsc_id );
	
	/**
	 * Insère ou met à jour le commentaire en DB selon qu'il existe déjà ou non en base.
	 *
	 * @param Entity $Comment
	 *
	 *
	 */
	final public function save( Entity $Comment ) {
		if (!$Comment instanceof Comment) {
			throw new \BadMethodCallException('Save method expects Comment argument.');
		}
		if ( $Comment->isValid() ) {
			if ( $Comment->objectNew() ) {
				$this->insertCommentc( $Comment );
			}
			
			else {
				$this->updateCommentc( $Comment );
			}
		}
	}
	
	/**
	 * Insère le commentaire en DB.
	 * Cette méthode ne doit pas être appelée directement ; utiliser la méthode publique save.
	 *
	 * @param Comment $Comment
	 */
	abstract protected function insertCommentc( Comment $Comment );
	
	/**
	 * Modifie le commentaire en DB.
	 * Cette méthode ne doit pas être appelée directement ; utiliser la méthode publique save.
	 *
	 * @param Comment $Comment
	 */
	abstract protected function updateCommentc( Comment $Comment );
	
	/**
	 * Vérifie si le commentaire d'id donné existe en base.
	 *
	 * @param $commentc_id int
	 *
	 * @return bool
	 */
	abstract public function existsCommentcUsingCommentcId($commentc_id);
}