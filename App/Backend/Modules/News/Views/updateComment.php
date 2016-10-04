<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 12:05
 */
?>
<form action = "" method = "post">
    <p>
        <?php
        if (isset($erreurs) && in_array(\Entity\Comment::AUTEUR_INVALIDE, $erreurs))
        {
           echo 'L\'auteur est invalide.';
        }
        ?>
        <br />

        <label>Pseudo</label>
        <input type = "text" name = "auteur" value = "<?= htmlspecialchars($comment['auteur']) ?>" />
        <br />

        <?php
        if (isset($erreurs) && in_array(\Entity\Comment::CONTENU_INVALIDE, $erreurs))
        {
            echo 'Le contenu est invalide.';
        }
        ?>
        <br />
        <label>Contenu</label>
        <textarea name = "contenu" rows = "7" cols = "50">
            <?= nl2br(htmlspecialchars($comment['contenu'])); ?>
        </textarea>
        <br />

        <input type = "hidden" name = "news" value = "<?= $comment['news'] ?>" />
        <input type = "submit" value = "Modifier" />
    </p>
</form>