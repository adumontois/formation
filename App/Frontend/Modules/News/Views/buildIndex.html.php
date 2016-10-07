<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 15:34
 */
/**
 * @var $T_NEWS_BUILDINDEX_NEWS_LIST_A \Entity\News[]
 */
foreach ( $T_NEWS_BUILDINDEX_NEWS_LIST_A as $News ):
	?>
	<h2><a href="news-<?= $News[ 'id' ] ?>.html"><?= htmlspecialchars( $News[ 'titre' ] ) ?></a></h2>
	<?php // Besoin de nl2br pour afficher les contenus
	?>
	<p><?= nl2br( htmlspecialchars( $News[ 'contenu' ] ) ) ?></p>
	<?php
endforeach;