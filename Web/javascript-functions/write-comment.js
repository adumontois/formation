/**
 * Created by adumontois on 17/10/2016.
 */

/**
 * Ecrit le commentaire à partir d'un commentaire donné sous format JSON.
 * Le commentaire doit être validé préalablement.
 */

function write_comment( Comment ) {
	
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
	new_comment.append( $( "<p></p>" ).html( Comment.content.replace( '\n', '<br />' ) ) );
	
	// Supprimer le message "pas de commentaires" si besoin
	var no_comment_message = $( "#no-comment" );
	if ( no_comment_message ) {
		// S'il n'y a pas encore de commentaire, on retire le message disant qu'il n'y a pas de commentaire.
		no_comment_message.remove();
	}
	
	// Endroit d'insertion
	// Prendre "au-dessus du premier commentaire"
	console.log($("#js-comment-panel"));
	$("#js-comment-panel").prepend (new_comment);
}