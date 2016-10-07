<?php

/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 14:50
 */

namespace Model;

use Entity\News;

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
		/**
		 * @var $Query \PDOStatement
		 * @var $News  News
		 */
		if ( $start < 0 OR $count <= 0 ) {
			throw new \InvalidArgumentException( 'Offset and limit values must be positive integers' );
		}
		
		$sql = 'SELECT SNC_id id, SNC_author auteur, SNC_title titre, SNC_content contenu, SNC_dateadd DateAjout, SNC_dateupdate DateModif
            FROM T_SIT_newsc ORDER BY SNC_id DESC LIMIT ' . $count . ' OFFSET ' . $start;
		
		// Utiliser le dao pour exécuter la requête
		$Query = $this->dao->query( $sql );
		
		$Query->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News' ); // Contresens avec la doc PHP.net
		$Liste_news_a = $Query->fetchAll();
		
		// Ajouter les propriétés date "à la main"
		foreach ( $Liste_news_a as $News ) {
			$News->setDateAjout( new \DateTime( $News->DateAjout() ) );
			$News->setDateModif( new \DateTime( $News->DateModif() ) );
		}
		
		$Query->closeCursor();
		
		return $Liste_news_a;
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
		 * @var $Query \PDOStatement
		 */
		$sql   = 'SELECT SNC_id id, SNC_author auteur, SNC_title titre, SNC_content contenu, SNC_dateadd DateAjout, SNC_dateupdate DateModif
                FROM T_SIT_newsc
                WHERE SNC_id = :id';
		$Query = $this->dao->prepare( $sql );
		$Query->bindValue( ':id', $newsc_id, \PDO::PARAM_INT );
		$Query->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News' ); // Contresens avec la doc PHP.net
		$Query->execute();
		$News = $Query->fetch();
		if ( $News ) {
			$News->setDateAjout( new \DateTime( $News->DateAjout() ) );
			$News->setDateModif( new \DateTime( $News->DateModif() ) );
			
			return $News;
		}
		
		return null;
	}
	
	/**
	 * Compte le nombre de news en DB.
	 *
	 * @return int
	 */
	public function countNewscUsingNewscId() {
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
	protected function insertNewsc( News $News ) {
		/**
		 * @var $Query \PDOStatement
		 * @var $News  News
		 */
		$sql   = 'INSERT INTO T_SIT_newsc (SNC_author, SNC_title, SNC_content, SNC_dateadd, SNC_dateupdate)
                    VALUES (:auteur, :titre, :contenu, NOW(), NOW())';
		$Query = $this->dao->prepare( $sql );
		$Query->bindValue( ':auteur', $News->auteur(), \PDO::PARAM_STR );
		$Query->bindValue( ':titre', $News->titre(), \PDO::PARAM_STR );
		$Query->bindValue( ':contenu', $News->contenu(), \PDO::PARAM_STR );
		$Query->execute();
	}
	
	/**
	 * Modifie la news en DB.
	 * Cette méthode ne doit pas être appelée directement ; utiliser la méthode publique save.
	 *
	 * @param News $News
	 */
	protected function updateNewsc( News $News ) {
		/**
		 * @var $Query \PDOStatement
		 * @var $News  News
		 */
		$sql   = 'UPDATE T_SIT_newsc
                SET SNC_author = :auteur, SNC_title = :titre, SNC_content = :contenu, SNC_dateupdate = NOW()
                WHERE SNC_id = :id';
		$Query = $this->dao->prepare( $sql );
		$Query->bindValue( ':id', $News->id(), \PDO::PARAM_INT );
		$Query->bindValue( ':auteur', $News->auteur(), \PDO::PARAM_STR );
		$Query->bindValue( ':titre', $News->titre(), \PDO::PARAM_STR );
		$Query->bindValue( ':contenu', $News->contenu(), \PDO::PARAM_STR );
		$Query->execute();
	}
	
	/**
	 * Supprime la news d'id donné de la DB.
	 * Renvoie true si la news existait, false sinon.
	 *
	 * @param $id int
	 *
	 * @return bool
	 */
	public function deleteNewscUsingNewscId( $id ) {
		/**
		 * @var $Query \PDOStatement
		 */
		$sql   = 'DELETE FROM T_SIT_newsc
                WHERE SNC_id = :id';
		$Query = $this->dao->prepare( $sql );
		$Query->bindValue( ':id', (int)$id, \PDO::PARAM_INT );
		$Query->execute();
		
		return (bool)$Query->rowCount();
	}
	
	/**
	 * Insère ou met à jour la news en DB selon qu'il existe déjà ou non en base.
	 *
	 * @param News $News
	 *
	 */
	public function save( News $News ) {
		if ( $News->isValid() ) {
			if ( $News->objectNew() ) {
				$this->insertNewsc( $News );
			}
			
			else {
				$this->updateNewsc( $News );
			}
		}
	}
}