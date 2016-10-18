<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 13/10/2016
 * Time: 10:49
 */

/**
 * @var $News              \Entity\News News à afficher
 * @var $Comment_list_a    \Entity\Comment[] Liste des commentaires à afficher
 * @var $link_a            string[] Liste des liens à afficher
 * @var $dateupdate        string Date de dernière mise à jour des commentaires
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

<?php $form_id         = 'insert_comment_1';
$js_data_action_insert = $News[ 'action_a' ][ 0 ][ 'insert_comment_json' ]; ?>
<?php require "form/insert_comment.html.php" ?>

<?php if ( empty( $Comment_list_a ) ): ?>
	<p id="no-comment">
		Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !
	</p>
<?php endif; ?>

<?php $js_data_action_refresh = $News[ 'action_a' ][ 0 ][ 'refresh_comments_json' ]; ?>
<div id="js-comment-panel" data-action="<?= $js_data_action_refresh ?>" data-last-update="<?= $dateupdate ?>">
	<?php foreach ( $Comment_list_a as $Comment ): ?>
		<fieldset class="js-comment" data-id=<?= $Comment['id']?>>
			<legend>
				Posté par <strong class="js-author"><?= htmlspecialchars( $Comment[ 'author' ] ) ?></strong> le <?= $Comment[ 'datecreation' ] ?>
				<?php if ( $Comment[ 'datecreation' ] != $Comment[ 'dateupdate' ] ): ?>
					- <strong class="js-edit-comment">modifié le <?= $Comment[ 'dateupdate' ] ?></strong>
				<?php endif; ?>
				<?php if ( !empty( $Comment[ 'action_a' ] ) ): ?>
					-
					<?php foreach ( $Comment[ 'action_a' ] as $action_a ): ?>
						<button data-action="<?= $action_a[ 'link' ] ?>" data-function="<?= $action_a['js_function']?>"><?= $action_a[ 'label' ]  ?></button>
					<?php endforeach; ?>
				<?php endif; ?>
			</legend>
			<p class = "js-comment-content">
				<?= nl2br( htmlspecialchars( $Comment[ 'content' ] ) ) ?>
			</p>
		</fieldset>
	<?php endforeach; ?>
</div>

<?php $form_id         = 'insert_comment_2';
$js_data_action_insert = $News[ 'action_a' ][ 0 ][ 'insert_comment_json' ]; ?>
<?php require "form/insert_comment.html.php" ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="/javascript-functions/insert-comment-to-news.js"></script>
<script src="/javascript-functions/refresh-comments-on-news.js"></script>
<script src="/javascript-functions/delete-comment-on-click.js"></script>
<script src="/javascript-functions/update-comment-on-click.js"></script>
