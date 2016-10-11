<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 04/10/2016
 * Time: 10:09
 */

/**
 * @var $news_count  integer
 * @var $News_list_a \Entity\News[]
 * @var $User \OCFram\User
 * @var string[] $action_a
 */
?>

<p style="text-align: center">
	Il y a actuellement <?=$news_count?> news. En voici la liste :
</p>

<table>
	<tr>
		<th>Auteur</th>
		<th>Titre</th>
		<th>Date d'ajout</th>
		<th>Dernière modification</th>
		<th>Action</th>
	</tr>
	<?php foreach ( $News_list_a as $News ): ?>
		<tr>
			<td>
				<?= htmlspecialchars( $News[ 'auteur' ] ) ?>
			</td>
			<td>
				<?= htmlspecialchars( $News[ 'titre' ] ) ?>
			</td>
			<td>
				le <?= $News[ 'DateAjout' ] ?>
			</td>
			<td>
				<?php if ( $News[ 'DateAjout' ] != $News[ 'DateModif' ] ): ?>
					le <?= $News[ 'DateModif' ] ?>
				<?php endif; ?>
			</td>
			<td>
				<?= $action_a[$News['id']] ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>