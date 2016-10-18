/**
 * Created by adumontois on 17/10/2016.
 */

/**
 * Fabrique le header du commentaire passé en paramètre
 *
 * @param Comment Commentaire à écrire (format JSON)
 */
function write_comment_header( Comment ) {
	console.log(Comment);
	new_comment_header = $( "<legend></legend>" )
		.append( "Posté par ", $( "<strong class=\"js-author\"></strong>" )
			.text( Comment.author ), ' le ', Comment.datecreation );

	if ( Comment.datecreation != Comment.dateupdate ) {
		new_comment_header.append( " - " )
				   .append( $( "<strong class='\"js-edit-comment\"'></strong>" ).text( "modifié le "+Comment.dateupdate ) );
	}

	// Ajout des actions au header
	if ( 0 !== Comment.action_a.length ) {
		new_comment_header.append( ' - ' );
		for ( action in Comment.action_a ) {
			new_comment_header.append( $( "<button data-action="+Comment.action_a[ action ].link+"></button>" )
				.text( Comment.action_a[ action ].label ) );
			new_comment_header.append(' ');
			$(new_comment_header).find("button:last").on('click', eval(Comment.action_a[action].js_function));
		}
	}
	return new_comment_header;
}