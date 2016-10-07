<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 10:09
 */

/**
 * @var $news_count integer
 */
?>
	
	<p style="text-align: center">
		Il y a actuellement <?= $news_count ?> news. En voici la liste :
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
		 * @var $News_list_a \Entity\News[]
		 */
		foreach ( $News_list_a as $News ):
			?>
			<tr>
				<td>
					<?= htmlspecialchars( $News[ 'auteur' ] ) ?>
				</td>
				<td>
					<?= htmlspecialchars( $News[ 'titre' ] ) ?>
				</td>
				<td>
					le <?= $News[ 'DateAjout' ]->format( 'd/m/Y à H\hi' ) ?>
				</td>
				<td>
					<?php
					if ( $News[ 'DateAjout' ] != $News[ 'DateModif' ] ):
						echo 'le ' . $News[ 'DateModif' ]->format( 'd/m/Y à H\hi' );
					endif;
					?>
				</td>
				<td>
					<a href= <?= 'news-update-' . $News[ 'id' ] . '.html' ?>><img src="../images/update.png" alt="Modifier" /></a>
					<a href= <?= 'news-delete-' . $News[ 'id' ] . '.html' ?>><img src="../images/delete.png" alt="Supprimer" /></a>
				</td>
			</tr>
			<?php
		endforeach;
		?>
	</table>

<?php
