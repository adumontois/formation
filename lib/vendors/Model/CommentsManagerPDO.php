<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 16:35
 */

namespace Model;

use Entity\Comment;


class CommentsManagerPDO extends CommentsManager
{
    protected function add(Comment $comment)
    {
        $sql = 'INSERT INTO comments
                    (news, auteur, contenu, date)
                VALUES (:news, :auteur, :contenu, NOW())';

        $query = $this -> dao -> prepare($sql);
        $query -> bindValue(':news', $comment -> news(), \PDO::PARAM_INT);
        $query -> bindValue(':auteur', $comment -> auteur(), \PDO::PARAM_STR);
        $query -> bindValue(':contenu', $comment -> contenu(), \PDO::PARAM_STR);
        $query -> execute();
        $comment -> setId($this -> dao -> lastInsertId());
    }

    protected function modify(Comment $comment)
    {
        $sql = 'UPDATE FROM comments
                SET news = :news, auteur = :auteur, contenu = :contenu, date = NOW()
                WHERE id = :id';

        $query = $this -> dao -> prepare($sql);
        $query -> bindValue(':news', $comment -> news(), \PDO::PARAM_INT);
        $query -> bindValue(':auteur', $comment -> auteur(), \PDO::PARAM_STR);
        $query -> bindValue(':contenu', $comment -> contenu(), \PDO::PARAM_STR);
        $query -> bindValue(':id', $comment -> id(), \PDO::PARAM_INT);
        $query -> execute();
    }

    public function getListOf($id)
    {
        if (!ctype_digit($id))
        {
            throw new \RuntimeException('News id must be an integer value');
        }

        $sql = 'SELECT id, news, auteur, contenu, date
                FROM comments
                WHERE news = :news
                ORDER BY id DESC';

        $query = $this -> dao -> prepare($sql);
        $query -> bindValue(':id', $id, \PDO::PARAM_INT);
        $query -> setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'Entity\Comment');
        $listeComments = $query -> fetchAll();
        foreach ($listeComments as $comment)
        {
            $comment -> setDate(new \DateTime($comment -> date()));
        }
        $query -> closeCursor();
        return $listeComments;
    }
}