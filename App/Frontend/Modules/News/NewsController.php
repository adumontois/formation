<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 14:48
 */

namespace App\Frontend;

use OCFram\BackController;
use \OCFram\HTTPRequest;

class NewsController extends BackController
{
    public function executeIndex(HTTPRequest $request)
    {
        // Récupérer la config
        $nombre_news = $this -> app() -> config() -> get('nombre_news');
        $longueur_news = $this -> app() -> config() -> get('longueur_news');

        // Ajouter un titre à la page
        $this -> page -> addVar('title', 'List of '.$nombre_news.' last news');

        // Récupérer le manager des news
        $this -> managers -> getManagerOf('News');

        // Récupérer la liste des news à afficher
        $listeNews = $manager -> getList(0, $nombre_news);

        foreach ($listeNews as $news)
        {
            // Prendre le nombre de caractères nécessaires
            $news -> setContenu(substr($news -> contenu(), 0, $longueur_news));
            if (strlen($news) == $longueur_news)
            {
                $news -> setContenu(substr($news -> contenu(), 0, strrpos($news -> contenu())).'...');
            }
        }
        $this -> page -> addVar('listeNews', $listeNews);
    }
}