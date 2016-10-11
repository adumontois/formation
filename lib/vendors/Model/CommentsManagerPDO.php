<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 16:35
 */

namespace Model;

use Entity\Comment;

/**
 * Class CommentsManagerPDO
 *
 * Implémentation d'un CommentsManager avec la bibliothèque PDO pour Mysql.
 *
 * @package Model
 */
class CommentsManagerPDO extends CommentsManager {
	/**
	 * Insère le commentaire en DB.
	 * Cette méthode ne doit pas être appelée directement ; utiliser la méthode publique save.
	 *
	 * @param Comment $Comment
	 */
	protected function insertCommentc( Comment $Comment ) {
		/**
		 * @var $stmt   \PDOStatement
		 * @var $Comment Comment
		 */
		$sql = 'INSERT INTO T_SIT_commentc
                    (SCC_fk_SNC, SCC_author, SCC_content, SCC_date)
                VALUES (:fk_SNC, :author, :content, NOW())';
		
		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':fk_SNC', $Comment->fk_SNC(), \PDO::PARAM_INT );
		$stmt->bindValue( ':author', $Comment->fk_SNC(), \PDO::PARAM_STR );
		$stmt->bindValue( ':content', $Comment->content(), \PDO::PARAM_STR );
		$stmt->execute();
		$Comment->setId( $this->dao->lastInsertId() );
	}
	
	/**
	 * Modifie le commentaire en DB.
	 * Cette méthode ne doit pas être appelée directement ; utiliser la méthode publique save.
	 *
	 * @param Comment $Comment
	 */
	protected function updateCommentc( Comment $Comment ) {
		/**
		 * @var $stmt   \PDOStatement
		 * @var $Comment Comment
		 */
		$sql   = 'UPDATE T_SIT_commentc
                SET SCC_fk_SNC = :fk_SNC, SCC_author = :fk_SNC, SCC_content = :contenu
                WHERE SCC_id = :id';
		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':fk_SNC', $Comment->fk_SNC(), \PDO::PARAM_INT );
		$stmt->bindValue( ':fk_SNC', $Comment->fk_SNC(), \PDO::PARAM_STR );
		$stmt->bindValue( ':content', $Comment->content(), \PDO::PARAM_STR );
		$stmt->bindValue( ':id', $Comment->id(), \PDO::PARAM_INT );
		$stmt->execute();
	}
	
	/**
	 * Récupère tous les commentaires associés à la news d'id passé en paramètre
	 *
	 * @param $newsc_id int ID de la news
	 *
	 * @return Comment[]
	 */
	public function getCommentcUsingNewscIdSortByIdDesc( $newsc_id ) {
		/**
		 * @var $stmt            \PDOStatement
		 * @var $Liste_comments_a Comment[]
		 */
		$sql = 'SELECT SCC_id id, SCC_fk_SNC fk_SNC, SCC_author author, SCC_content content, SCC_date date
                FROM T_SIT_commentc
                WHERE SCC_fk_SNC = :fk_SNC
                ORDER BY SCC_id DESC';
		
		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':fk_SNC', (int)$newsc_id, \PDO::PARAM_INT );
		$stmt->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'Entity\Comment' ); // OK
		$stmt->execute();
		$Liste_comments_a = $stmt->fetchAll();
		foreach ( $Liste_comments_a as $Comment ) {
			$Comment->setDate( new \DateTime( $Comment->date() ) );
		}
		$stmt->closeCursor();
		
		return $Liste_comments_a;
	}
	
	/**
	 * Récupère le commentaire d'id donné.
	 *
	 * @param $commentc_id int ID du commentaire
	 *
	 * @return Comment|null
	 */
	public function getCommentcUsingCommentcId( $commentc_id ) {
		/**
		 * @var         $stmt \PDOStatement
		 * @var Comment $Comment
		 */
		$sql   = 'SELECT SCC_id id, SCC_fk_SNC fk_SNC, SCC_author author, SCC_content content, SCC_date date
                FROM T_SIT_commentc
                WHERE SCC_id = :id';
		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':id', (int)$commentc_id, \PDO::PARAM_INT );
		$stmt->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'Entity\Comment' ); // Incohérence avec la doc PHP
		$stmt->execute();
		$Comment = $stmt->fetch();
		if ( $Comment != null ) {
			$Comment->setDate( new \DateTime( $Comment->date() ) );
		}
		$stmt->closeCursor();
		
		return $Comment;
	}
	
	/**
	 * Supprime le commentaire d'id fourni en paramètre.
	 * Renvoie true si le commentaire existait, false sinon.
	 *
	 * @param $commentc_id int ID du commentaire
	 *
	 * @return bool
	 */
	public function deleteCommentcUsingCommentcId( $commentc_id ) {
		/**
		 * @var $stmt \PDOStatement
		 */
		$sql   = 'DELETE FROM T_SIT_commentc
                WHERE SCC_id = :id';
		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':id', (int)$commentc_id, \PDO::PARAM_INT );
		$stmt->execute();
		
		return (bool)$stmt->rowCount();
	}
	
	/**
	 * Supprime tous les commentaires liés à une news d'id donné.
	 *
	 * @param $newsc_id int ID de la news
	 */
	public function deleteCommentcUsingNewscId( $newsc_id ) {
		/**
		 * @var $stmt \PDOStatement
		 */
		$sql   = 'DELETE FROM T_SIT_commentc
                WHERE SCC_fk_SNC = :fk_SNC';
		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':fk_SNC', (int)$newsc_id, \PDO::PARAM_INT );
		$stmt->execute();
	}
}