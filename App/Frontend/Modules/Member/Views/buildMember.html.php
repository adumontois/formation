<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 18/10/2016
 * Time: 16:39
 */

/**
 * @var \Entity\User   $User   L'utilisateur dont on souhaite afficher la fiche membre
 * @var \Entity\News[] $News_a Liste des News à afficher avec les commentaires correspondants
 */
?>
	
	<?php // Affichage des infos membre ?>
	<fieldset>
		<h2>
			Fiche de <?= htmlspecialchars( $User[ 'login' ] ) ?>
		</h2>
		<p>
			Login : <?= htmlspecialchars( $User[ 'login' ] ) ?> (<?= \Entity\User::getTextualValid( $User[ 'fk_SUE_valid' ] ) ?>, <?= \Entity\User::getTextualBanned( $User[ 'fk_SUE_banned' ] ) ?>)
			<br />
			<?= htmlspecialchars( $User[ 'login' ] ) ?> est un <?= lcfirst( \Entity\User::getTextualStatus( $User[ 'fk_SUY' ] ) ) ?>. Il s'est inscrit le <?= $User[ 'datesubscription' ] ?>.
			<br />
			Vous pouvez contacter <?= htmlspecialchars( $User[ 'login' ] ) ?> via son adresse mail : <?= htmlspecialchars( $User[ 'email' ] ) ?>
		</p>
	</fieldset>
	<br />

<?php if ( !empty( $News_a ) ): ?>
	<fieldset>
		<h2>
			<?= htmlspecialchars( $User[ 'login' ] ) ?> est l'auteur des posts suivants :
		</h2>
		<?php foreach ( $News_a as $News ): ?>
		<fieldset>
			<?php // Afficher la News ?>
			<p>
				<?= htmlspecialchars( $News[ 'title' ] ) ?>, par <strong>
					<?php if ( $News[ 'User' ][ 'id' ] != $User[ 'id' ] ): ?>
					<a href="<?= $News[ 'User' ][ 'link' ] ?>">
						<?php endif; ?>
						<?= htmlspecialchars( $News[ 'User' ] ) ?>
						<?php if ( $News[ 'User' ][ 'id' ] != $User[ 'id' ] ): ?>
					</a> <?php endif; ?></strong>
				<br />
				News publiée le <?= $News[ 'dateadd' ] ?>
				<?php if ( $News[ 'dateadd' ] != $News[ 'dateupdate' ] ): ?>
					- <strong>modifiée le <?= $News[ 'dateupdate' ] ?></strong>
				<?php endif; ?>
				<?php foreach ( $News[ 'link_a' ] as $link_a ): ?>
					<a href="<?= $link_a[ 'url' ] ?>"><img src="<?= $link_a[ 'src' ] ?>" alt="<?= $link_a[ 'alt' ] ?>" /><?= $link_a[ 'label' ] ?></a>
				<?php endforeach; ?>
			</p>
			<p>
				<?= $News[ 'content' ] ?>
			</p>
			<?php foreach ( $News[ 'Comment_a' ] as $Comment ): ?>
			<fieldset>
				<legend>
					Posté par <strong><?= htmlspecialchars( $Comment[ 'author' ] ) ?></strong> le <?= $Comment[ 'datecreation' ] ?>
					<?php if ( $Comment[ 'datecreation' ] != $Comment[ 'dateupdate' ] ): ?>
						- <strong>modifié le <?= $Comment[ 'dateupdate' ] ?></strong>
					<?php endif; ?>
					<?php if ( !empty( $Comment[ 'link_a' ] ) ): ?>
						-
						<?php foreach ( $Comment[ 'link_a' ] as $link_a ): ?>
							<a href="<?= $link_a[ 'url' ] ?>"><img src="<?= $link_a[ 'src' ] ?>" alt="<?= $link_a[ 'alt' ] ?>" /><?= $link_a[ 'label' ] ?></a>
						<?php endforeach; ?>
					<?php endif; ?>
				</legend>
				<p>
					<?= nl2br( htmlspecialchars( $Comment[ 'content' ] ) ) ?>
				</p>
			</fieldset>
			<br />
			<?php endforeach; ?>
		</fieldset>
		<br />
		<br />
		<?php endforeach; ?>
	</fieldset>
<?php endif; ?>