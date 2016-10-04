<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 16:19
 */
?>

<p>
    Par <em><?= htmlspecialchars($news['auteur']) ?></em>, le <?= $news['dateAjout'] -> format('d/m/Y à H\hi') ?>
</p>
<h2><?= htmlspecialchars($news['titre']) ?></h2>
<p><?= nl2br(htmlspecialchars($news['contenu'])) ?></p>

<?php
if ($news['dateAjout'] != $news['dateModif'])
{ ?>
    <p style="text-align: right;">
        <small><em>Modifiée le <?= $news['dateModif'] -> format('d/m/Y à H\hi') ?></em></small>
    </p>
<?php
}
?>

<p>
    <a href="commenter-<?= $news['id'] ?>.html">Ajouter un commentaire</a>
</p>

<?php
if (empty($listeCommentaires))
{
    ?>
    <p>
        Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !
    </p>
    <?php
}

foreach ($listeCommentaires as $comment)
{
    ?>
    <fieldset>
        <legend>
            Posté par <strong><?= htmlspecialchars($comment['auteur']) ?></strong> le <?= $comment['date'] -> format('d/m/Y à H\hi') ?>
            <?php if ($user -> isAuthenticated())
            {
                ?>
                - <a href="admin/comment-update-<?= $comment['id'] ?>.html">Modifier</a> |
                <a href="admin/comment-delete-<?= $comment['id'] ?>.html">Supprimer</a>
                <?php
            }
            ?>
        </legend>
        <p>
            <?= nl2br(htmlspecialchars($comment['contenu'])) ?>
        </p>
    </fieldset>
    <?php
}
?>

<p>
    <a href="commenter-<?= $news['id'] ?>.html">Ajouter un commentaire</a>
</p>