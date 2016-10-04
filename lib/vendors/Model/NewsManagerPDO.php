<?php

/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 14:50
 */

namespace Model;

use Entity\News;

class NewsManagerPDO extends NewsManager
{
    /**
     * Récupère une liste de $count news, commençant par la news n° $start
     *
     * @param $offset
     * @param $limit
     * @return News[] Renvoi un tableau de news
     */
    public function getList($offset, $limit)
    {
        if ($offset < 0 OR $limit <= 0)
        {
            throw new InvalidArgumentException('Offset and limit values must be positive integers');
        }

        $sql = 'SELECT id, auteur, titre, contenu, dateAjout, dateModif
            FROM news ORDER BY id DESC LIMIT '.$limit.' OFFSET '.$offset;

        // Utiliser le dao pour exécuter la requête
        $query = $this -> dao -> query($sql);
        $query -> setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, '\Entity\News');
        $listeNews = $query -> fetchAll();

        // Ajouter les propriétés date "à la main"
        foreach ($listeNews as $news)
        {
            $news -> setDateAjout(new DateTime($news -> dateAjout()));
            $news -> setDateModif(new DateTime($news -> dateModif()));
        }

        $query -> closeCursor();

        return $listeNews;
    }

    public function getUnique($id)
    {
        if ($id < 0)
        {
            return NULL;
        }
        $sql = 'SELECT id, auteur, titre, contenu, dateAjout, dateModif
                FROM news
                WHERE id = :id';
        $query = $this -> dao -> prepare($sql);
        $query -> bindValue(':id', $id, \PDO::PARAM_INT);
        $query -> setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News');
        $query -> execute();
        $news = $query -> fetch();
        if ($news)
        {
            $news -> setDateAjout(new \DateTime($news -> dateAjout()));
            $news -> setDateModif(new \DateTime($news -> dateModif()));
            return $news;
        }
        return NULL;
    }

    public function count()
    {
        $sql = 'SELECT COUNT(*)
                FROM news';
        return $this -> dao -> query($sql) -> fetchColumn();
    }

    public function add(News $news)
    {
        $sql = 'INSERT INTO news (auteur, titre, contenu, dateAjout, dateModif)
                    VALUES (:auteur, :titre, :contenu, NOW(), NOW())';
        $query = $this -> dao -> prepare($sql);
        $query -> bindValue(':auteur', $news -> auteur(), \PDO::PARAM_STR);
        $query -> bindValue(':titre', $news -> titre(), \PDO::PARAM_STR);
        $query -> bindValue(':contenu', $news -> contenu(), \PDO::PARAM_STR);
        $query -> execute($sql);
    }

    public function modify(News $news)
    {
        $sql = 'UPDATE news
                SET auteur = :auteur, titre = :titre, contenu = :contenu, dateModif = NOW()
                WHERE id = :id';
        $query = $this -> dao -> prepare($sql);
        $query -> bindValue(':id', $news -> id(), \PDO::PARAM_INT);
        $query -> bindValue(':auteur', $news -> auteur(), \PDO::PARAM_STR);
        $query -> bindValue(':titre', $news -> titre(), \PDO::PARAM_STR);
        $query -> bindValue(':contenu', $news -> contenu(), \PDO::PARAM_STR);
        $query -> execute($sql);
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM news
                WHERE id = :id';
        $query = $this -> dao -> prepare($sql);
        $query -> bindValue(':id', (int) $id, \PDO::PARAM_INT);
        $query -> execute();
    }
}