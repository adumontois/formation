<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 16:37
 */

namespace Entity;


use OCFram\Entity;

class Comment extends Entity
{
    protected $news;
    protected $auteur;
    protected $contenu;
    protected $date;

    const INVALID_AUTHOR = 1;
    const INVALID_CONTENT = 2;
    const INVALID_NEWS = 3;

    public function isValid()
    {
        return !empty($this -> auteur) AND !empty($this -> contenu);
    }

    public function news()
    {
        return $this -> news;
    }

    public function auteur()
    {
        return $this -> auteur;
    }

    public function contenu()
    {
        return $this -> contenu;
    }

    public function date()
    {
        return $this -> date;
    }

    public function setNews($news)
    {
        if (!is_int($news))
        {
            $this -> erreurs[] = self::INVALID_NEWS;
        }
        else
        {
            $this -> news = $news;
        }
    }

    public function setAuteur($auteur)
    {
        if (!is_string($auteur) OR empty($auteur))
        {
            $this -> erreurs[] = self::INVALID_AUTHOR;
        }
        else
        {
            $this -> auteur = $auteur;
        }
    }

    public function setContenu($contenu)
    {
        if (!is_string($contenu) OR empty($contenu))
        {
            $this -> erreurs[] = self::INVALID_CONTENT;
        }
        else
        {
            $this -> contenu = $contenu;
        }
    }

    public function setDate(\DateTime $date)
    {
        $this -> date = $date;
    }
}