<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 16:19
 */

/**
 * @var $T_NEWS_BUILDNEWS_NEWS              \Entity\News
 * @var $T_NEWS_BUILDNEWS_COMMENT_LIST_A    \Entity\Comment[]
 * @var $T_NEWS_BUILDNEWS_USER              OCFram\User
 *
 * @var $T_NEWS_BUILDNEWS_NEWS              ['DateAjout'] DateTime (OK)
 * @var $T_NEWS_BUILDNEWS_NEWS              ['DateModif'] DateTime (OK)
 * @var $comment                            ['Date'] DateTime (OK)
 */

?>

<p>
	Par <em><?= htmlspecialchars( $T_NEWS_BUILDNEWS_NEWS[ 'auteur' ] ) ?></em>, le <?= $T_NEWS_BUILDNEWS_NEWS[ 'DateAjout' ]->format( 'd/m/Y à H\hi' ) ?>
</p>
<h2><?= htmlspecialchars( $T_NEWS_BUILDNEWS_NEWS[ 'titre' ] ) ?></h2>
<p><?= nl2br( htmlspecialchars( $T_NEWS_BUILDNEWS_NEWS[ 'contenu' ] ) ) ?></p>

<?php
if ( $T_NEWS_BUILDNEWS_NEWS[ 'DateAjout' ] != $T_NEWS_BUILDNEWS_NEWS[ 'DateModif' ] ): ?>
	<p style="text-align: right;">
		<small><em>Modifiée le <?= $T_NEWS_BUILDNEWS_NEWS[ 'DateModif' ]->format( 'd/m/Y à H\hi' ) ?></em></small>
	</p>
	<?php
endif;
?>

<p>
	<a href="commenter-<?= $T_NEWS_BUILDNEWS_NEWS[ 'id' ] ?>.html">Ajouter un commentaire</a>
</p>

<?php
if ( empty( $T_NEWS_BUILDNEWS_COMMENT_LIST_A ) ):
	?>
	<p>
		Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !
	</p>
	<?php
endif;

foreach ( $T_NEWS_BUILDNEWS_COMMENT_LIST_A as $comment ):
	?>
	<fieldset>
		<legend>
			Posté par <strong><?= htmlspecialchars( $comment[ 'auteur' ] ) ?></strong> le <?= $comment[ 'Date' ]->format( 'd/m/Y à H\hi' ) ?>
			<?php if ( $T_NEWS_BUILDNEWS_USER->isAuthenticated() ):
				?>
				- <a href="admin/comment-update-<?= $comment[ 'id' ] ?>.html">Modifier</a> |
				<a href="admin/comment-delete-<?= $comment[ 'id' ] ?>.html">Supprimer</a>
				<?php
			endif;
			?>
		</legend>
		<p>
			<?= nl2br( htmlspecialchars( $comment[ 'contenu' ] ) ) ?>
		</p>
	</fieldset>
	<?php
endforeach;
?>

<p>
	<a href="commenter-<?= $T_NEWS_BUILDNEWS_NEWS[ 'id' ] ?>.html">Ajouter un commentaire</a>
</p>