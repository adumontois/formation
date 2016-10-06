<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 16:35
 */

namespace Model;

use Entity\Comment;
use OCFram\Entity;

/**
 * Class CommentsManagerPDO
 *
 * Implémentation d'un CommentsManager avec la bibliothèque PDO pour Mysql.
 *
 * @package Model
 */
class CommentsManagerPDO extends CommentsManager {
	/**
	 * Ajoute un nouveau commentaire en DB.
	 *
	 * @param Entity $comment Commentaire à insérer
	 */
	protected function add( Entity $comment ) {
		/**
		 * @var $query \PDOStatement
		 * @var $comment Comment
		 */
		$sql = 'INSERT INTO comment
                    (news, auteur, contenu, date)
                VALUES (:news, :auteur, :contenu, NOW())';
		
		$query = $this->dao->prepare( $sql );
		$query->bindValue( ':news', $comment->news(), \PDO::PARAM_INT );
		$query->bindValue( ':auteur', $comment->auteur(), \PDO::PARAM_STR );
		$query->bindValue( ':contenu', $comment->contenu(), \PDO::PARAM_STR );
		$query->execute();
		$comment->setId( $this->dao->lastInsertId() );
	}
	
	/**
	 * Met à jour un commentaire existant en DB.
	 *
	 * @param Entity $comment
	 */
	protected function modify( Entity $comment ) {
		/**
		 * @var $query \PDOStatement
		 * @var $comment Comment
		 */
		$sql = 'UPDATE comment
                SET news = :news, auteur = :auteur, contenu = :contenu
                WHERE id = :id';
		$query = $this->dao->prepare( $sql );
		$query->bindValue( ':news', $comment->news(), \PDO::PARAM_INT );
		$query->bindValue( ':auteur', $comment->auteur(), \PDO::PARAM_STR );
		$query->bindValue( ':contenu', $comment->contenu(), \PDO::PARAM_STR );
		$query->bindValue( ':id', $comment->id(), \PDO::PARAM_INT );
		$query->execute();
	}
	
	/**
	 * Récupère tous les commentaires associés à la news d'id passé en paramètre
	 *
	 * @param $id int ID de la news
	 *
	 * @return Comment[]
	 */
	public function getListOf( $id ) {
		/**
		 * @var $query         \PDOStatement
		 * @var $listeComments Comment[]
		 */
		if ( !ctype_digit( $id ) ) {
			throw new \RuntimeException( 'News id must be an integer value' );
		}
		
		$sql = 'SELECT id, news, auteur, contenu, date
                FROM comment
                WHERE news = :news
                ORDER BY id DESC';
		
		$query = $this->dao->prepare( $sql );
		$query->bindValue( ':news', $id, \PDO::PARAM_INT );
		$query->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'Entity\Comment' ); // Incohérence avec la doc PHP
		$query->execute();
		$listeComments = $query->fetchAll();
		foreach ( $listeComments as $comment ) {
			$comment->setDate( new \DateTime( $comment->date() ) );
		}
		$query->closeCursor();
		
		return $listeComments;
	}
	
	/**
	 * Récupère le commentaire d'id donné.
	 *
	 * @param $id int ID du commentaire
	 *
	 * @return Comment
	 */
	public function get( $id ) {
		/**
		 * @var $query \PDOStatement
		 */
		if ( !ctype_digit( $id ) ) {
			throw new \RuntimeException( 'Comment id must be an integer value' );
		}
		
		$sql   = 'SELECT id, news, auteur, contenu, date
                FROM comment
                WHERE id = :id';
		$query = $this->dao->prepare( $sql );
		$query->bindValue( ':id', $id, \PDO::PARAM_INT );
		$query->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'Entity\Comment' ); // Incohérence avec la doc PHP
		$query->execute();
		$comment = $query->fetch();
		$query->closeCursor();
		return $comment;
	}
	
	/**
	 * Supprime le commentaire d'id fourni en paramètre.
	 *
	 * @param $id int ID du commentaire
	 */
	public function delete( $id ) {
		/**
		 * @var $query \PDOStatement
		 */
		$sql   = 'DELETE FROM comment
                WHERE id = :id';
		$query = $this->dao->prepare( $sql );
		$query->bindValue( ':id', (int)$id, \PDO::PARAM_INT );
		$query->execute();
	}
	
	/**
	 * Supprime tous les commentaires liés à une news d'id donné.
	 *
	 * @param $id int ID de la news
	 */
	public function deleteFromNews( $id ) {
		/**
		 * @var $query \PDOStatement
		 */
		$sql   = 'DELETE FROM comment
                WHERE news = :id';
		$query = $this->dao->prepare( $sql );
		$query->bindValue( ':id', (int)$id, \PDO::PARAM_INT );
		$query->execute();
	}
}