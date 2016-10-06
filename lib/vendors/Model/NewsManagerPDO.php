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
	public function getList( $start = 0, $count = parent::MAX_LIST_SIZE ) {
		/**
		 * @var $query \PDOStatement
		 * @var $news  News
		 */
		if ( $start < 0 OR $count <= 0 ) {
			throw new \InvalidArgumentException( 'Offset and limit values must be positive integers' );
		}

		$sql = 'SELECT id, auteur, titre, contenu, dateAjout, dateModif
            FROM news ORDER BY id DESC LIMIT ' . $count . ' OFFSET ' . $start;
		
		// Utiliser le dao pour exécuter la requête
		$query = $this->dao->query( $sql );

		$query->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News' ); // Contresens avec la doc PHP.net
		$listeNews = $query->fetchAll();

		// Ajouter les propriétés date "à la main"
		foreach ( $listeNews as $news ) {
			$news->setDateAjout( new \DateTime( $news->dateAjout() ) );
			$news->setDateModif( new \DateTime( $news->dateModif() ) );
		}
		
		$query->closeCursor();
		
		return $listeNews;
	}
	
	/**
	 * Récupère la news d'id donné.
	 *
	 * @param $id int
	 *
	 * @return null|News
	 */
	public function getUnique( $id ) {
		/**
		 * @var $query \PDOStatement
		 */
		if ( $id < 0 ) {
			return null;
		}
		$sql   = 'SELECT id, auteur, titre, contenu, dateAjout, dateModif
                FROM news
                WHERE id = :id';
		$query = $this->dao->prepare( $sql );
		$query->bindValue( ':id', $id, \PDO::PARAM_INT );
		$query->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News' ); // Contresens avec la doc PHP.net
		$query->execute();
		$news = $query->fetch();
		if ( $news ) {
			$news->setDateAjout( new \DateTime( $news->dateAjout() ) );
			$news->setDateModif( new \DateTime( $news->dateModif() ) );
			
			return $news;
		}
		
		return null;
	}
	
	/**
	 * Compte le nombre de news en DB.
	 *
	 * @return int
	 */
	public function count() {
		$sql = 'SELECT COUNT(*)
                FROM news';
		
		return $this->dao->query( $sql )->fetchColumn();
	}
	
	/**
	 * Insère la news passée en paramètre en DB.
	 *
	 *
	 * @param Entity $news News à insérer
	 */
	protected function add( Entity $news ) {
		/**
		 * @var $query \PDOStatement
		 * @var $news News
		 */
		$sql   = 'INSERT INTO news (auteur, titre, contenu, dateAjout, dateModif)
                    VALUES (:auteur, :titre, :contenu, NOW(), NOW())';
		$query = $this->dao->prepare( $sql );
		$query->bindValue( ':auteur', $news->auteur(), \PDO::PARAM_STR );
		$query->bindValue( ':titre', $news->titre(), \PDO::PARAM_STR );
		$query->bindValue( ':contenu', $news->contenu(), \PDO::PARAM_STR );
		$query->execute();
	}
	
	/**
	 * Met à jour la news passée en paramètre en DB.
	 *
	 * @param Entity $news News à modifier
	 */
	protected function modify( Entity $news ) {
		/**
		 * @var $query \PDOStatement
		 * @var $news News
		 */
		$sql   = 'UPDATE news
                SET auteur = :auteur, titre = :titre, contenu = :contenu, dateModif = NOW()
                WHERE id = :id';
		$query = $this->dao->prepare( $sql );
		$query->bindValue( ':id', $news->id(), \PDO::PARAM_INT );
		$query->bindValue( ':auteur', $news->auteur(), \PDO::PARAM_STR );
		$query->bindValue( ':titre', $news->titre(), \PDO::PARAM_STR );
		$query->bindValue( ':contenu', $news->contenu(), \PDO::PARAM_STR );
		$query->execute( $sql );
	}
	
	/**
	 * Supprime la news d'id donné de la DB.
	 *
	 * @param $id int
	 */
	public function delete( $id ) {
		/**
		 * @var $query \PDOStatement
		 */
		$sql   = 'DELETE FROM news
                WHERE id = :id';
		$query = $this->dao->prepare( $sql );
		$query->bindValue( ':id', (int)$id, \PDO::PARAM_INT );
		$query->execute();
	}
}