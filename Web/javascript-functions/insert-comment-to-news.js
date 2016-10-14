/**
 * Created by adumontois on 14/10/2016.
 */

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
			content : $this.find( '[name=content]' ).val()
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
			
			var Comment = json.content.Comment;
			
			
			// Il y a des erreurs : on n'affiche pas le commentaire.
			// Par contre on affiche les erreurs de remplissage du formulaire
			if ( null !== Comment.error_a ) {
				var bad_field;
				for ( input in Comment.error_a ) {
					bad_field = ($( "<div class = \"form_error\"></div>" ).text( Comment.error_a[ input ] ));
					$( bad_field ).insertBefore( $this.find( '[name=' + input + ']' ) );
				}
			}
			else {
				// Pas d'erreur = afficher le nouveau commentaire. On le formate en HTML.
				// On remplace les retours lignes
				
				var new_comment_header;
				// Structure de base avec le header du message
				var new_comment = $( "<fieldset class=\"js-comment\"></fieldset>" )
					.append( new_comment_header = $( "<legend></legend>" )
						.append( "Posté par ", $( "<strong></strong>" )
							.text( Comment.author ), ' le ', Comment.date ) );
				
				// Ajout des actions au header
				if ( 0 !== Comment.action_a.length ) {
					new_comment_header.append( ' - ' );
					for ( action in Comment.action_a ) {
						new_comment_header.append( $( "<a></a>" )
							.attr( "href", Comment.action_a[ action ].link )
							.text( Comment.action_a[ action ].label + ' ' ) );
					}
				}
				
				// Ajout du contenu
				// Penser à injecter des balises de retour ligne
				new_comment.append( $( "<p></p>" ).html( Comment.content.replace('\n', '<br />') ) );
				
				// Supprimer le message "pas de commentaires" si besoin
				var no_comment_message = $( "#no-comment" );
				if ( no_comment_message ) {
					// S'il n'y a pas encore de commentaire, on retire le message disant qu'il n'y a pas de commentaire.
					no_comment_message.remove();
				}
				
				// Endroit d'insertion
				// Prendre "au-dessus du premier commentaire"
				insert_location = $( ".js-comment" )[ 0 ];
				if ( typeof insert_location === 'undefined' ) {
					// S'il n'y a pas de commentaire, on insère au dessous du premier formulaire
					$( new_comment ).insertAfter( $( "#insert_comment_1" ) );
				}
				else {
					// Insérer le commentaire en top commentaire
					$( new_comment ).insertBefore( insert_location );
				}
				
				// Vider le message entré par l'utilisateur pour éviter les doublons
				$this.find( '[name=content]' ).val( '' );
			}
		}
	} );
} );