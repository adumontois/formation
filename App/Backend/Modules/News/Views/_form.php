<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 10:58
 */
?>

<form action = "" method = "post">
    <p>
        <?php
        if (isset($erreurs) && in_array(\Entity\News::AUTEUR_INVALIDE, $erreurs))
        {
            echo 'L\'auteur est invalide.<br />';
        }
        ?>
        <label>Auteur</label>
        <input type = "text" name = "auteur" value = "<?= isset($news) ? $news['auteur'] : '' ?>" />
        <br />

        <?php
        if (isset($erreurs) && in_array(\Entity\News::TITRE_INVALIDE, $erreurs))
        {
            echo 'Le titre est invalide.<br />';
        }
        ?>
        <label>Titre</label>
        <input type = "text" name = "titre" value = "<?= isset($news) ? $news['titre'] : '' ?>" />
        <br />

        <?php
        if (isset($erreurs) && in_array(\Entity\News::CONTENU_INVALIDE, $erreurs))
        {
           echo 'Le contenu est invalide.<br />';
        }
        ?>
        <label>Contenu</label>
        <textarea rows = "8" cols = "60" name = "contenu"><?= isset($news) ? $news['contenu'] : '' ?></textarea>
        <br />
        <?php
        if (isset($news) && !$news -> isNew())
        {
            ?>
            <input type = "hidden" name = "id" value = "<?= $news['id'] ?>" />
            <input type = "submit" value = "Modifier" name = "modifier" />
            <?php
        }
        else
        {
            ?>
            <input type = "submit" value = "Ajouter" />
            <?php
        }
        ?>
    </p>
</form>
