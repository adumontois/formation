<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 15:34
 */

    foreach ($listeNews as $news)
    {
        ?>
        <h2><a href = "news-<?= $news['id'] ?>.html"><?= htmlspecialchars($news['titre']) ?></a></h2>
        <!-- Besoin de nl2br pour afficher les contenus -->
        <p><?= nl2br(htmlspecialchars($news['contenu'])) ?></p>
        <?php
    }