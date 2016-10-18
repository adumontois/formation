/**
 * Created by adumontois on 18/10/2016.
 */

/**
 * Ouvre un champ pour éditer le commentaire entré.
 */
$(".js-comment button[data-function=\"update_comment_on_click\"]").click( update_comment_on_click = function( event ) {
	var $this = $(this);
	event.preventDefault();
	
	// Requête Ajax
	$.ajax( {
		url      : $this.data( 'action' ),
		type     : "POST",
		dataType : "json",
		data : {
			content: $this.parents(".js-comment").find(".js-comment-content").html()
		}
		success  : function( json, status ) {
			if ( typeof json.master_code !== 'undefined' ) {
				console.error(json.master_error);
			}
			else {
				refresh_comments();
				if (typeof json.form !== 'undefined') {
					
				}
			}
		},
		
		error : function(resultat, statut, erreur) {
			console.log(resultat);
		}
	});
});