/**
 * Created by adumontois on 14/10/2016.
 */

window.onload = function() {
	setTimeout(refresh_comments, 5000);
}


// Fonction pour rafraîchir les commentaires sur la page
function refresh_comments() {
	console.log('test');
	
	// Sélection du panel de commentaires
	var $this = $( "#js-comment-panel" );
	
	// Ecriture de la requête Ajax
	$.ajax( {
		url      : $this.data( 'action' ),
		type     : "POST",
		data     : {
			dateupdate : $this.data( 'last-update' ).val()
		},
		dataType : "json",
		success  : function( json, status ) {
			
			if ( json.master_code != 0 ) {
				console.error( 'Aie aie aie je m\'arrete la car j\'ai rencontré une erreur' );
				return;
			}
			
			var Comment_a = json.content.Comment;
			for ( Comment in Comment_a ) {
				
			}
		}
	});
	
	/* mettre à jour la date de mise à jour */
	$this.data('last-update').val(json.content.dateupdate);
	
	/* Relancer le décompte */
	setTimeout(refresh_comments, 5000);
}