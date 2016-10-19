<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 18/10/2016
 * Time: 16:39
 */

/**
 * @var \Entity\User      $User            L'utilisateur dont on souhaite afficher la fiche membre
 * @var \Entity\News[]    $News_owned_a    Liste des News postées par le membre
 * @var \Entity\News[]    $News_others_a   Liste des News auxquelles le membre a participé
 * @var \Entity\Comment[] $Comment_owned_a Liste des commentaires de l'utilisateur
 */
?>

<!-- Affichage des infos membre -->
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
<!-- Affichage des news postées par le membre -->
<?php if ( !empty( $News_owned_a ) ): ?>
	<fieldset>
		<h2>
			<?= htmlspecialchars( $User[ 'login' ] ) ?> est l'auteur des posts suivants :
		</h2>
		<?php foreach ( $News_owned_a as $News ): ?>
			<fieldset>
				<!-- Afficher la News -->
				<p>
					"<?= htmlspecialchars( $News[ 'title' ] ) ?>", par <strong><?= htmlspecialchars( $News[ 'User' ] ) ?></strong>
					<br />
					News publiée le <?= $News[ 'dateadd' ] ?>
					<?php if ( $News[ 'dateadd' ] != $News[ 'dateupdate' ] ): ?>
						- <strong>modifiée le <?= $News[ 'dateupdate' ] ?></strong>
					<?php endif; ?>
					<?php foreach ( $News[ 'action_a' ] as $action_a ): ?>
						<a href="<?= $action_a[ 'action_link' ] ?>"><img src="<?= $action_a[ 'image_source' ] ?>" alt="<?= $action_a[ 'alternative_text' ] ?>" /></a>
					<?php endforeach; ?>
					<p>
						<?= $News['content'] ?>
					</p>
				</p>
				
				<!-- Afficher les commentaires pour la News -->
				<?php foreach ( array_filter( $Comment_owned_a, function( \Entity\Comment $Comment) use ($News) {
					return (int)$Comment->fk_SNC() === (int)$News[ 'id' ];
				} ) as $key => $Comment ): ?>
			
					<fieldset>
						<legend>
							Posté par <strong><?= htmlspecialchars( $Comment[ 'author' ] ) ?></strong> le <?= $Comment[ 'datecreation' ] ?>
							<?php if ( $Comment[ 'datecreation' ] != $Comment[ 'dateupdate' ] ): ?>
								- <strong>modifié le <?= $Comment[ 'dateupdate' ] ?></strong>
							<?php endif; ?>
							<?php if ( !empty( $Comment[ 'action_a' ] ) ): ?>
								-
								<?php foreach ( $Comment[ 'action_a' ] as $action_a ): ?>
									<a href="<?= $action_a[ 'action_link' ] ?>"><img src="<?= $action_a[ 'image_source' ] ?>" alt="<?= $action_a[ 'alternative_text' ] ?>" /></a>
								<?php endforeach; ?>
							<?php endif; ?>
						</legend>
						<p>
							<?= nl2br( htmlspecialchars( $Comment[ 'content' ] ) ) ?>
						</p>
					</fieldset>
					<?php unset($Comment_owned_a[$key]); ?>
				<?php endforeach; ?>
			</fieldset>
			<br />
			<br />
		<?php endforeach; ?>
	</fieldset>
	<br />
	<?php if (!empty($Comment_owned_a)): ?>
		<fieldset>
			<h2>
				<?= htmlspecialchars( $User[ 'login' ] ) ?> a aussi contribué aux news suivantes :
			</h2>
			<?php foreach ( $News_others_a as $News ): ?>
				<fieldset>
					<!-- Afficher la News -->
					<p>
						"<?= htmlspecialchars( $News[ 'title' ] ) ?>", par <strong><?= htmlspecialchars( $News[ 'User' ] ) ?></strong>
						<br />
						News publiée le <?= $News[ 'dateadd' ] ?>
						<?php if ( $News[ 'dateadd' ] != $News[ 'dateupdate' ] ): ?>
							- <strong>modifiée le <?= $News[ 'dateupdate' ] ?></strong>
						<?php endif; ?>
						<?php foreach ( $News[ 'action_a' ] as $action_a ): ?>
							<a href="<?= $action_a[ 'action_link' ] ?>"><img src="<?= $action_a[ 'image_source' ] ?>" alt="<?= $action_a[ 'alternative_text' ] ?>" /></a>
						<?php endforeach; ?>
						<p>
							<?= $News['content'] ?>
						</p>
					</p>
					
					<!-- Afficher les commentaires pour la News -->
					<?php foreach ( array_filter( $Comment_owned_a, function( \Entity\Comment $Comment ) use ($News) {
						/** @var \Entity\News $News */
						return (int)$Comment->fk_SNC() === (int)$News[ 'id' ];
					} ) as $key => $Comment ): ?>
						<fieldset>
							<legend>
								Posté par <strong><?= htmlspecialchars( $Comment[ 'author' ] ) ?></strong> le <?= $Comment[ 'datecreation' ] ?>
								<?php if ( $Comment[ 'datecreation' ] != $Comment[ 'dateupdate' ] ): ?>
									- <strong>modifié le <?= $Comment[ 'dateupdate' ] ?></strong>
								<?php endif; ?>
								<?php if ( !empty( $Comment[ 'action_a' ] ) ): ?>
									-
									<?php foreach ( $Comment[ 'action_a' ] as $action_a ): ?>
										<a href="<?= $action_a[ 'action_link' ] ?>"><img src="<?= $action_a[ 'image_source' ] ?>" alt="<?= $action_a[ 'alternative_text' ] ?>" /></a>
									<?php endforeach; ?>
								<?php endif; ?>
							</legend>
							<p>
								<?= nl2br( htmlspecialchars( $Comment[ 'content' ] ) ) ?>
							</p>
						</fieldset>
						<?php // Retirer l'élément traité du tableau ?>
						<?php unset($Comment_owned_a[$key]); ?>
					<?php endforeach; ?>
				</fieldset>
				<br />
				<br />
			<?php endforeach; ?>
		</fieldset>
	<?php else: ?>
		<p>
			<?= htmlspecialchars( $User[ 'login' ] ) ?> n'a publié aucun commentaire sur les news d'autres contributeurs.
		</p>
	<?php endif; ?>
<?php else: ?>
<p>
	<?= htmlspecialchars( $User[ 'login' ] ) ?> n'a pour l'heure publié ni news, ni commentaire.
</p>
<?php endif; ?>
