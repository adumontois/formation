<?php

/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 14:50
 */

namespace Model;

use OCFram\Manager;

abstract class NewsManager extends Manager
{
    // Récupère une liste de $count news, commençant par la news n° $start
    // Renvoie un tableau de news
    abstract public function getList($start, $count);

    // Récupère la news correspondant à un id donné
    abstract public function getUnique($id);
}