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

<?php require "putInsertComment.html.php" ?>

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

<?php require "putInsertComment.html.php" ?>
	
<button class="insert-comment">Insérer le commentaire arbitraire</button>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>
<script type="text/javascript">
	// Fonction pour insérer un commentaire arbitraire sur la page
	// La connexion sur la DB n'est pas assurée pour l'instant
	$( function() {
		$( ".insert-comment" ).click( function() {
			// Test
			var html = {
				author  : 'Mon_auteur',
				content : 'Ceci est un contenu.',
				date    : '13/10/2016 à 14h24',
			};
			
			
			// On a construit un objet JS à partir du JSON. Maintenant on veut afficher le nouveau commentaire. On le formate en HTML
			var new_comment = $( "<fieldset></fieldset>" )
					.append( $( "<legend></legend>" )
							.append( "Posté par ", $( "<strong></strong>" )
									.text( html.author ), ' le ', html.date ) );
			new_comment.prepend( $( "<p></p>" ).text( html.content ) );
			

			
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
		} )
	} );
	
	// Fonction pour traiter l'envoi du formulaire
	$('#putInsertComment').submit(function( event ) {
		// Empêcher l'envoi au contrôleur html
		event.preventDefault();
		
		// Ecriture de la requête Ajax
		$.ajax({
			url: "/commenter-16.json", // à générer dynamiquement
			type: "POST",
			data: "author=" + $("#putInsertComment")[0].author.value + "&content=" + $("#putInsertComment")[0].content.value,
			datatype: "json",
			success: function(json, status) {
				
				console.log(json);
				
				// On a construit un objet JS à partir du JSON. Maintenant on veut afficher le nouveau commentaire. On le formate en HTML
				var new_comment = $( "<fieldset></fieldset>" )
						.append( $( "<legend></legend>" )
								.append( "Posté par ", $( "<strong></strong>" )
										.text( json.author ), ' le ', json.date ) );
				new_comment.prepend( $( "<p></p>" ).text( json.content ) );
				
				
				
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
		})
	});

</script>