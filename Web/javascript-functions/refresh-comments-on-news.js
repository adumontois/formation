/**
 * Created by adumontois on 14/10/2016.
 */



window.onload = function() {
	setTimeout(refresh_comments, 5000);
}


$.getScript("/javascript-functions/write-comment.js");

// Fonction pour rafraîchir les commentaires sur la page
function refresh_comments() {
	
	// Sélection du panel de commentaires
	var $this = $( "#js-comment-panel" );
	
	// Ecriture de la requête Ajax
	$.ajax( {
		url      : $this.data( 'action' ),
		type     : "POST",
		data     : {
			dateupdate : $this.data( 'last-update' )
		},
		dataType : "json",
		success  : function( json, status ) {
			console.log(json);
			
			if ( json.master_code != 0 ) {
				console.error( 'Aie aie aie je m\'arrete la car j\'ai rencontré une erreur' );
				return;
			}
			
			var Comment_a = json.content.Comment_a;
			
			for ( Comment in Comment_a ) {
				write_comment(Comment_a[Comment]);
			}
			
			// mettre à jour la date de mise à jour dans l'attribut
			$this.data('last-update', json.content.dateupdate.date);
		},
		
		//error : function(resultat, statut, erreur) {
		//	console.log(resultat);
		//}
	});
	
	// Relancer le décompte
	setTimeout(refresh_comments, 5000);
}