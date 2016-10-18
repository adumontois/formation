/**
 * Created by adumontois on 18/10/2016.
 */

/**
 * Supprime le commentaire en appuyant sur le bouton de suppression
 */
$(".js-comment button[data-action]").click( delete_comment_on_click = function( event ) {
	var $this = $(this);
	
	event.preventDefault();
	console.log($this);
	// Ecriture de la requête Ajax
	$.ajax( {
		url      : $this.data( 'action' ),
		type     : "POST",
		dataType : "json",
		success  : function( json, status ) {
			if ( typeof json.master_code !== 'undefined' ) {
				console.error(json.master_error);
			}
			else {
				refresh_comments();
			}
		},
		
		error : function(resultat, statut, erreur) {
			console.log(resultat);
		}
	});
});