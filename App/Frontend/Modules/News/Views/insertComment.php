<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 16:49
 */
?>

<h2>
    Ajouter un commentaire
</h2>
<form action="" method="post">
<!-- Action vide = déclencher la page courante -->
    <p>
        <?php
        if (isset($erreurs) && in_array(\Entity\Comment::AUTEUR_INVALIDE, $erreurs))
        {
            echo 'L\'auteur est invalide.<br />';
        }
        ?>
        <label>Pseudo</label>
        <input type="text" name="pseudo" value="<?= isset($comment) ? htmlspecialchars($comment['auteur']) : '' ?>" />
        <br />
        <?php
        if (isset($erreurs) && in_array(\Entity\Comment::CONTENU_INVALIDE, $erreurs))
        {
            echo 'Le contenu est invalide.<br />';
        }
        ?>
        <label>Contenu</label>
        <textarea name="contenu" rows="7" cols="50"><?= isset($comment) ? htmlspecialchars($comment['contenu']) : '' ?></textarea><br />

        <input type="submit" value="Commenter" />
    </p>
</form>