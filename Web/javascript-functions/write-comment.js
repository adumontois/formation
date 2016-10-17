/**
 * Created by adumontois on 17/10/2016.
 */


$.getScript("/javascript-functions/write-comment-header.js");

/**
 * Ecrit le commentaire à partir d'un commentaire donné sous format JSON.
 * Le commentaire doit être validé préalablement.
 */
function write_comment( Comment ) {
	
	// Pas d'erreur = afficher le nouveau commentaire. On le formate en HTML.
	// On remplace les retours lignes
	
	var new_comment_header;
	// Structure de base avec le header du message
	var new_comment = $( "<fieldset class=\"js-comment\" data-id=\""+Comment.id+"\"></fieldset>" )
		.append( write_comment_header(Comment));
	
	// Ajout du contenu
	// Penser à injecter des balises de retour ligne
	new_comment.append( $( "<p class=\"js-comment-content\"></p>" ).html( Comment.content.replace( '\n', '<br />' ) ) );
	
	// Supprimer le message "pas de commentaires" si besoin
	var no_comment_message = $( "#no-comment" );
	if ( no_comment_message ) {
		// S'il n'y a pas encore de commentaire, on retire le message disant qu'il n'y a pas de commentaire.
		no_comment_message.remove();
	}
	
	// Endroit d'insertion
	// Prendre en haut de la liste des commentaires
	$("#js-comment-panel").prepend (new_comment);
}