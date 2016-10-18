/**
 * Created by adumontois on 14/10/2016.
 */

$.getScript("/javascript-functions/write-comment.js");

// Fonction pour traiter l'envoi du formulaire
$( '.js-form-insert-comment' ).submit( function( event ) {
	var $this = $( this );
	
	// Empêcher l'envoi au contrôleur html
	event.preventDefault();
	
	// Ecriture de la requête Ajax
	$.ajax( {
		url      : $this.data( 'action' ),
		type     : "POST",
		data     : {
			author  : $this.find( '[name=author]' ).val(),
			content : $this.find( '[name=content]' ).val(),
		},
		dataType : "json", // et non datatype
		success  : function( json, status ) {
			// Cette fonction se déclenche dès lors que l'URL est trouvée !
			if ( json.master_code != 0 ) {
				console.error( 'Aie aie aie je m\'arrete la car j\'ai rencontré une erreur' );
				return;
			}
			
			// Suppression des anciens messages d'erreur
			$( ".form_error" ).remove();
			
			var error_a = json.content.error_a;
			
			// Il y a des erreurs : on n'affiche pas le commentaire.
			// Par contre on affiche les erreurs de remplissage du formulaire
			if ( null !== error_a ) {
				var bad_field;
				for ( error in error_a ) {
					bad_field = ($( "<div class = \"form_error\"></div>" ).text( error_a[ error ] ));
					$( bad_field ).insertBefore( $this.find( '[name=' + error + ']' ) );
				}
			}
				
			else {
				// Supprimer le champ auteur s'il existe
				$(".js-form-insert-comment label[for='author']").remove();
				$(".js-form-insert-comment input[name='author']").next().remove();
				$(".js-form-insert-comment input[name='author']").remove();
				
				
				// Supprimer le contenu du commentaire
				$(".js-form-insert-comment textarea[name='content']").val("");
			}
			
			// On rafraîchit les commentaires dans tous les cas : le nouveau commentaire s'affiche s'il est correct.
			refresh_comments();
		},
		error : function(resultat, statut, erreur) {
			console.log(resultat.responseText);
		}
	} );
} );