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
		 * @var $Query   \PDOStatement
		 * @var $Comment Comment
		 */
		$sql = 'INSERT INTO T_SIT_commentc
                    (SCC_fk_SNC, SCC_author, SCC_content, SCC_date)
                VALUES (:news, :auteur, :contenu, NOW())';
		
		$Query = $this->dao->prepare( $sql );
		$Query->bindValue( ':news', $Comment->news(), \PDO::PARAM_INT );
		$Query->bindValue( ':auteur', $Comment->auteur(), \PDO::PARAM_STR );
		$Query->bindValue( ':contenu', $Comment->contenu(), \PDO::PARAM_STR );
		$Query->execute();
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
		 * @var $Query   \PDOStatement
		 * @var $Comment Comment
		 */
		$sql   = 'UPDATE T_SIT_commentc
                SET SCC_fk_SNC = :news, SCC_author = :auteur, SCC_content = :contenu
                WHERE SCC_id = :id';
		$Query = $this->dao->prepare( $sql );
		$Query->bindValue( ':news', $Comment->news(), \PDO::PARAM_INT );
		$Query->bindValue( ':auteur', $Comment->auteur(), \PDO::PARAM_STR );
		$Query->bindValue( ':contenu', $Comment->contenu(), \PDO::PARAM_STR );
		$Query->bindValue( ':id', $Comment->id(), \PDO::PARAM_INT );
		$Query->execute();
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
		 * @var $Query            \PDOStatement
		 * @var $Liste_comments_a Comment[]
		 */
		$sql = 'SELECT SCC_id id, SCC_fk_SNC news, SCC_author auteur, SCC_content contenu, SCC_date Date
                FROM T_SIT_commentc
                WHERE SCC_fk_SNC = :news
                ORDER BY SCC_id DESC';
		
		$Query = $this->dao->prepare( $sql );
		$Query->bindValue( ':news', (int)$newsc_id, \PDO::PARAM_INT );
		$Query->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'Entity\Comment' ); // Incohérence avec la doc PHP
		$Query->execute();
		$Liste_comments_a = $Query->fetchAll();
		foreach ( $Liste_comments_a as $Comment ) {
			$Comment->setDate( new \DateTime( $Comment->Date() ) );
		}
		$Query->closeCursor();
		
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
		 * @var $Query \PDOStatement
		 */
		$sql   = 'SELECT SCC_id id, SCC_fk_SNC news, SCC_author auteur, SCC_content contenu, SCC_date Date
                FROM T_SIT_commentc
                WHERE SCC_id = :id';
		$Query = $this->dao->prepare( $sql );
		$Query->bindValue( ':id', (int)$commentc_id, \PDO::PARAM_INT );
		$Query->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'Entity\Comment' ); // Incohérence avec la doc PHP
		$Query->execute();
		$Comment = $Query->fetch();
		$Query->closeCursor();
		
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
		 * @var $Query \PDOStatement
		 */
		$sql   = 'DELETE FROM T_SIT_commentc
                WHERE SCC_id = :id';
		$Query = $this->dao->prepare( $sql );
		$Query->bindValue( ':id', (int)$commentc_id, \PDO::PARAM_INT );
		$Query->execute();
		
		return (bool)$Query->rowCount();
	}
	
	/**
	 * Supprime tous les commentaires liés à une news d'id donné.
	 *
	 * @param $newsc_id int ID de la news
	 */
	public function deleteCommentcUsingNewscId( $newsc_id ) {
		/**
		 * @var $Query \PDOStatement
		 */
		$sql   = 'DELETE FROM T_SIT_commentc
                WHERE SCC_fk_SNC = :id';
		$Query = $this->dao->prepare( $sql );
		$Query->bindValue( ':id', (int)$newsc_id, \PDO::PARAM_INT );
		$Query->execute();
	}
}