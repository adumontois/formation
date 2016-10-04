<?php

/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 14:50
 */

namespace Entity;

use \OCFram\Entity;

class News extends Entity
{
    protected $auteur;
    protected $titre;
    protected $contenu;
    protected $dateAjout;
    protected $dateModif;

    const INVALID_AUTHOR = 1;
    const INVALID_TITLE = 2;
    const INVALID_CONTENT = 3;

    public function isValid()
    {
        return !empty($this -> auteur) AND !empty($this -> titre) AND !empty($this -> contenu);
    }

    public function setAuteur($auteur)
    {
        if (!empty($auteur) AND is_string($auteur))
        {
            $this -> auteur = $auteur;
        }
        else
        {
            $this -> erreurs[] = self::INVALID_AUTHOR;
        }
    }

    public function setTitre($titre)
    {
        if (!empty($titre) AND is_string($titre))
        {
            $this -> titre = $titre;
        }
        else
        {
            $this -> erreurs[] = self::INVALID_TITLE;
        }
    }

    public function setContenu($contenu)
    {
        if (!empty($contenu) AND is_string($contenu))
        {
            $this -> contenu = $contenu;
        }
        else
        {
            $this -> erreurs[] = self::INVALID_CONTENT;
        }
    }

    public function setDateAjout(\DateTime $date)
    {
        $this -> dateAjout = $date;
    }

    public function setDateModif(\DateTime $date)
    {
        $this -> dateModif = $date;
    }


    public function auteur()
    {
        return $this -> auteur;
    }

    public function titre()
    {
        return $this -> titre;
    }

    public function contenu()
    {
        return $this -> contenu;
    }

    public function dateAjout()
    {
        return $this -> dateAjout;
    }

    public function dateModif()
    {
        return $this -> dateModif;
    }


}