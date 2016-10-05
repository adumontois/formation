<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 10:09
 */

/**
 * @var $nbNews integer
 */
?>

<p style="text-align: center">
	Il y a actuellement <?= $nbNews ?> news. En voici la liste :
</p>

<table>
	<tr>
		<th>Auteur</th>
		<th>Titre</th>
		<th>Date d'ajout</th>
		<th>Dernière modification</th>
		<th>Action</th>
	</tr>
	<?php
	/**
	 * @var $listeNews \Entity\News[]
	 */
	foreach ( $listeNews as $news ):
		?>
		<tr>
			<td>
				<?= htmlspecialchars( $news[ 'auteur' ] ) ?>
			</td>
			<td>
				<?= htmlspecialchars( $news[ 'titre' ] ) ?>
			</td>
			<td>
				le <?= 	$news[ 'dateAjout' ]->format( 'd/m/Y à H\hi' ) ?>
			</td>
			<td>
				<?php
				if ( $news[ 'dateAjout' ] != $news[ 'dateModif' ] ):
					echo 'le ' . $news[ 'dateModif' ]->format( 'd/m/Y à H\hi' );
				endif;
				?>
			</td>
			<td>
				<a href= <?= 'news-update-' . $news[ 'id' ] . '.html' ?>><img src="/Web/images/update.png" alt="Modifier" /></a>
				<a href= <?= 'news-delete-' . $news[ 'id' ] . '.html' ?>><img src="/Web/images/delete.png" alt="Supprimer" /></a>
			</td>
		</tr>
		<?php
	endforeach;
	?>
</table>
