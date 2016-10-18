/**
 * Created by adumontois on 17/10/2016.
 */

/**
 * Supprime le commentaire d'id donné de l'affichage
 *
 * @param id
 */
function delete_comment(id) {
	$(".js-comment[data-id="+id+"]").remove();
}