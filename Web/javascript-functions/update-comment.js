/**
 * Created by adumontois on 17/10/2016.
 */

$.getScript("/javascript-functions/write-comment-header.js");

/**
 * Met à jour un commentaire sur l'affichage
 * @param Comment Données JSON d'un commentaire
 */
function update_comment( Comment ) {
	var html_comment     = $( ".js-comment[data-id=" + Comment.id + "]" );
	var date_update_text = $( html_comment ).find( ".js-edit-comment " );
	if ( Comment.datecreation != Comment.dateupdate ) {
		if ( !date_update_text.length ) {
			// Recréer le header complet : on supprime d'abord
			html_comment.find( "legend" ).remove();
			
			// Insérer le header
			html_comment.prepend( write_comment_header(Comment) );
		}
		else {
			date_update_text.text( " modifié le " + Comment.dateupdate );
		}
	}
	html_comment.find( ".js-comment-content" ).text( Comment.content.replace( '\n', '<br />' ) );
}