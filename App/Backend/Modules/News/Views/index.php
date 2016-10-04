<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 10:09
 */
?>

<p style="text-align: center">
    Il y a actuellement <?= $nombreNews ?> news. En voici la liste :
</p>

<table>
    <tr>
        <th>Auteur</th>
        <th>Titre</th>
        <th>Date d'ajout</th>
        <th>Dernière modification</th>
        <th>Action</th>
    </tr>
    <?php
    foreach ($listeNews as $news)
    {
        ?>
        <tr>
            <td>
                <?= $news['auteur'] ?>
            </td>
            <td>
                <?= $news['titre'] ?>
            </td>
            <td>
                le <?= $news['dateAjout'] -> format('d/m/Y à H\hi') ?>
            </td>
            <td>
                <?php
                if ($news['dateAjout'] != $news['dateModif'])
                {
                    echo 'le '.$news['dateModif'] -> format('d/m/Y à H\hi');
                }
                ?>
            </td>
            <td>
                <a href = <?= 'news-update-'.$news['id'].'.html' ?>><img src = "/images/update.png" alt = "Modifier" /></a>
                <a href = <?= 'news-delete-'.$news['id'].'.html' ?>><img src = "/images/delete.png" alt = "Supprimer" /></a>
            </td>
        </tr>
        <?php
    }
    ?>
</table>
