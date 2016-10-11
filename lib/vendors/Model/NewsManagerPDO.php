<?php

/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 14:50
 */

namespace Model;

use Entity\News;
use Entity\User;

/**
 * Class NewsManagerPDO
 *
 * Implémentation d'un NewsManager avec la bibliothèque PDO pour Mysql
 *
 * @package Model
 */
class NewsManagerPDO extends NewsManager {
	/**
	 * Récupère une liste de $count news, commençant par la start-ème news.
	 *
	 * @param $start int Offset dans la liste
	 * @param $count int Nombre maximal de news à retourner
	 *
	 * @return News[] Renvoie un tableau de news
	 */
	public function getNewscSortByIdDesc( $start = 0, $count = parent::MAX_LIST_SIZE ) {
		if ( $start < 0 OR $count <= 0 ) {
			throw new \InvalidArgumentException( 'Offset and limit values must be positive integers' );
		}
		
		$sql = 'SELECT SNC_id, SNC_fk_SUC, SNC_title, SNC_content, SNC_dateadd, SNC_dateupdate, SUC_id, SUC_login, SUC_email, SUC_datesubscription, SUC_fk_SUE_banned, SUC_fk_SUE_valid, SUC_fk_SUY
           		FROM T_SIT_newsc
				INNER JOIN T_SIT_userc ON SNC_fk_SUC = SUC_id
				ORDER BY SNC_id DESC LIMIT :count OFFSET :start';
		
		// Utiliser le dao pour exécuter la requête
		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':count', $count, \PDO::PARAM_INT );
		$stmt->bindValue( ':start', $start, \PDO::PARAM_INT );
		$stmt->execute();
		$News_a = [];
		while ( $line = $stmt->fetch( \PDO::FETCH_ASSOC ) ) {
			$News_a[] = ( new News( [
				'id'         => $line[ 'SNC_id' ],
				'fk_SUC'     => $line[ 'SNC_fk_SUC' ],
				'title'      => $line[ 'SNC_title' ],
				'content'    => $line[ 'SNC_content' ],
				'dateadd'    => new \DateTime( $line[ 'SNC_dateadd' ] ),
				'dateupdate' => new \DateTime( $line[ 'SNC_dateupdate' ] ),
			] ) )->setUser( new User( [
				'id'               => $line[ 'SUC_id' ],
				'login'            => $line[ 'SUC_login' ],
				'mail'             => $line[ 'SUC_email' ],
				'datesubscription' => $line[ 'SUC_datesubscription' ],
				'fk_SUE_banned'    => $line[ 'SUC_fk_SUE_banned' ],
				'fk_SUE_valid'     => $line[ 'SUC_fk_SUE_valid' ],
				'fk_SUY'           => $line[ 'SUC_fk_SUY' ],
			] ) );
		}
		$stmt->closeCursor();
		
		return $News_a;
	}
	
	/**
	 * Récupère la news d'id donné.
	 *
	 * @param $newsc_id int
	 *
	 * @return null|News
	 */
	public function getNewscUsingNewscId( $newsc_id ) {
		/**
		 * @var $stmt \PDOStatement
		 */
		$sql  = 'SELECT SNC_id, SNC_fk_SUC, SNC_title, SNC_content, SNC_dateadd, SNC_dateupdate, SUC_id, SUC_login, SUC_email, SUC_datesubscription, SUC_fk_SUE_banned, SUC_fk_SUE_valid, SUC_fk_SUY
                FROM T_SIT_newsc
                	INNER JOIN T_SIT_userc ON SNC_fk_SUC = SUC_id
                WHERE SNC_id = :id';
		$stmt = $this->dao->prepare( $sql );
		$stmt->bindValue( ':id', $newsc_id, \PDO::PARAM_INT );
		$stmt->execute();
		if ( $line = $stmt->fetch( \PDO::FETCH_ASSOC ) ) {
			$News = ( new News( [
				'id'         => $line[ 'SNC_id' ],
				'fk_SUC'     => $line[ 'SNC_fk_SUC' ],
				'title'      => $line[ 'SNC_title' ],
				'content'    => $line[ 'SNC_content' ],
				'dateadd'    => new \DateTime( $line[ 'SNC_dateadd' ] ),
				'dateupdate' => new \DateTime( $line[ 'SNC_dateupdate' ] ),
			] ) )->setUser( new User( [
				'id'               => $line[ 'SUC_id' ],
				'login'            => $line[ 'SUC_login' ],
				'mail'             => $line[ 'SUC_email' ],
				'datesubscription' => $line[ 'SUC_datesubscription' ],
				'fk_SUE_banned'    => $line[ 'SUC_fk_SUE_banned' ],
				'fk_SUE_valid'     => $line[ 'SUC_fk_SUE_valid' ],
				'fk_SUY'           => $line[ 'SUC_fk_SUY' ],
			] ) );
		}
		else {
			$News = null;
		}
		$stmt->closeCursor();
		
		return $News;
	}

/**
 * Compte le nombre de news en DB.
 *
 * @return int
 */
public
function countNewscUsingNewscId() {
	$sql = 'SELECT COUNT(*)
                FROM T_SIT_newsc';
	
	return $this->dao->query( $sql )->fetchColumn();
}

/**
 * Insère la news en DB.
 * Cette méthode ne doit pas être appelée directement ; utiliser la méthode publique save.
 *
 * @param News $News
 */
protected
function insertNewsc( News $News ) {
	/**
	 * @var $stmt \PDOStatement
	 * @var $News  News
	 */
	$sql   = 'INSERT INTO T_SIT_newsc (SNC_fk_SUC, SNC_title, SNC_content, SNC_dateadd, SNC_dateupdate)
                    VALUES (:fk_SUC, :title, :content, NOW(), NOW())';
	$stmt = $this->dao->prepare( $sql );
	$stmt->bindValue( ':fk_SUC', $News->fk_SUC(), \PDO::PARAM_STR );
	$stmt->bindValue( ':title', $News->title(), \PDO::PARAM_STR );
	$stmt->bindValue( ':content', $News->content(), \PDO::PARAM_STR );
	$stmt->execute();
}

/**
 * Modifie la news en DB.
 * Cette méthode ne doit pas être appelée directement ; utiliser la méthode publique save.
 *
 * @param News $News
 */
protected function updateNewsc( News $News ) {
	/**
	 * @var $stmt \PDOStatement
	 * @var $News  News
	 */
	$sql   = 'UPDATE T_SIT_newsc
                SET SNC_title = :titre, SNC_content = :contenu, SNC_dateupdate = NOW()
                WHERE SNC_id = :id';
	$stmt = $this->dao->prepare( $sql );
	$stmt->bindValue( ':id', $News->id(), \PDO::PARAM_INT );
	$stmt->bindValue( ':titre', $News->title(), \PDO::PARAM_STR );
	$stmt->bindValue( ':contenu', $News->content(), \PDO::PARAM_STR );
	$stmt->execute();
}

/**
 * Supprime la news d'id donné de la DB.
 * Renvoie true si la news existait, false sinon.
 *
 * @param $id int
 *
 * @return bool
 */
public
function deleteNewscUsingNewscId( $id ) {
	/**
	 * @var $stmt \PDOStatement
	 */
	$sql   = 'DELETE FROM T_SIT_newsc
                WHERE SNC_id = :id';
	$stmt = $this->dao->prepare( $sql );
	$stmt->bindValue( ':id', (int)$id, \PDO::PARAM_INT );
	$stmt->execute();
	
	return (bool)$stmt->rowCount();
}
}