/**
 * Created by adumontois on 18/10/2016.
 */

/**
 * Ouvre un champ pour éditer le commentaire entré.
 */
$(".js-comment button[data-function=\"update_comment_on_click\"]").click( update_comment_on_click = function( event ) {
	var $this = $(this);
	event.preventDefault();
	var contentNode = $this.parents(".js-comment").find(".js-comment-content-new");
	if (contentNode.length != 0) {
		text_content = contentNode.val();
	}
	else {
		text_content = undefined;
	}
	console.log(text_content);
	
	// Requête Ajax
	$.ajax( {
		url      : $this.data( 'action' ),
		type     : "POST",
		data : {
			content: text_content,
		},
		dataType : "json",
		success  : function( json, status ) {
			console.log(json);
			if (json.master_code != 0 ) {
				console.error(json.master_code);
			}
			else {
				// Si on ne connaît pas l'auteur (non passé en paramètre) : c'est qu'on cherche à modifier
				if (typeof json.content.form !== 'undefined') {
					$this.text('Valider la modification');
					// On affiche le formulaire en retirant les espaces inutiles
					if ($this.parents(".js-comment").find(".js-comment-content").length) {
						$this.parents(".js-comment").find(".js-comment-content").replaceWith($("<div id=\""+$this.parents(".js-comment").attr('data-id')+"\"></div>").html(json.content.form.replace(/[\s\n\r]+/g, ' ')));
					}
					else {
						$this.parents(".js-comment").find("#"+$this.parents(".js-comment").attr('data-id')).replaceWith($("<div id=\""+$this.parents(".js-comment").attr('data-id')+"\"></div>").html(json.content.form.replace(/[\s\n\r]+/g, ' ')));
					}
					// Rajouter la classe
					$this.parents(".js-comment").find("[name=\"content\"]").attr("class", "js-comment-content-new");
				}
				else {
					$this.text('Modifier');
					// Supprimer le formulaire
					$this.parents(".js-comment").find("#"+$this.parents(".js-comment").attr('data-id')).remove();
				}
				
				// Rafraîchir les commentaires
				refresh_comments();
			}
		},
		
		error : function(resultat, statut, erreur) {
			console.log(resultat.responseText);
		}
	});
});