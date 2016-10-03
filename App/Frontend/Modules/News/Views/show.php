
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 16:19
 */

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