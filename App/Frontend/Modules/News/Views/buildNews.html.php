<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 16:19
 */

/**
 * @var $News              \Entity\News News à afficher
 * @var $Comment_list_a    \Entity\Comment[] Liste des commentaires à afficher
 * @var $link_a string[] Liste des liens à afficher
 */

?>

<p>
	Par <em><?= htmlspecialchars( $News[ 'User' ] ) ?></em>, le <?= $News[ 'dateadd' ] ?>
</p>
<h2><?= htmlspecialchars( $News[ 'title' ] ) ?></h2>
<p><?= nl2br( htmlspecialchars( $News[ 'content' ] ) ) ?></p>

<?php if ( $News[ 'dateadd' ] != $News[ 'dateupdate' ] ): ?>
	<p style="text-align: right;">
		<small><em>Modifiée le <?= $News[ 'dateupdate' ] ?></em></small>
	</p>
<?php endif; ?>

<!-- Début de formulaire Ajax -->
<p id="test">
	<a href="<?= $link_a['putInsertComment'] ?>">Ajouter un commentaire</a>
</p>
<!-- Fin de formulaire Ajax -->

<?php if ( empty( $Comment_list_a ) ): ?>
	<p>
		Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !
	</p>
<?php endif; ?>

<?php foreach ( $Comment_list_a as $Comment ): ?>
	<fieldset>
		<legend>
			Posté par <strong><?= htmlspecialchars( $Comment[ 'author' ] ) ?></strong> le <?= $Comment[ 'date' ] ?>
			<?php if (!empty($Comment['action_a'])): ?>
				-
				<?php foreach ($Comment['action_a'] as $action_a): ?>
					<a href=<?= $action_a['link'] ?>><?= $action_a['label'] ?></a>
				<?php endforeach; ?>
			<?php endif; ?>
		</legend>
		<p>
			<?= nl2br( htmlspecialchars( $Comment[ 'content' ] ) ) ?>
		</p>
	</fieldset>
<?php endforeach; ?>

<!-- Début de formulaire Ajax -->
<p>
	<a href="<?= $link_a['putInsertComment'] ?>">Ajouter un commentaire</a>
</p>
<!-- Fin de formulaire Ajax -->