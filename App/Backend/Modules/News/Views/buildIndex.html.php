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
		<th>User</th>
		<th>Titre</th>
		<th>Date d'ajout</th>
		<th>Dernière modification</th>
		<th class="action">Action</th>
	</tr>
	<?php foreach ( $News_list_a as $News ): ?>
		<tr>
			<td>
				<?= htmlspecialchars( $News[ 'User' ] ) ?>
			</td>
			<td>
				<?= htmlspecialchars( $News[ 'title' ] ) ?>
			</td>
			<td>
				le <?= $News[ 'dateadd' ] ?>
			</td>
			<td>
				<?php if ( $News[ 'dateadd' ] != $News[ 'dateupdate' ] ): ?>
					le <?= $News[ 'dateupdate' ] ?>
				<?php endif; ?>
			</td>
			<td class="action">
				<?php foreach ($News['action_a'] as $action_a): ?>
					<a href="<?= $action_a['action_link'] ?>"><img src="<?= $action_a['image_source'] ?>" alt="<?= $action_a['alternative_text'] ?>" /></a>
				<?php endforeach; ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>