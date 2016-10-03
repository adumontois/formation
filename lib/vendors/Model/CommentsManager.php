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

abstract class CommentsManager extends Manager
{
    // Méthode qui sauvegarde (insert ou update selon le besoin) un commentaire
    public function save(Comment $comment)
    {
        if ($comment -> isValid())
        {
            if ($comment -> object_new())
            {
                $this -> add($comment);
            }
            else
            {
                $this -> modify($comment);
            }
        }
        else
        {
            throw new \RuntimeException('Invalid comment, it couldn\'t be registered');
        }
    }


    // Méthode qui insère dans la DB
    abstract protected function add(Comment $comment);

    // Méthode qui update dans la DB
    abstract protected function modify(Comment $comment);

    // Méthode qui renvoie les news d'id donné
    abstract public function getListOf($id);

}