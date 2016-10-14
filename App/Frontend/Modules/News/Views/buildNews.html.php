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

<?php $form_id = 'insert_comment_1'; ?>
<?php require "form/insert_comment.html.php" ?>

<button class="insert-comment">Insérer le commentaire arbitraire</button>


<?php if ( empty( $Comment_list_a ) ): ?>
	<p id="no-comment">
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

<?php $form_id = 'insert_comment_2'; ?>
<?php require "form/insert_comment.html.php" ?>
	
<button class="insert-comment">Insérer le commentaire arbitraire</button>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>
<script type="text/javascript">
	// Fonction pour traiter l'envoi du formulaire
	$('.js-form-insert-comment').submit(function( event ) {
		var $this = $(this);
		// Empêcher l'envoi au contrôleur html
		event.preventDefault();
		
		// Ecriture de la requête Ajax
		$.ajax({
			url: "/commenter-16.json", // à générer dynamiquement
			type: "POST",
			data: {
				author :  $this.find('[name=author]').val(),
				content : $this.find('[name=content]').val()
			},
			dataType: "json",
			success: function(json, status) {
				console.log(json);
				if (json.master_code != 0) {
					console.error('Aie aie aie je m\'arrete la car j\'ai rencontré une erreur');
					return;
				}
				
				var Comment = json.content.Comment;
				
				// Parser la string JSON obtenue
//				json = JSON.parse(json);
				
				// On a construit un objet JS à partir du JSON. Maintenant on veut afficher le nouveau commentaire. On le formate en HTML
				var new_comment_header;
				var new_comment = $( "<fieldset></fieldset>" )
						.append( new_comment_header = $( "<legend></legend>" )
								.append( "Posté par ", $( "<strong></strong>" )
										.text( Comment.author ), ' le ', Comment.date ) );
				
				console.log(Comment.action_a);
				if ( 0 !== Comment.action_a.length ) {
					new_comment_header.append(' - ');
					for (action in Comment.action_a) {
						console.log(Comment.action_a[action]);
						new_comment_header.append( $( "<a></a>" ).attr( "href", Comment.action_a[action].link ).text( Comment.action_a[action].label + ' ' ) );
					}
				};
				
				new_comment.append( $( "<p></p>" ).text( Comment.content ) );
				
				// Supprimer le message "pas de commentaires"
				var no_comment_message = $( "#no-comment" );
				if ( no_comment_message ) {
					// S'il n'y a pas encore de commentaire, on retire le message disant qu'il n'y a pas de commentaire.
					no_comment_message.remove();
				}
				
				// Endroit d'insertion
				insert_location = $( "#main fieldset" )[ 0 ];
				if (typeof insert_location === 'undefined') {
					insert_location = $( "#putInsertComment" )[ 0 ];
				}
				
				console.log(insert_location);
				// Insérer le commentaire en top commentaire
				$( new_comment ).insertBefore( insert_location );
			}
		});
	});

</script>