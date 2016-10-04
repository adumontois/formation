<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 14:48
 */

namespace App\Frontend\Modules\News;

use Entity\Comment;
use \OCFram\BackController;
use \OCFram\HTTPRequest;
use OCFram\MaxLengthValidator;
use OCFram\NotNullValidator;
use OCFram\StringField;
use OCFram\TextField;

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
        $manager = $this -> managers -> getManagerOf('News');

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

    public function executeShow(HTTPRequest $request)
    {
        $id = $request -> getData('id');
        $manager = $this -> managers -> getManagerOf('News');
        $news = $manager -> getUnique($id);

        if (empty($news))
        // Si la news n'existe pas on redirige
        {
            $this -> app -> httpResponse() -> redirect404();
            exit;
        }

        // Afficher les commentaires
        $listeComments = $this -> managers -> getManagerOf('Comments') -> getListOf($news -> id());

        $this -> page -> addVar('titre', $news -> titre);
        $this -> page -> addVar('news', $news);
        $this -> page -> addVar('listeComments', $listeComments);
    }

    public function executeInsertComment(HTTPRequest $request)
    {
        $commentaire = new Comment();
        if ($request -> method() == 'POST')
        {
            $commentaire -> setNews($request -> getData('news'));
            $commentaire -> setAuteur($request -> postData('pseudo'));
            $commentaire -> setContenu($request -> postData('contenu'));
        }

        // Création du formulaire dans le contrôleur


        if ($form -> isValid())
        {
            $this -> managers -> getManagerOf('Comments') -> save($commentaire);
            $this -> app -> user() -> setFlash('Votre commentaire a bien été ajouté.');
            $this -> app -> httpResponse() -> redirect('news-'.$request -> getData('news').'.html');
        }
        else
        {
            $this -> page -> addVar('erreurs', $commentaire -> erreurs());
        }
        $this -> page -> addVar('title', 'Ajout d\'un commentaire');
        $this -> page -> addVar('comment', $commentaire);
        // Passer le formulaire à la vue
        $this -> page -> addVar('form', $form -> createView());
    }
}