<?php

/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 14:50
 */

namespace Model;

use Entity\News;
use OCFram\Manager;

abstract class NewsManager extends Manager
{
    // Récupère une liste de $count news, commençant par la news n° $start
    // Renvoie un tableau de news
    abstract public function getList($start, $count);

    // Récupère la news correspondant à un id donné
    abstract public function getUnique($id);

    // Compte le nombre de news en DB
    abstract public function count();

    // Insert ou update une news selon si elle existe déjà ou non
    public function save(News $news)
    {
        if (!$news -> isValid())
        {
            throw new \RuntimeException('Couldn\'t save the news : invalid news given');
        }
        else
        {
            if ($news -> object_new())
            {
                $this -> add($news);
            }
            else
            {
                $this -> modify($news);
            }
        }
    }

    // Insère une nouvelle news
    abstract public function add(News $news);

    // Edite une news
    abstract public function modify(News $news);

    // Supprime une news
    abstract public function delete($id);
}