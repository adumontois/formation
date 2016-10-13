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

<p>
	<a href="<?= $link_a[ 'putInsertComment' ] ?>">Ajouter un commentaire</a>
</p>

<?php if ( empty( $Comment_list_a ) ): ?>
	<p>
		Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !
	</p>
<?php endif; ?>

<?php foreach ( $Comment_list_a as $Comment ): ?>
	<fieldset>
		<legend>
			Posté par <strong><?= htmlspecialchars( $Comment[ 'author' ] ) ?></strong> le <?= $Comment[ 'date' ] ?>
			<?php if ( !empty( $Comment[ 'action_a' ] ) ): ?>
				-
				<?php foreach ( $Comment[ 'action_a' ] as $action_a ): ?>
					<a href=<?= $action_a[ 'link' ] ?>><?= $action_a[ 'label' ] ?></a>
				<?php endforeach; ?>
			<?php endif; ?>
		</legend>
		<p>
			<?= nl2br( htmlspecialchars( $Comment[ 'content' ] ) ) ?>
		</p>
	</fieldset>
<?php endforeach; ?>

<p>
	<a href="<?= $link_a[ 'putInsertComment' ] ?>">Ajouter un commentaire</a>

	
	
	
	

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>
<script type="text/javascript">
	// Test
	var comment = {
		author  : 'Mon_auteur',
		content : 'Ceci est un contenu.',
		date    : '13/10/2016 à 14h24'
	};
	
	// Transformer la variable en string JSON
	var string = JSON.stringify( comment );
	var html   = jQuery.parseJSON( string );
	
	// On a construit un objet JS à partir du JSON. Maintenant on veut afficher le nouveau commentaire
	
	node = document.createElement('fieldset');
	node2 = document.createElement('legend');
	node.appendChild(node2);
	node2.innerHTML = 'Posté par <strong>'+html['author']+'</strong> le '+html[ 'date' ];
	node3 = document.createElement('p');
	node.appendChild(node3);
	node3.textContent = html['content'];
	//console.log(document.getElementsByTagName('fieldset'));
	node = document.getElementById('main').insertBefore(node, document.getElementsByTagName('fieldset')[0]);
</script>